const webpack = require('webpack')
const path = require('path')
const { VueLoaderPlugin } = require('vue-loader')
//引入html-webpack-plugin，打包html
const HtmlWebpackPlugin = require('html-webpack-plugin')
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

module.exports = {
  mode: 'production',
  entry: './src/main.js',
  // 出口文件
  output: {
    path: path.resolve(__dirname, './dist'),
    filename: 'js/[name]_[hash].js',
    // 打包文件引入的路径
    publicPath: '/dist/'
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        use: 'vue-loader'
      },
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
          }
        ]
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name]_[hash].[ext]',
              outputPath: 'img/',
              publicPath: '/dist/img/' // 设置资源路径 /dist/img/
            }
          }
        ]
      },
      {
        test: /\.(woff|woff2|ttf|eot)(\?.*)?$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name]_[hash].[ext]',
              outputPath: 'fonts/',
              publicPath: ' /dist/fonts/' // 设置资源路径 /dist/dist/
            }
          }
        ]
      },
      {
        test: /\.less$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: 'css-loader', // 使用css-loader进行处理
            options: {
              importLoaders: 1
            }
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
            loader: MiniCssExtractPlugin.loader
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
    new VueLoaderPlugin(),
    new CleanWebpackPlugin(),
    new HtmlWebpackPlugin({
        //文件注入位置
      filename: 'index.html',
      template: './public/index.html',
      inject: true,
      favicon: './public/web128_128.png'
    }),
     new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: 'css/[name].css',
      chunkFilename: 'css/[id].css'
    })
  ]
}