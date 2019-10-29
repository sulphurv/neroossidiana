var path = require("path");
var webpack = require("webpack");

module.exports = function (env) {
    env = env || {};
    var isProd = env.NODE_ENV === "production";

    var config = {
        entry: {
            main: "./assets/js/main"
        },
        output: {
            path: path.join(__dirname, "./assets/dist"),
            filename: "[name].bundle.js"
        },
        devtool: "eval-source-map",
        plugins: [
            new webpack.ProvidePlugin({ 
                $: "jquery", 
                jQuery: "jquery", 
                'window.jQuery': 'jquery' 
            })
        ],
        module: {
            rules: [
                { test: /\.css?$/, use: ["style-loader", "css-loader"] },
                { test: /\.(png|jpg|jpeg|gif|svg)$/, use: ['url-loader?limit=25000'] },
                { test: /\.(png|woff|woff2|eot|ttf|svg)(\?|$)/, use: ["url-loader?limit=100000"] }
            ]
        }
    };

    if (isProd) {
        config.devtool = "source-map";
        config.plugins = config.plugins.concat([
            new webpack.optimize.UglifyJsPlugin({
                sourceMap: true
            })
        ]);
    }

    return config;
}