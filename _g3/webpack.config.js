module.exports = {
  mode: 'production',
  entry: [
    './assets/_js/_common.js',
    './assets/_js/_master.js',
    './assets/_js/_sidebar-fixed.js',
    // './assets/_js/_master.js',
    // './assets/_js/_vk-prlx.min.js',
    './inc/vk-mobile-nav/package/js/vk-mobile-nav.js',
  ],
  output: {
      path: __dirname + '/assets/js',
      filename: 'main.js',
  },
  devtool: "source-map",
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