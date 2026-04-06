/**
 * Webpack config for G3 block editor panel.
 */
module.exports = {
  mode: 'production',
  devtool: false,
  entry: './_g3/assets/_js/design-setting-panel.js',
  output: {
    path: __dirname + '/_g3/assets/js',
    filename: 'design-setting-panel.js',
  },
  externals: {
    '@wordpress/plugins': 'wp.plugins',
    '@wordpress/edit-post': 'wp.editPost',
    '@wordpress/editor': 'wp.editor',
    '@wordpress/element': 'wp.element',
    '@wordpress/components': 'wp.components',
    '@wordpress/data': 'wp.data',
    '@wordpress/core-data': 'wp.coreData',
    '@wordpress/i18n': 'wp.i18n',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
            },
          },
        ],
        exclude: /node_modules/,
      },
    ],
  },
};
