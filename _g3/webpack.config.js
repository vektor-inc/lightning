module.exports = {
  mode: 'production',
  devtool: false,
  entry: [
    './assets/_js/_common.js',
    './assets/_js/_master.js',
    './assets/_js/_sidebar-fixed.js',
    './inc/vk-mobile-nav/package/js/vk-mobile-nav.js',
  ],
  output: {
      path: __dirname + '/assets/js',
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