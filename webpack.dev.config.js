const { merge } = require('webpack-merge')
const WebpackConfig = require('./webpack.config.js')

module.exports = merge(WebpackConfig, {
    mode: 'development'
})
