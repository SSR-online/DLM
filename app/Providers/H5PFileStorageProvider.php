<?php
namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\H5PLibrary;

/**
 * Interface defining functions the h5p library needs the framework to implement
 */
class H5PFileStorageProvider implements \H5PFileStorage {
private $path, $alteditorpath;

    /**
     * The great Constructor!
     *
     * @param string $path
     *  The base location of H5P files
     * @param string $alteditorpath
     *  Optional. Use a different editor path
     */
    function __construct($path, $alteditorpath = NULL) {
        // Set H5P storage path
        $this->path = $path;
        $this->alteditorpath = $alteditorpath;
    }

    /**
     * Store the library folder.
     *
     * @param array $library
     *  Library properties
     */
    public function saveLibrary($library) {
        $dest = $this->path . '/libraries/' . \H5PCore::libraryToString($library, TRUE);
        // Make sure destination dir doesn't exist
        \H5PCore::deleteFileTree($dest);

        // Move library folder
        self::copyFileTree($library['uploadDirectory'], $dest);
    }
    
    /**
     * Convert list of file paths to csv
     *
     * @param array $librarydata
     *  Library data as found in library.json files
     * @param string $key
     *  Key that should be found in $librarydata
     * @return string
     *  file paths separated by ', '
     */
    private function pathsToCsv($librarydata, $key) {
        if (isset($librarydata[$key])) {
            $paths = array();
            foreach ($librarydata[$key] as $file) {
                $paths[] = $file['path'];
            }
            return implode(', ', $paths);
        }
        return '';
    }

    /**
     * Store the content folder.
     *
     * @param string $source
     *  Path on file system to content directory.
     * @param array $content
     *  Content properties
     */
    public function saveContent($source, $content) {
        $dest = "{$this->path}/content/{$content['id']}";

        // Remove any old content
        \H5PCore::deleteFileTree($dest);

        self::copyFileTree($source, $dest);
    }

    /**
     * Remove content folder.
     *
     * @param array $content
     *  Content properties
     */
    public function deleteContent($content) {
        \H5PCore::deleteFileTree("{$this->path}/content/{$content['id']}");
    }

    /**
     * Creates a stored copy of the content folder.
     *
     * @param string $id
     *  Identifier of content to clone.
     * @param int $newId
     *  The cloned content's identifier
     */
    public function cloneContent($id, $newId) {
        $path = $this->path . '/content/';
        if (Storage::disk('local')->exists($path . $id)) {
            self::copyFileTree($path . $id, $path . $newId);
        }
    }

    /**
     * Get path to a new unique tmp folder.
     *
     * @return string
     *  Path
     */
    public function getTmpPath() {
        $temp = "{$this->path}/temp";
        self::dirReady($temp);
        return "{$temp}/" . uniqid('h5p-');
    }

    /**
     * Fetch content folder and save in target directory.
     *
     * @param int $id
     *  Content identifier
     * @param string $target
     *  Where the content folder will be saved
     */
    public function exportContent($id, $target) {
        $source = "{$this->path}/content/{$id}";
        if (Storage::disk('local')->exists($source)) {
            // Copy content folder if it exists
            self::copyFileTree($source, $target);
        }
        else {
            // No contnet folder, create emty dir for content.json
            self::dirReady($target);
        }
    }

    /**
     * Fetch library folder and save in target directory.
     *
     * @param array $library
     *  Library properties
     * @param string $target
     *  Where the library folder will be saved
     * @param string $developmentPath
     *  Folder that library resides in
     */
    public function exportLibrary($library, $target, $developmentPath=NULL) {
        $folder = \H5PCore::libraryToString($library, TRUE);
        $srcPath = ($developmentPath === NULL ? "/libraries/{$folder}" : $developmentPath);
        self::copyFileTree("{$this->path}{$srcPath}", "{$target}/{$folder}");
    }

    /**
     * Save export in file system
     *
     * @param string $source
     *  Path on file system to temporary export file.
     * @param string $filename
     *  Name of export file.
     * @throws Exception Unable to save the file
     */
    public function saveExport($source, $filename) {
        $this->deleteExport($filename);

        if (!self::dirReady("{$this->path}/exports")) {
            throw new Exception("Unable to create directory for H5P export file.");
        }

        if (!Storage::disk('local')->copy($source, "{$this->path}/exports/{$filename}")) {
            throw new Exception("Unable to save H5P export file.");
        }
    }

    /**
     * Removes given export file
     *
     * @param string $filename
     */
    public function deleteExport($filename) {
        $target = "{$this->path}/exports/{$filename}";
        if (Storage::disk('local')->exists($target)) {
            unlink($target);
        }
    }

    /**
     * Check if the given export file exists
     *
     * @param string $filename
     * @return boolean
     */
    public function hasExport($filename) {
        $target = "{$this->path}/exports/{$filename}";
        return Storage::disk('local')->exists($target);
    }

