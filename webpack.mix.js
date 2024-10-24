const mix = require('laravel-mix');

// Compile JS and CSS files
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css') // If you are using SASS
   .css('resources/css/app.css', 'public/css');   // This line is for custom CSS
