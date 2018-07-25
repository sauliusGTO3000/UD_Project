var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/coverImageBrowser', './assets/js/coverImageBrowser.js')
    .addEntry('js/infiniteScroll', './assets/js/infiniteScroll.js')
    .addEntry('js/navbar', './assets/js/navbar.js')
    .addStyleEntry('css/app', './assets/sass/app.scss')
    .addStyleEntry('css/coverImageBrowser', './assets/sass/coverImageBrowser.scss')
    .addStyleEntry('css/parallax', './assets/sass/parallax.scss')
    .addStyleEntry('css/navbar', './assets/sass/navbar.scss')
    .addStyleEntry('css/archive', './assets/sass/archive.scss')
    .addStyleEntry('css/sidebar', './assets/sass/sidebar.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
