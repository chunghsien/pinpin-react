
require('dotenv').config();
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const FaviconsWebpackPlugin = require('favicons-webpack-plugin');

let config = {
    entry: {
        admin: path.resolve(__dirname, './modules/React/admin.js'),
    },
    output: {
        filename: '[name].[contenthash].bundle.js',
        chunkFilename: '[name].[contenthash].[id].js',
        path: path.resolve(__dirname, './public/dist'),
        publicPath: '/dist/',
    },
    plugins: [
        new HtmlWebpackPlugin({
            chunks: ['admin'],
            template: path.resolve(__dirname, './modules/React/stubs/admin-default.html.twig'),
            filename: path.resolve(__dirname, './resources/templates/app/admin-default.html.twig'),
            minify: false,
            inject: 'body',
        }),
        new FaviconsWebpackPlugin({
            logo: './resources/logo.png',
            mode: 'webapp',
            devMode: 'light',
            cache: ".wwp-cache",
            prefix: "../assets/icons",
            favicons: {}
        }),
        
        new CleanWebpackPlugin({
            dry: false,
            cleanOnceBeforeBuildPatterns: [
                '**/*',
                '!index.php',
                '!admin-default.html.twig',
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
                exclude: [/node_modules/, /modules\/React\/admin\.js/],
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
                //exclude: [/modules\/React\/admin\.scss/],
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
    //console.log(argv);
    if (argv.mode == 'development' && argv.watch == true) {
        config.watch = true;
        config.watchOptions = {
            ignored: /node_modules/,
            aggregateTimeout: 500,
            poll: 1000
        }
    }
    if (argv.mode == 'production') {
        config.optimization = {
            splitChunks: {
                chunks: 'all',
                maxSize: 350000,
                maxAsyncRequests: 20,
                maxInitialRequests: 20,
            },
            minimize: true,
            minimizer: [
                new CssMinimizerPlugin({ test: /(\.bundle\.css)|(\.scss)$/i }),
                new TerserPlugin(
                    {
                        test: /\.js?$/i,
                        cache: true,
                        parallel: true,
                        sourceMap: true,
                        extractComments: true,
                    }
                )
            ]
        };
    }
    config.mode = argv.mode
    return config;
};