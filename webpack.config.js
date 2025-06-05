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
};
