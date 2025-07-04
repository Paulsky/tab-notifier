const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );

module.exports = {
	...defaultConfig,
	entry: {
		'wdevs-tab-notifier-admin': path.resolve(
			process.cwd(),
			'admin/js',
			'index.js'
		),
		'wdevs-tab-notifier-public': path.resolve(
			process.cwd(),
			'public/js',
			'index.js'
		),
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization.splitChunks.cacheGroups,
				vendors: false,
				sharedGlobal: {
					name: 'wdevs-tab-notifier-shared',
					chunks: 'all',
					minChunks: 2,
					enforce: true,
					test: /[\\/]includes[\\/]js[\\/]/,
				},
			},
		},
	},
	plugins: [
		...defaultConfig.plugins,
		new CopyWebpackPlugin( {
			patterns: [
				{
					from: 'node_modules/emoji-picker-element-data/en/emojibase/data.json',
					to: 'emoji-picker-element-data/en/emojibase/[name][ext]',
				},
			],
		} ),
	],
};
