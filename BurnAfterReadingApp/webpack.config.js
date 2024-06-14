const path = require("path");

module.exports = {
  entry: {
    client: "./src/js/client.js",
    admin: "./src/js/admin.js",
  },
  output: {
    filename: "[name].min.js",
    path: path.resolve(__dirname, "../public/bar/assets/js"),
  },
  mode: "production",
};
