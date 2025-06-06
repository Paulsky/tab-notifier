const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		'tab-return-notifier-admin': path.resolve(
			process.cwd(),
			'admin/js',
			'index.js'
		),
		'tab-return-notifier-public': path.resolve(
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
					name: 'tab-return-notifier-shared',
					chunks: 'all',
					minChunks: 2,
					enforce: true,
					test: /[\\/]includes[\\/]js[\\/]/,
				},
			},
		},
	},
};
