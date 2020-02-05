<?php
namespace App\Providers;

use App\H5PLibrary;
use App\H5PBlock;

/**
 * Interface defining functions the h5p library needs the framework to implement
 */
class H5PProvider implements \H5PFrameworkInterface {

    public $uploadedFolderPath;
    public $uploadedPath;
    private $block;

    private $messages = [
        'info' => [],
        'error' => []
    ];


    public function __construct($block_id) {
        $this->block = H5PBlock::findOrFail($block_id);
    }

    /**
    * Returns info for the current platform
    *
    * @return array
    *   An associative array containing:
    *   - name: The name of the platform, for instance "Wordpress"
    *   - version: The version of the platform, for instance "4.0"
    *   - h5pVersion: The version of the H5P plugin/module
    */
    public function getPlatformInfo() {
    return [
        'name' => 'DLM',
        'version' => '0.1.0',
        'h5pVersion' => '0.1.0',
    ];
    }


    /**
    * Fetches a file from a remote server using HTTP GET
    *
    * @param string $url Where you want to get or send data.
    * @param array $data Data to post to the URL.
    * @param bool $blocking Set to 'FALSE' to instantly time out (fire and forget).
    * @param string $stream Path to where the file should be saved.
    * @return string The content (response body). NULL if something went wrong
    */
    public function fetchExternalData($url, $data = NULL, $blocking = TRUE, $stream = NULL) {
        //TODO: Implement
        return null;
    }

    /**
    * Set the tutorial URL for a library. All versions of the library is set
    *
    * @param string $machineName
    * @param string $tutorialUrl
    */
    public function setLibraryTutorialUrl($machineName, $tutorialUrl) {
        //TODO: Implement
    }

    /**
    * Show the user an error message
    *
    * @param string $message
    *   The error message
    */
    public function setErrorMessage($message, $code = null) {
        $this->messages['errors'][] = $message;
    }

    public function getMessages($type) {
        return;
    }
    /**
    * Show the user an information message
    *
    * @param string $message
    *  The error message
    */
    public function setInfoMessage($message) {
        $this->messages['info'][] = $message;
    }

    public function displayMessages() {
        if(count($this->messages['info'])>0) {
            echo '<h3>Info</h3>';
            echo implode('<br />', $this->messages['info']);
        }
        if(count($this->messages['errors'])>0) {
            echo '<h3>Errors</h3>';
            echo implode('<br />', $this->messages['errors']);
        }
    }

    /**
    * Translation function
    *
    * @param string $message
    *  The english string to be translated.
    * @param array $replacements
    *   An associative array of replacements to make after translation. Incidences
    *   of any key in this array are replaced with the corresponding value. Based
    *   on the first character of the key, the value is escaped and/or themed:
    *    - !variable: inserted as is
    *    - @variable: escape plain text to HTML
    *    - %variable: escape text and theme as a placeholder for user-submitted
    *      content
    * @return string Translated string
    * Translated string
    */
    public function t($message, $replacements = array()) {
        return $message; //TODO Actually translate
    }

    /**
    * Get URL to file in the specific library
    * @param string $libraryFolderName
    * @param string $fileName
    * @return string URL to file
    */
    public function getLibraryFileUrl($libraryFolderName, $fileName) {
        return '/uploads/h5p/'.$libraryFolderName.'/'.$fileName;
    }

    /**
    * Get the Path to the last uploaded h5p
    *
    * @return string
    *   Path to the folder where the last uploaded h5p for this session is located.
    */
    public function getUploadedH5pFolderPath() {
        return $this->uploadedFolderPath;
    }

    /**
    * Get the path to the last uploaded h5p file
    *
    * @return string
    *   Path to the last uploaded h5p
    */
    public function getUploadedH5pPath() {
        return $this->uploadedPath;
    }

    /**
    * Get a list of the current installed libraries
    *
    * @return array
    *   Associative array containing one entry per machine name.
    *   For each machineName there is a list of libraries(with different versions)
    */
    public function loadLibraries() {
        $libraries = H5PLibrary::all();
        $libs = [];
        foreach($libraries as $library) {
            $libs[$library->machine_name] = $library;
        }
        return $libs;
    }

