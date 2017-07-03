var path = require('path');

module.exports = {
  watch: true,
  entry: './index.js',
  output: {
    path: path.join(__dirname, '../public/assets/js'),
    filename: 'grid.wp.bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['es2015', 'react'],
          }
        }
      }
    ]
  },
}
