import jQuery from 'jquery';
import TrnAnimator from '../../includes/js/tab-return-notifier-animator';

( function ( $ ) {
	'use strict';

	$( function () {
		const viewConfig = window.trnData || {
			animation: 'rotating',
			speed: 500,
			messages: [],
		};

		const faviconHolder = document.getElementById( 'trn-tab-favicon' );
		if ( faviconHolder ) {
			const favicon = document.querySelector( 'link[rel~="icon"]' );
			if ( favicon ) {
				const faviconImage = favicon ? favicon.href : null;

				if ( faviconImage ) {
					faviconHolder.src = favicon;
				}
			}
		}

		const animator = new TrnAnimator( {
			animation: viewConfig.animation,
			speed: viewConfig.speed,
			messages: viewConfig.messages,
			preview: true,
		} );
	} );
} )( jQuery );
