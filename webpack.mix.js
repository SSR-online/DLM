const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// mix.browserSync('dlm.test');
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.js(['resources/assets/js/app.js',
		'resources/assets/js/asides.js',
		'resources/assets/js/stickyfill.js',
		'resources/assets/js/discussions.js',
		'resources/assets/js/sortable.js',
		'resources/assets/js/videoplayers.js',
		'node_modules/basicLightbox/dist/basicLightbox.min.js'], 'public/js').version();

mix.scripts(['resources/assets/js/MediasitePlayerIFrameAPI.js'], 'public/js/MediasitePlayerIFrameAPI.js');
mix.scripts(['resources/assets/js/polyfills.js'], 'public/js/polyfills.js');
//Copy pdf.js
mix.copyDirectory('resources/assets/js/pdfjs', 'public/js/pdfjs');
mix.copyDirectory('resources/assets/js/h5p', 'public/js/h5p');
mix.copy('resources/assets/css/h5p.css', 'public/css/h5p.css');
mix.copyDirectory('resources/assets/themes/', 'public/css/themes');
mix.copyDirectory('node_modules/tinymce/skins/', 'public/css/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes/', 'public/js/themes');