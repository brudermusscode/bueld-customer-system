const path = require("path");

module.exports = {
  entry: process.env.NODE_BUNDLE_ENTRY_FILE,
  output: {
    path: path.resolve(__dirname, process.env.NODE_BUNDLE_OUTPUT_PATH),
    filename: process.env.NODE_BUNDLE_OUTPUT_FILENAME,
    publicPath: process.env.NODE_BUNDLE_OUTPUT_PUBLIC_PATH,
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
              sourceMap: true,
              implementation: require.resolve("sass-embedded"),
              quietDeps: true,
            },
          },
        ],
      },
    ],
  },
  devServer: {
    client: {
      // webSocketUrl:
      //   "auto://" +
      //   process.env.NODE_DEVSERVER_HOST +
      //   ":" +
      //   process.env.NODE_DEVSERVER_PORT +
      //   "/ws",
      overlay: true,
      logging: "verbose",
      progress: true,
      reconnect: 4,
    },
    webSocketServer: {
      type: "ws",
      options: {
        onConnection: (socket) => {
          socket.on("error", (err) => {
            console.error("[webpack-dev-server] WebSocket error:", err.message);
          });
        },
      },
    },
    port: process.env.NODE_DEVSERVER_PORT,
    host: process.env.NODE_DEVSERVER_HOST,
    allowedHosts: "all",
    static: process.env.NODE_DEVSERVER_STATIC,
    proxy: [
      {
        context: ["/"],
        target: process.env.NODE_DEVSERVER_PROXY_ROOT,
        secure: false,
      },
    ],
  },
  watch: true,
  watchOptions: {
    aggregateTimeout: 100,
    poll: 100,
  },
  mode: "development",
  devtool: "inline-source-map",
};
