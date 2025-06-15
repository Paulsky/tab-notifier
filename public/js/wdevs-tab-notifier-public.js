import WtnAnimator from '../../includes/js/wdevs-tab-notifier-animator';

( function ( $, window, document ) {
	'use strict';

	$( function () {
		const viewConfig = window.wtnData || {
			animation: 'rotating',
			speed: 500,
			messages: [],
			ajaxUrl: '',
			nonce: '',
			messagesAction: '',
		};

		const animator = new WtnAnimator( {
			animation: viewConfig.animation,
			speed: viewConfig.speed,
			messages: viewConfig.messages,
		} );

		$( document.body ).on(
			'wc_fragments_loaded wc_fragments_refreshed',
			function () {
				const data = {
					action: viewConfig.messagesAction,
					nonce: viewConfig.nonce,
				};

				jQuery.post(
					viewConfig.ajaxUrl,
					data,
					function ( response ) {
						if ( response.success ) {
							animator.setMessages( response.data.messages );
						}
					},
					'json'
				);
			}
		);
	} );
} )( jQuery, window, document );
