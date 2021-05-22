const { merge } = require('webpack-merge')
const WebpackConfig = require('./webpack.g2prod.js')

module.exports = merge(WebpackConfig, {
    mode: 'development'
})
