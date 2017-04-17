let path = require('path');
let webpack = require('webpack');

let root = __dirname + '/../..';

module.exports = {
    devtool: 'eval',
    entry: {
        main: [
            path.join(root, 'src/Frontend/index.tsx')
        ]
    },
    output: {
        path: path.join(root, 'web/assets/js'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
                options: {
                    transpileOnly: true,
                    compilerOptions: {
                        declaration: false,
                    }
                }
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.ts', '.tsx', '.json'],
        modules: [
            'node_modules',
            path.join(root, 'src/Frontend')
        ]
    },
    devServer: {
        port: 3000,
        ip: "0.0.0.0"
    }
};