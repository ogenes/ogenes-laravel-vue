const mix = require('laravel-mix');
const path = require('path');
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

mix.js('resources/js/app.js', 'public/js')
  .vue();
mix.alias({
  '@': path.join(__dirname, 'resources/js')
});

mix.webpackConfig({
  output: {
    chunkFilename: 'js/split/[name].js',
  }
});

Mix.listen('configReady', (webpackConfig) => {
  webpackConfig.module.rules.push(
    {
      test: /\.svg$/,
      loader: 'svg-sprite-loader',
      include: path.resolve(__dirname, 'resources/js/icons/svg'),
      options: {
        symbolId: 'icon-[name]',
      }
    }
  );
  let fontLoaderConfig = webpackConfig.module.rules.find(rule => String(rule.test) === String(/(\.(png|jpe?g|gif|webp|avif)$|^((?!font).)*\.svg$)/));
  fontLoaderConfig.exclude = /(resources\/js\/icons)/;
});
