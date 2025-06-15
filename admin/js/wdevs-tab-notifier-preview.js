import jQuery from 'jquery';
import WtnAnimator from '../../includes/js/wdevs-tab-notifier-animator';

( function ( $ ) {
	'use strict';

	$( function () {
		const viewConfig = window.wtnData || {
			animation: 'rotating',
			speed: 500,
			messages: [],
		};

		const faviconHolder = document.getElementById( 'wtn-tab-favicon' );
		if ( faviconHolder ) {
			const favicon = document.querySelector( 'link[rel~="icon"]' );
			if ( favicon ) {
				const faviconImage = favicon ? favicon.href : null;

				if ( faviconImage ) {
					faviconHolder.src = favicon;
				}
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
