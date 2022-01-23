const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries')
const glob = require('glob')
const path = require('path')

module.exports = {
  // @see https://dev.to/bbenefield89/webpack-how-to-create-dynamic-entry-output-paths-1oc9
  // Keep only scss files whose name match the folder name.
  entry: glob.sync('./weasyprint-rest/templates/*/*.scss').reduce((acc, path) => {
    const match = /([^/]+)\/\1\.scss$/.exec(path)

    if (match) {
      acc[match[1]] = path
    }

    return acc
  }, {}),
  output: {
    path: path.resolve(__dirname)
  },
  module: {
    rules: [
      {
        test: /\.scss$/i,

        // @see https://stackoverflow.com/a/67307684
        type: 'asset/resource',
        generator: {
          filename: 'weasyprint-rest/templates/[name]/[name].css'
        },

        use: [
          // // Creates `style` nodes from JS strings
          // "style-loader",
          // // Translates CSS into CommonJS
          // "css-loader",
          // Compiles Sass to CSS
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new FixStyleOnlyEntriesPlugin({ extensions: ['scss'] })
  ]
}
