require('dotenv').config();

const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

let config = {
    target: 'web',
    /*node: {
        global: true,
        __filename: true,
        __dirname: true,
        
    },*/
    entry: {
        site: path.resolve(__dirname, './src/React/site.js'),
        admin: path.resolve(__dirname, './src/React/admin.js'),
    },
    output: {
        filename: '[name].[contenthash].bundle.js',
        chunkFilename: '[name].[contenthash].[id].js',
        path: path.resolve(__dirname, './public/dist'),
        publicPath: '/dist/',
    },
    plugins: [
        new HtmlWebpackPlugin({
            chunks: ['site'],
            template: path.resolve(__dirname, './src/React/stubs/site-default.html.twig'),
            filename: path.resolve(__dirname, './resources/templates/app/site-default.html.twig'),
            favicon: './resources/favicon.ico',
            minify: false,
            inject: 'body'
        }),
        new HtmlWebpackPlugin({
            chunks: ['admin'],
            template: path.resolve(__dirname, './src/React/stubs/admin-default.html.twig'),
            filename: path.resolve(__dirname, './resources/templates/app/admin-default.html.twig'),
            minify: false,
            inject: 'body',
        }),
        new CleanWebpackPlugin({
            dry: false,
            cleanOnceBeforeBuildPatterns: [
                '**/*',
                '!index.php',
                '!.htaccess',
                '!assets/**',
                '!locales/**'
            ],
            dangerouslyAllowCleanPatternsOutsideProject: true
        }),
        new MiniCssExtractPlugin({ filename: '[name].[contenthash].bundle.css' }),
    ],
    module: {
        rules: [
            {
                test: /\.(jsx|js)$/,
                exclude: [/node_modules/, /src\/React\/admin\.js/, /src\/React\/site\.js/],
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react'],
                        plugins: ["@babel/plugin-proposal-class-properties"]
                    }
                }
            },
            {
                test: /\.(scss|css)$/,
                //exclude: [/src\/React\/admin\.scss/, /src\/React\/site\.scss/],
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    'css-loader',
                    'sass-loader',
                ]
            },
            {
                test: /\.(png|jpg|gif)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: '../assets/images',
                        }
                    },
                    {
                        loader: 'image-webpack-loader',
                        options: {
                            disable: true,
                        },
                    },

                ],
            },
            {
                test: /\.(pdf|docx|doc|xls|xlsx)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: '../assets/documents',
                }
            },
            {
                test: /\.(mp4)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: '../assets/medias',
                }
            },
            {
                test: /\.(svg|eot|woff|woff2|ttf)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    outputPath: '../assets/fonts',
                }
            },
        ]
    },
    devServer: {
        port: 8080,
    },
};

module.exports = (env, argv) => {
    if (argv.mode == 'development') {
        config.watch = true;
        config.watchOptions = {
            ignored: /node_modules/
        }
    }
    if (argv.mode == 'production') {
        config.optimization = {
            splitChunks:{
                chunks: 'all',
                maxSize: 350000,
                maxAsyncRequests: 20,
                maxInitialRequests: 20,
            },
            minimize: true,
            minimizer: [
                new CssMinimizerPlugin({test: /(\.bundle\.css)|(\.scss)$/i}),
                new TerserPlugin(
                    {test: /\.js?$/i}
                )
            ]
        };
    }
    config.mode = argv.mode
    return config;
};