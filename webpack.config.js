const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
module.exports = {
    plugins: [
        new VuetifyLoaderPlugin(),
    ]
};

const mix = require('laravel-mix')
mix.browserSync('localhost:8000')
