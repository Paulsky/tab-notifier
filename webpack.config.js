const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

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
};