    /**
     * Will concatenate all JavaScrips and Stylesheets into two files in order
     * to improve page performance.
     *
     * @param array $files
     *  A set of all the assets required for content to display
     * @param string $key
     *  Hashed key for cached asset
     */
    public function cacheAssets(&$files, $key) {
        foreach ($files as $type => $assets) {
            if (empty($assets)) {
                continue; // Skip no assets
            }

            $content = '';
            foreach ($assets as $asset) {
                // Get content from asset file
                $assetContent = $storage::get($this->path . $asset->path);
                $cssRelPath = preg_replace('/[^\/]+$/', '', $asset->path);

                // Get file content and concatenate
                if ($type === 'scripts') {
                    $content .= $assetContent . ";\n";
                }
                else {
                    // Rewrite relative URLs used inside stylesheets
                    $content .= preg_replace_callback(
                            '/url\([\'"]?([^"\')]+)[\'"]?\)/i',
                            function ($matches) use ($cssRelPath) {
                                    if (preg_match("/^(data:|([a-z0-9]+:)?\/)/i", $matches[1]) === 1) {
                                        return $matches[0]; // Not relative, skip
                                    }
                                    return 'url("../' . $cssRelPath . $matches[1] . '")';
                            },
                            $assetContent) . "\n";
                }
            }

            self::dirReady("{$this->path}/cachedassets");
            $ext = ($type === 'scripts' ? 'js' : 'css');
            $outputfile = "/cachedassets/{$key}.{$ext}";
            Storage::disk('local')->put($this->path . $outputfile, $content);
            $files[$type] = array((object) array(
                'path' => $outputfile,
                'version' => ''
            ));
        }
    }

    /**
     * Will check if there are cache assets available for content.
     *
     * @param string $key
     *  Hashed key for cached asset
     * @return array
     */
    public function getCachedAssets($key) {
        $files = array();
        $js = "/cachedassets/{$key}.js";
        if (Storage::disk('local')->exists($this->path . $js)) {
            $files['scripts'] = array((object) array(
                'path' => $js,
                'version' => ''
            ));
        }

        $css = "/cachedassets/{$key}.css";
        if (Storage::disk('local')->exists($this->path . $css)) {
            $files['styles'] = array((object) array(
                'path' => $css,
                'version' => ''
            ));
        }

        return empty($files) ? NULL : $files;
    }

