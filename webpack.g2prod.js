const path = require('path');

module.exports = {
  mode: 'production',
  entry: [
    './_g2/assets/_js/_common.js',
    './_g2/assets/_js/_master.js',
    './_g2/assets/_js/_header_fixed.js',
    './_g2/assets/_js/_sidebar-fixed.js',
    './_g2/assets/_js/_vk-prlx.min.js',
    './_g2/inc/vk-mobile-nav/package/js/vk-mobile-nav.js',
  ],
  output: {
    path: path.resolve(__dirname, '_g2/assets/js'),
    filename: 'lightning.min.js'
  },
  module: {
    // babel-loaderの設定
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [
                '@babel/preset-env'
              ]
            }
          }
        ],
        exclude: /node_modules/,
      }
    ]
  },
  plugins: [
  ],
};
