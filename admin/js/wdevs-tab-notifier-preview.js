import jQuery from 'jquery';
import WtnAnimator from '../../includes/js/wdevs-tab-notifier-animator';

( function ( $ ) {
	'use strict';

	$( function () {
		const viewConfig = window.wdtanoData || {
			animation: 'rotating',
			speed: 500,
			messages: [],
		};

		const faviconHolder = document.getElementById( 'wdtano-tab-favicon' );
		if (
			faviconHolder &&
			( ! faviconHolder.src || faviconHolder.src === '' )
		) {
			const favicon = document.querySelector( 'link[rel~="icon"]' );
			if ( favicon ) {
				faviconHolder.src = favicon?.length
					? favicon[ 0 ].href
					: favicon?.href;
			}
		}

		const animator = new WtnAnimator( {
			animation: viewConfig.animation,
			speed: viewConfig.speed,
			messages: viewConfig.messages,
			preview: true,
		} );
	} );
} )( jQuery );