    /**
     * Remove the aggregated cache files.
     *
     * @param array $keys
     *   The hash keys of removed files
     */
    public function deleteCachedAssets($keys) {
        foreach ($keys as $hash) {
            foreach (array('js', 'css') as $ext) {
                $path = "{$this->path}/cachedassets/{$hash}.{$ext}";
                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->delete($path);
                }
            }
        }
    }

    /**
     * Read file content of given file and then return it.
     *
     * @param string $file_path
     * @return string
     */
    public function getContent($file_path) {
        return Storage::disk('local')->get($file_path);
    }

    /**
     * Save files uploaded through the editor.
     * The files must be marked as temporary until the content form is saved.
     *
     * @param \H5peditorFile $file
     * @param int $contentid
     */
    public function saveFile($file, $contentId) {
        // Prepare directory
        if (empty($contentId)) {
            // Should be in editor tmp folder
            $path = $this->getEditorPath();
        }
        else {
            // Should be in content folder
            $path = $this->path . '/content/' . $contentId;
        }
        $path .= '/' . $file->getType() . 's';
        self::dirReady($path);

        // Add filename to path
        $path .= '/' . $file->getName();

        $fileData = $file->getData();
        if ($fileData) {
            Storage::disk('local')->put($path, $fileData);
        }
        else {
            Storage::disk('local')->copy($_FILES['file']['tmp_name'], $path);
        }

        return $file;
    }

    /**
     * Copy a file from another content or editor tmp dir.
     * Used when copy pasting content in H5P Editor.
     *
     * @param string $file path + name
     * @param string|int $fromid Content ID or 'editor' string
     * @param int $toid Target Content ID
     */
    public function cloneContentFile($file, $fromId, $toId) {
        // Determine source path
        if ($fromId === 'editor') {
            $sourcepath = $this->getEditorPath();
        }
        else {
            $sourcepath = "{$this->path}/content/{$fromId}";
        }
        $sourcepath .= '/' . $file;

        // Determine target path
        $filename = basename($file);
        $filedir = str_replace($filename, '', $file);
        $targetpath = "{$this->path}/content/{$toId}/{$filedir}";

        // Make sure it's ready
        self::dirReady($targetpath);

        $targetpath .= $filename;

        // Check to see if source exist and if target doesn't
        if (!Storage::disk('local')->exists($sourcepath) || Storage::disk('local')->exists($targetpath)) {
            return; // Nothing to copy from or target already exists
        }

        Storage::disk('local')->copy($sourcepath, $targetpath);
    }

    /**
     * Copy a content from one directory to another. Defaults to cloning
     * content from the current temporary upload folder to the editor path.
     *
     * @param string $source path to source directory
     * @param string $contentId Id of content
     *
     * @return object Object containing h5p json and content json data
     */
    public function moveContentDirectory($source, $contentId = NULL) {
        if ($source === NULL) {
            return NULL;
        }

        if ($contentId === NULL || $contentId == 0) {
            $target = $this->getEditorPath();
        }
        else {
            // Use content folder
            $target = "{$this->path}/content/{$contentId}";
        }

        $contentSource = $source . DIRECTORY_SEPARATOR . 'content';

        $files  = Storage::disk('local')->files($contentSource);
        $directories = Storage::disk('local')->directories($contentSource);

        foreach ($files as $file) {
            if($file != 'content.json') {
                Storage::disk('local')->copy("{$contentSource}/{$file}", "{$target}/{$file}");
            }
        }
        foreach ($directories as $directory) {
            self::copyFileTree("{$contentSource}/{$directory}", "{$target}/{$directory}");
        }

        // Successfully loaded content json of file into editor
        $h5pJson = $this->getContent($source . DIRECTORY_SEPARATOR . 'h5p.json');
        $contentJson = $this->getContent($contentSource . DIRECTORY_SEPARATOR . 'content.json');

        return (object) array(
            'h5pJson' => $h5pJson,
            'contentJson' => $contentJson
        );
    }

    /**
     * Checks to see if content has the given file.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param int $contentId
     * @return string File ID or NULL if not found
     */
    public function getContentFile($file, $contentId) {
        $path = "{$this->path}/content/{$contentId}/{$file}";
        return Storage::disk('local')->exists($path) ? $path : NULL;
    }

    /**
     * Checks to see if content has the given file.
     * Used when saving content.
     *
     * @param string $file path + name
     * @param int $contentid
     * @return string|int File ID or NULL if not found
     */
    public function removeContentFile($file, $contentId) {
        $path = "{$this->path}/content/{$contentId}/{$file}";
        if (Storage::disk('local')->exists($path)) {
            unlink($path);

            // Clean up any empty parent directories to avoid cluttering the file system
            $parts = explode('/', $path);
            while (array_pop($parts) !== NULL) {
                $dir = implode('/', $parts);
                if (is_dir($dir) && count(scandir($dir)) === 2) { // empty contains '.' and '..'
                    rmdir($dir); // Remove empty parent
                }
                else {
                    return; // Not empty
                }
            }
        }
    }

    /**
     * Check if server setup has write permission to
     * the required folders
     *
     * @return bool True if site can write to the H5P files folder
     */
    public function hasWriteAccess() {
        return self::dirReady($this->path);
    }

    /**
     * Recursive function for copying directories.
     *
     * @param string $source
     *  From path
     * @param string $destination
     *  To path
     * @return boolean
     *  Indicates if the directory existed.
     *
     * @throws Exception Unable to copy the file
     */
    private static function copyFileTree($source, $destination) {
        if (!self::dirReady($destination)) {
            throw new \Exception('unabletocopy');
        }

        $ignoredFiles = self::getIgnoredFiles("{$source}/.h5pignore");
        $files = File::files($source);
        $directories = File::directories($source);

        foreach($files as $file) {
            $filename = substr($file, strlen($source));

            File::copy("{$source}{$filename}", "{$destination}{$filename}");
        }

        foreach($directories as $directory) {
            $directory = substr($directory, strlen($source));
            self::copyFileTree("{$source}{$directory}", "{$destination}{$directory}");
        }
    }

    /**
     * Retrieve array of file names from file.
     *
     * @param string $file
     * @return array Array with files that should be ignored
     */
    private static function getIgnoredFiles($file) {
        if (Storage::disk('local')->exists($file) === FALSE) {
            return array();
        }

        $contents = Storage::disk('local')->get($file);
        if ($contents === FALSE) {
            return array();
        }

        return preg_split('/\s+/', $contents);
    }

    /**
     * Recursive function that makes sure the specified directory exists and
     * is writable.
     *
     * @param string $path
     * @return bool
     */
    private static function dirReady($path) {
        $prefix = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = substr($path, strlen($prefix));
        if (!empty($path) &! Storage::disk('local')->exists($path)) {
            $parent = preg_replace("/\/[^\/]+\/?$/", '', $path);
            if($parent == $path) {
                $parent =  $prefix;
            } else {
                $parent =  $prefix . $parent;
            }
            Log::info('parent: ' . $parent);
            if (!self::dirReady($parent)) {
                return FALSE;
            }
            Log::info('making directory for ' . $path);
            if(!Storage::disk('local')->makeDirectory($path)) {
                trigger_error('Path is not a directory ' . $path, E_USER_WARNING);
            }
        }
        Log::info('returning true for ' . $path);
        return TRUE;
    }

    /**
     * Easy helper function for retrieving the editor path
     *
     * @return string Path to editor files
     */
    private function getEditorPath() {
        return ($this->alteditorpath !== NULL ? $this->alteditorpath : "{$this->path}/editor");
    }
}