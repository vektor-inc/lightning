module.exports = {
  mode: 'production',
  entry: [
    './_g3/assets/_js/_common.js',
    './_g3/assets/_js/_master.js',
    './_g3/assets/_js/_sidebar-fixed.js',
    './_g3/inc/vk-mobile-nav/package/js/vk-mobile-nav.js',
  ],
  output: {
      path: __dirname + '/_g3/assets/js',
      filename: 'main.js',
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
};