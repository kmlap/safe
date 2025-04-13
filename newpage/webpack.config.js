const webpack = require('webpack')
const path = require('path')
const { VueLoaderPlugin } = require('vue-loader')
//引入html-webpack-plugin，打包html
const HtmlWebpackPlugin = require('html-webpack-plugin')

const isDev = process.env.NODE_ENV === 'development'

module.exports = {
  // 设置开发环境不压缩代码
  mode: 'development',
  devtool: 'cheap-module-eval-source-map',
  entry: './src/main.js',
  // 出口文件
  output: {
    path: path.resolve(__dirname, './dist'),
    filename: 'bundle.js',
    // 打包文件引入的路径
    publicPath: '/'
  },
  //热加载配置，服务器代理
  devServer: {
    index: '',
    // host: '125.93.77.46',
    // proxy: {
    //   '/api/': {
    //     target: 'https://api.robotwallet.net/',
    //     changeOrigin: true
    //   }
    // },
    hot: true,
    watchOptions: {
      poll: true
    },
    historyApiFallback: true
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        use: 'vue-loader'
      },
      {
        test: /\.js$/,
        use: 'babel-loader'
      },
      {
        test: /\.(png|jpe?g|gif|svg|woff|woff2|ttf|eot)(\?.*)?$/,
        use: 'url-loader'
      },
      {
        test: /\.less$/,
        use: [
          {
            loader: 'style-loader' // 使用style-loader进行处理，位置必须在css-loader前面
          },
          {
            loader: 'css-loader' // 使用css-loader进行处理
          },
          {
            loader: 'less-loader'
          }
        ]
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: 'style-loader' // 使用style-loader进行处理，位置必须在css-loader前面
          },
          {
            loader: 'css-loader' // 使用css-loader进行处理
          }
        ]
      }
    ]
  },
  resolve: {
    extensions: [" ", ".js", ".vue", ".less", ".css", ".json"],
    alias: {
      "@": path.resolve(__dirname, "./src"),
      "_c": path.resolve(__dirname, "./src/components")
    }
  },
  plugins: [
    new webpack.HotModuleReplacementPlugin(),
    new VueLoaderPlugin(),
    new HtmlWebpackPlugin({
        //文件注入位置
      filename: 'index.html',
      template: './public/index.html',
      inject: true,
      favicon: './public/web128_128.png'
    })
  ]
}