    /**
    * Returns the URL to the library admin page
    *
    * @return string
    *   URL to admin page
    */
    public function getAdminUrl() {
        //TODO implement
    }

    /**
    * Get id to an existing library.
    * If version number is not specified, the newest version will be returned.
    *
    * @param string $machineName
    *   The librarys machine name
    * @param int $majorVersion
    *   Optional major version number for library
    * @param int $minorVersion
    *   Optional minor version number for library
    * @return int
    *   The id of the specified library or FALSE
    */
    public function getLibraryId($machineName, $majorVersion = NULL, $minorVersion = NULL) {
        $query = H5PLibrary::where('machine_name', $machineName);
        if($majorVersion!=null) {
            $query->where('major_version', $majorVersion);
        }
        if($minorVersion!=null) {
            $query->where('minor_version', $minorVersion);
        }
        $library = $query->first();
        return (!is_null($library)) ? $library->id : false;
    }

    /**
    * Get file extension whitelist
    *
    * The default extension list is part of h5p, but admins should be allowed to modify it
    *
    * @param boolean $isLibrary
    *   TRUE if this is the whitelist for a library. FALSE if it is the whitelist
    *   for the content folder we are getting
    * @param string $defaultContentWhitelist
    *   A string of file extensions separated by whitespace
    * @param string $defaultLibraryWhitelist
    *   A string of file extensions separated by whitespace
    */
    public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist) {
        return $defaultContentWhitelist . ($isLibrary ? ' ' . $defaultLibraryWhitelist : '');
    }

    /**
    * Is the library a patched version of an existing library?
    *
    * @param object $library
    *   An associative array containing:
    *   - machineName: The library machineName
    *   - majorVersion: The librarys majorVersion
    *   - minorVersion: The librarys minorVersion
    *   - patchVersion: The librarys patchVersion
    * @return boolean
    *   TRUE if the library is a patched version of an existing library
    *   FALSE otherwise
    */
    public function isPatchedLibrary($library) {
        return true; //TODO implement
    }

    /**
    * Is H5P in development mode?
    *
    * @return boolean
    *  TRUE if H5P development mode is active
    *  FALSE otherwise
    */
    public function isInDevMode() {
        return false;
    }

    /**
    * Is the current user allowed to update libraries?
    *
    * @return boolean
    *  TRUE if the user is allowed to update libraries
    *  FALSE if the user is not allowed to update libraries
    */
    public function mayUpdateLibraries() {
        return true;
    }

    /**
    * Store data about a library
    *
    * Also fills in the libraryId in the libraryData object if the object is new
    *
    * @param object $libraryData
    *   Associative array containing:
    *   - libraryId: The id of the library if it is an existing library.
    *   - title: The library's name
    *   - machineName: The library machineName
    *   - majorVersion: The library's majorVersion
    *   - minorVersion: The library's minorVersion
    *   - patchVersion: The library's patchVersion
    *   - runnable: 1 if the library is a content type, 0 otherwise
    *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
    *   - embedTypes(optional): list of supported embed types
    *   - preloadedJs(optional): list of associative arrays containing:
    *     - path: path to a js file relative to the library root folder
    *   - preloadedCss(optional): list of associative arrays containing:
    *     - path: path to css file relative to the library root folder
    *   - dropLibraryCss(optional): list of associative arrays containing:
    *     - machineName: machine name for the librarys that are to drop their css
    *   - semantics(optional): Json describing the content structure for the library
    *   - language(optional): associative array containing:
    *     - languageCode: Translation in json format
    * @param bool $new
    * @return
    */
    public function saveLibraryData(&$libraryData, $new = TRUE) {
        $embedtypes = '';
        if (isset($libraryData['embedTypes'])) {
            $embedtypes = implode(', ', $libraryData['embedTypes']);
        }
        if (!isset($libraryData['semantics'])) {
            $libraryData['semantics'] = '';
        }
        if($new) {
            $library = new H5PLibrary();
        } else {
            $library = H5PLibrary::findorFail($libraryData['libraryId']);
        }
        $preloadedjs = $this->pathsToCsv($libraryData, 'preloadedJs');
        $preloadedcss = $this->pathsToCsv($libraryData, 'preloadedCss');

        $library->title = $libraryData['title'];
        $library->machine_name = $libraryData['machineName'];
        $library->major_version = $libraryData['majorVersion'];
        $library->minor_version = $libraryData['minorVersion'];
        $library->patch_version = $libraryData['patchVersion'];
        $library->runnable = $libraryData['runnable'];
        $library->fullscreen = isset($libraryData['fullscreen']);
        $library->embed_types = $embedtypes;
        $library->preloaded_js = $preloadedjs;
        $library->preloaded_css = $preloadedcss;
        $library->drop_library_css = '';
        $library->semantics = $libraryData['semantics'];
        $library->has_icon = isset($libraryData['hasIcon']);
        $library->save();

        if($new) {
            $libraryData['libraryId'] = $library->id;
        }
        if(isset($libraryData['libraryId'])) {
            $this->deleteLibraryDependencies($libraryData['libraryId']);
        }
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
    * Insert new content.
    *
    * @param array $content
    *   An associative array containing:
    *   - id: The content id
    *   - params: The content in json format
    *   - library: An associative array containing:
    *     - libraryId: The id of the main library for this content
    * @param int $contentMainId
    *   Main id for the content if this is a system that supports versions
    */
    public function insertContent($content, $contentMainId = NULL) {
        return $this->updateContent($content, $contentMainId);
    }

    /**
    * Update old content.
    *
    * @param array $content
    *   An associative array containing:
    *   - id: The content id
    *   - params: The content in json format
    *   - library: An associative array containing:
    *     - libraryId: The id of the main library for this content
    * @param int $contentMainId
    *   Main id for the content if this is a system that supports versions
    */
    public function updateContent($content, $contentMainId = NULL) {
        if(array_key_exists('id', $content) && $content['id']!=null) {
            $block = H5PBlock::find($content['id']);
        } else {
            $block = $this->block;
        }
        $block->json_content = $content['params'];
        $block->embed_type = 'div';
        $block->main_library_id = $content['library']['libraryId'];
        $block->slug = ''; //TODO: remove\
        $block->disable = false;
        $block->save();
        return $block->id;
    }

    /**
    * Resets marked user data for the given content.
    *
    * @param int $contentId
    */
    public function resetContentUserData($contentId) {
        //TODO implement
    }

    /**
    * Save what libraries a library is depending on
    *
    * @param int $libraryId
    *   Library Id for the library we're saving dependencies for
    * @param array $dependencies
    *   List of dependencies as associative arrays containing:
    *   - machineName: The library machineName
    *   - majorVersion: The library's majorVersion
    *   - minorVersion: The library's minorVersion
    * @param string $dependency_type
    *   What type of dependency this is, the following values are allowed:
    *   - editor
    *   - preloaded
    *   - dynamic
    */
    public function saveLibraryDependencies($libraryId, $dependencies, $dependency_type) {
        $library = H5PLibrary::find($libraryId);
        foreach($dependencies as $dependency) {
            $required_library = H5PLibrary::where(
            [
                'machine_name' => $dependency['machineName'],
                'major_version' => $dependency['majorVersion'],
                'minor_version' => $dependency['minorVersion']
            ])->first();

            $library->required_libraries()->attach($required_library, [
                'dependency_type' => $dependency_type,
            ]);
            $library->save();
        }
    }

    /**
    * Give an H5P the same library dependencies as a given H5P
    *
    * @param int $contentId
    *   Id identifying the content
    * @param int $copyFromId
    *   Id identifying the content to be copied
    * @param int $contentMainId
    *   Main id for the content, typically used in frameworks
    *   That supports versions. (In this case the content id will typically be
    *   the version id, and the contentMainId will be the frameworks content id
    */
    public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = NULL) {
        //TODO implement
    }

    /**
    * Deletes content data
    *
    * @param int $contentId
    *   Id identifying the content
    */
    public function deleteContentData($contentId) {
        //TODO implement
    }

    /**
    * Delete what libraries a content item is using
    *
    * @param int $contentId
    *   Content Id of the content we'll be deleting library usage for
    */
    public function deleteLibraryUsage($contentId) {
        $block = H5PBlock::find($contentId);
        $block->libraries()->detach();
        $block->save();
    }

    /**
    * Saves what libraries the content uses
    *
    * @param int $contentId
    *   Id identifying the content
    * @param array $librariesInUse
    *   List of libraries the content uses. Libraries consist of associative arrays with:
    *   - library: Associative array containing:
    *     - dropLibraryCss(optional): comma separated list of machineNames
    *     - machineName: Machine name for the library
    *     - libraryId: Id of the library
    *   - type: The dependency type. Allowed values:
    *     - editor
    *     - dynamic
    *     - preloaded
    */
    public function saveLibraryUsage($contentId, $librariesInUse) {
        $block = H5PBlock::find($contentId);
        foreach($librariesInUse as $libraryData) {
            $library = H5PLibrary::find($libraryData['library']['libraryId']);
            $drop_css = (array_key_exists('dropLibraryCss', $libraryData['library'])) ? $libraryData['library']['dropLibraryCss'] : false;
            $type = $libraryData['type'];
            $block->libraries()->attach($library, [
                'dependency_type' => $type,
                'drop_css' => $drop_css,
                'weight' => $libraryData['weight']
            ]);
            $block->save();
        }
    }

    /**
    * Get number of content/nodes using a library, and the number of
    * dependencies to other libraries
    *
    * @param int $libraryId
    *   Library identifier
    * @param boolean $skipContent
    *   Flag to indicate if content usage should be skipped
    * @return array
    *   Associative array containing:
    *   - content: Number of content using the library
    *   - libraries: Number of libraries depending on the library
    */
    public function getLibraryUsage($libraryId, $skipContent = FALSE) {
        //TODO implement
    }

    /**
    * Loads a library
    *
    * @param string $machineName
    *   The library's machine name
    * @param int $majorVersion
    *   The library's major version
    * @param int $minorVersion
    *   The library's minor version
    * @return array|FALSE
    *   FALSE if the library does not exist.
    *   Otherwise an associative array containing:
    *   - libraryId: The id of the library if it is an existing library.
    *   - title: The library's name
    *   - machineName: The library machineName
    *   - majorVersion: The library's majorVersion
    *   - minorVersion: The library's minorVersion
    *   - patchVersion: The library's patchVersion
    *   - runnable: 1 if the library is a content type, 0 otherwise
    *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
    *   - embedTypes(optional): list of supported embed types
    *   - preloadedJs(optional): comma separated string with js file paths
    *   - preloadedCss(optional): comma separated sting with css file paths
    *   - dropLibraryCss(optional): list of associative arrays containing:
    *     - machineName: machine name for the librarys that are to drop their css
    *   - semantics(optional): Json describing the content structure for the library
    *   - preloadedDependencies(optional): list of associative arrays containing:
    *     - machineName: Machine name for a library this library is depending on
    *     - majorVersion: Major version for a library this library is depending on
    *     - minorVersion: Minor for a library this library is depending on
    *   - dynamicDependencies(optional): list of associative arrays containing:
    *     - machineName: Machine name for a library this library is depending on
    *     - majorVersion: Major version for a library this library is depending on
    *     - minorVersion: Minor for a library this library is depending on
    *   - editorDependencies(optional): list of associative arrays containing:
    *     - machineName: Machine name for a library this library is depending on
    *     - majorVersion: Major version for a library this library is depending on
    *     - minorVersion: Minor for a library this library is depending on
    */
    public function loadLibrary($machineName, $majorVersion, $minorVersion) {
        $library = H5PLibrary::where(
            [
                'machine_name' => $machineName,
                'major_version' => $majorVersion,
                'minor_version' => $minorVersion
            ]
        )->first();
        $lib = [
            'libraryId' => $library->id,
            'machineName' => $library->machine_name,
            'title' => $library->title,
            'majorVersion' => $library->major_version,
            'minorVersion' => $library->minor_version,
            'patchVersion' => $library->patch_version,
            'embedTypes' => $library->embed_types,
            'fullscreen' => $library->fullscreen,
            'runnable' => $library->runnable,
            'semantics' => $library->semantics,
            'restricted' => $library->restricted,
            'hasIcon' => $library->has_icon
        ];
        $dependencies = $library->required_libraries;

        foreach ($dependencies as $dependency) {
            $lib[$dependency->pivot->dependency_type . 'Dependencies'][] = array(
                'machineName' => $dependency->machine_name,
                'majorVersion' => $dependency->major_version,
                'minorVersion' => $dependency->minor_version
            );
        }
        return $lib;
    }

    /**
    * Loads library semantics.
    *
    * @param string $machineName
    *   Machine name for the library
    * @param int $majorVersion
    *   The library's major version
    * @param int $minorVersion
    *   The library's minor version
    * @return string
    *   The library's semantics as json
    */
    public function loadLibrarySemantics($machineName, $majorVersion, $minorVersion) {
         $library = H5PLibrary::where(
            [
                'machine_name' => $machineName,
                'major_version' => $majorVersion,
                'minor_version' => $minorVersion
            ]
        )->first();
        if($library) {
            return $library->semantics;
        }
    }

    /**
    * Makes it possible to alter the semantics, adding custom fields, etc.
    *
    * @param array $semantics
    *   Associative array representing the semantics
    * @param string $machineName
    *   The library's machine name
    * @param int $majorVersion
    *   The library's major version
    * @param int $minorVersion
    *   The library's minor version
    */
    public function alterLibrarySemantics(&$semantics, $machineName, $majorVersion, $minorVersion) {
        //TODO implement
    }

    /**
    * Delete all dependencies belonging to given library
    *
    * @param int $libraryId
    *   Library identifier
    */
    public function deleteLibraryDependencies($libraryId) {
        //TODO implement
    }

    /**
    * Start an atomic operation against the dependency storage
    */
    public function lockDependencyStorage() {
        //TODO implement
    }

    /**
    * Stops an atomic operation against the dependency storage
    */
    public function unlockDependencyStorage() {
        //TODO implement
    }


    /**
    * Delete a library from database and file system
    *
    * @param stdClass $library
    *   Library object with id, name, major version and minor version.
    */
    public function deleteLibrary($library) {
        //TODO implement
    }

    /**
    * Load content.
    *
    * @param int $id
    *   Content identifier
    * @return array
    *   Associative array containing:
    *   - contentId: Identifier for the content
    *   - params: json content as string
    *   - embedType: csv of embed types
    *   - title: The contents title
    *   - language: Language code for the content
    *   - libraryId: Id for the main library
    *   - libraryName: The library machine name
    *   - libraryMajorVersion: The library's majorVersion
    *   - libraryMinorVersion: The library's minorVersion
    *   - libraryEmbedTypes: CSV of the main library's embed types
    *   - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise.
    */
    public function loadContent($id) {
        $block = H5PBlock::find($id);
        $library = $block->mainLibrary;

        // Some databases do not support camelCase, so we need to manually
        // map the values to the camelCase names used by the H5P core.
        $content = array(
            'id' => $block->id,
            'title' => 'test', //TODO fix
            'intro' => 'test intro', //TODO fix
            'params' => $block->json_content,
            'filtered' => $block->filtered,
            'slug' => $block->slug,
            'embedType' => $block->embed_type,
            'disable' => $block->disable,
            'libraryId' => $library->id,
            'libraryName' => $library->machine_name,
            'libraryMajorVersion' => $library->major_version,
            'libraryMinorVersion' => $library->minor_version,
            'libraryEmbedTypes' => $library->embed_types,
            'libraryFullscreen' => $library->fullscreen
        );

        return $content;
    }

    /**
    * Load dependencies for the given content of the given type.
    *
    * @param int $id
    *   Content identifier
    * @param int $type
    *   Dependency types. Allowed values:
    *   - editor
    *   - preloaded
    *   - dynamic
    * @return array
    *   List of associative arrays containing:
    *   - libraryId: The id of the library if it is an existing library.
    *   - machineName: The library machineName
    *   - majorVersion: The library's majorVersion
    *   - minorVersion: The library's minorVersion
    *   - patchVersion: The library's patchVersion
    *   - preloadedJs(optional): comma separated string with js file paths
    *   - preloadedCss(optional): comma separated sting with css file paths
    *   - dropCss(optional): csv of machine names
    */
    public function loadContentDependencies($id, $type = NULL) {
        $block = H5PBlock::find($id);
        $dependencies = [];
        foreach($block->libraries as $dependency) {
            $dependencies[$dependency->machine_name] = [
                'libraryId' => $dependency->id,
                'machineName' => $dependency->machine_name,
                'majorVersion' => $dependency->major_version,
                'minorVersion' => $dependency->minor_version,
                'patchVersion' => $dependency->patch_version,
                'preloadedJs' => $dependency->preloaded_js,
                'preloadedCss' => $dependency->preloaded_css,
                'dropCSs' => ''
            ];
        }
        return $dependencies;
    }

    /**
    * Get stored setting.
    *
    * @param string $name
    *   Identifier for the setting
    * @param string $default
    *   Optional default value if settings is not set
    * @return mixed
    *   Whatever has been stored as the setting
    */
    public function getOption($name, $default = NULL) {
        //TODO implement
    }

    /**
    * Stores the given setting.
    * For example when did we last check h5p.org for updates to our libraries.
    *
    * @param string $name
    *   Identifier for the setting
    * @param mixed $value Data
    *   Whatever we want to store as the setting
    */
    public function setOption($name, $value) {
        //TODO implement
    }

    /**
    * This will update selected fields on the given content.
    *
    * @param int $id Content identifier
    * @param array $fields Content fields, e.g. filtered or slug.
    */
    public function updateContentFields($id, $fields) {
        $block = H5PBlock::find($id);
        foreach($fields as $name => $value) {
            $block->$name = $value;
        }
        $block->save();
    }

    /**
    * Will clear filtered params for all the content that uses the specified
    * library. This means that the content dependencies will have to be rebuilt,
    * and the parameters re-filtered.
    *
    * @param int $library_id
    */
    public function clearFilteredParameters($library_id) {
        $blocks = H5PBlock::where('main_library_id', $library_id)->get();
        foreach($blocks as $block) {
            $block->filtered = null;
            $block->save();
        }
    }

    /**
    * Get number of contents that has to get their content dependencies rebuilt
    * and parameters re-filtered.
    *
    * @return int
    */
    public function getNumNotFiltered() {
        //TODO implement
    }

    /**
    * Get number of contents using library as main library.
    *
    * @param int $libraryId
    * @return int
    */
    public function getNumContent($libraryId) {
        //TODO implement
    }

    /**
    * Determines if content slug is used.
    *
    * @param string $slug
    * @return boolean
    */
    public function isContentSlugAvailable($slug) {
        $block = H5PBlock::where('slug', $slug)->first();
        if($block) { return false; }
        return true;
    }

    /**
    * Generates statistics from the event log per library
    *
    * @param string $type Type of event to generate stats for
    * @return array Number values indexed by library name and version
    */
    public function getLibraryStats($type) {
        //TODO implement
    }

    /**
    * Aggregate the current number of H5P authors
    * @return int
    */
    public function getNumAuthors() {
        //TODO implement
    }

    /**
    * Stores hash keys for cached assets, aggregated JavaScripts and
    * stylesheets, and connects it to libraries so that we know which cache file
    * to delete when a library is updated.
    *
    * @param string $key
    *  Hash key for the given libraries
    * @param array $libraries
    *  List of dependencies(libraries) used to create the key
    */
    public function saveCachedAssets($key, $libraries) {
        //TODO implement
    }

    /**
    * Locate hash keys for given library and delete them.
    * Used when cache file are deleted.
    *
    * @param int $library_id
    *  Library identifier
    * @return array
    *  List of hash keys removed
    */
    public function deleteCachedAssets($library_id) {
        //TODO implement
    }

    /**
    * Get the amount of content items associated to a library
    * return int
    */
    public function getLibraryContentCount() {
        //TODO implement
    }

    /**
    * Will trigger after the export file is created.
    */
    public function afterExportCreated($content, $filename) {
        //TODO implement
    }

    /**
    * Check if user has permissions to an action
    *
    * @method hasPermission
    * @param  [H5PPermission] $permission Permission type, ref H5PPermission
    * @param  [int]           $id         Id need by platform to determine permission
    * @return boolean
    */
    public function hasPermission($permission, $id = NULL) {
        //TODO implement
    }

    /**
    * Replaces existing content type cache with the one passed in
    *
    * @param object $contentTypeCache Json with an array called 'libraries'
    *  containing the new content type cache that should replace the old one.
    */
    public function replaceContentTypeCache($contentTypeCache) {
        //TODO implement
    }
}