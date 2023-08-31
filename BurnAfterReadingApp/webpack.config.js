const path = require("path");

module.exports = {
  entry: "./src/js/client.js",
  output: {
    filename: "client.min.js",
    path: path.resolve(__dirname, "../public/bar/assets/js"),
  },
  mode: "production",
};
