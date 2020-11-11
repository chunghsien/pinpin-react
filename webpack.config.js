require('dotenv').config();

const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const FaviconsWebpackPlugin = require('favicons-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const AfterDonePlugin = require('./modules/React/plugins/AfterDonePlugin');


let config = {
    node: { fs: "empty" },
    entry: {
        site: path.resolve(__dirname, './modules/React/site.js'),
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
            chunks: ['site'],
            template: path.resolve(__dirname, './modules/React/stubs/site-default.html.twig'),
            filename: path.resolve(__dirname, './public/site-default.html.twig'),
            favicon: './resources/favicon.ico',
            minify: false,
            inject: 'body'
        }),
        new HtmlWebpackPlugin({
            chunks: ['admin'],
            template: path.resolve(__dirname, './modules/React/stubs/admin-default.html.twig'),
            filename: path.resolve(__dirname, './public/admin-default.html.twig'),
            minify: false,
            inject: 'body',
        }),
        new FaviconsWebpackPlugin({
            logo: './resources/logo.png',
            mode: 'webapp',
            devMode: 'light',
            cache: ".wwp-cache",
            prefix: "../assets/icons",
            inject: function(html) {
                return html.options.filename === 'site-default.html.twig';
            },
            favicons: {}
        }),
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: [
                '**/*',
                '!index.php',
                '!.htaccess',
                '!*.html.twig',
                '!assets/**',
                '!locales/**'
            ]
        }, { exclude: ['assets'] }),
        new MiniCssExtractPlugin({ filename: '[name].[contenthash].bundle.css' }),
        new AfterDonePlugin()
    ],
    module: {
        rules: [
            {
                test: /\.(jsx|js)$/,
                exclude: [/node_modules/, /modules\/React\/admin\.js/, /modules\/React\/site\.js/],
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
                //exclude: [/modules\/React\/admin\.scss/, /modules\/React\/site\.scss/],
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
    if (argv.mode == 'production') {
        config.optimization = {
            splitChunks: {
                chunks: 'all'
            },
            minimize: true,
            minimizer: [
                new CssMinimizerPlugin({test: /(\.bundle\.css)|(\.scss)$/i}),
                new TerserPlugin({
                    cache: true,
                    parallel: true,
                    sourceMap: true,
                    extractComments: true,
                }),
            ]
        };
    }
    return config;
};