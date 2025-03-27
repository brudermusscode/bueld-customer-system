require("dotenv").config();

const path = require("path");

module.exports = {
  entry: process.env.NODE_BUNDLE_ENTRY_FILE,
  output: {
    path: path.resolve(__dirname, process.env.NODE_BUNDLE_OUTPUT_PATH),
    filename: process.env.NODE_PRODUCTION_BUNDLE_OUTPUT_FILENAME,
  },
  module: {
    rules: [
      {
        test: /\.(j|t)s$/,
        exclude: [/[\\/]node_modules[\\/]/],
        loader: "builtin:swc-loader",
      },
      {
        test: /\.(sass|scss)$/,
        use: [
          "style-loader",
          "css-loader",
          {
            loader: "sass-loader",
            options: {
              api: "modern-compiler",
              sourceMap: false,
              implementation: require.resolve("sass-embedded"),
            },
          },
        ],
      },
    ],
  },
  mode: "production",
  devtool: false,
};
