( function ( $, window, document ) {
	'use strict';

	const trnAnimator = {
		settings: {
			animation: 'rotating', // 'rotating' or 'scrolling'
			speed: 500,
			messages: [],
			originalTitle: document.title,
			isTabActive: true,
			animationId: null,
			separatorSymbol: '|',
			ajaxUrl: '',
			nonce: '',
			messagesAction: '',
		},

		init: function ( options ) {
			this.settings = $.extend( {}, this.settings, options );
			this.settings.originalTitle = document.title;

			if ( this.settings.messages.length === 0 ) {
				return;
			}

			this.setupEventHandlers();
		},

		setupEventHandlers: function () {
			const vm = this;

			document.addEventListener( 'visibilitychange', function () {
				if ( document.hidden ) {
					vm.settings.isTabActive = false;
					vm.startAnimation();
				} else {
					vm.updateTitleWithMessage( vm.settings.originalTitle );
					vm.settings.isTabActive = true;
					vm.stopAnimation();
				}
			} );

			$( document.body ).on(
				'wc_fragments_loaded wc_fragments_refreshed',
				function () {
					vm.reloadMessages();
				}
			);
		},

		reloadMessages: function () {
			const vm = this;
			const data = {
				action: vm.settings.messagesAction,
				nonce: vm.settings.nonce,
			};

			$.post(
				vm.settings.ajaxUrl,
				data,
				function ( response ) {
					if ( response.success ) {
						vm.settings.messages = response.data.messages;
					}
				},
				'json'
			);
		},

		startAnimation: function () {
			if ( this.settings.isTabActive ) return;

			this.stopAnimation();

			if ( this.settings.animation === 'rotating' ) {
				this.startRotatingAnimation();
			} else if ( this.settings.animation === 'scrolling' ) {
				this.startScrollingAnimation();
			}
		},

		stopAnimation: function () {
			if ( this.settings.animationId ) {
				clearInterval( this.settings.animationId );
				this.settings.animationId = null;
			}
		},

		startRotatingAnimation: function () {
			const vm = this;

			let currentIndex = 0;

			this.settings.animationId = setInterval( function () {
				if ( currentIndex > vm.settings.messages.length - 1 ) {
					currentIndex = 0;
				}
				if ( vm.settings.messages[ currentIndex ] ) {
					vm.updateTitleWithMessage(
						vm.settings.messages[ currentIndex ]
					);
				}
				currentIndex++;
			}, vm.settings.speed );
		},

		startScrollingAnimation: function () {
			const vm = this;
			const separator = ' ' + vm.settings.separatorSymbol + ' ';
			const fullMessage =
				vm.settings.messages.join( separator ) + separator;
			let chars = Array.from( fullMessage );

			vm.settings.animationId = setInterval( function () {
				chars.push( chars.shift() );
				vm.updateTitleWithMessage( chars.join( '' ) );
			}, vm.settings.speed );
		},

		updateTitleWithMessage: function ( message ) {
			document.title = message;
		},
	};

	$( function () {
		const viewConfig = window.trnData || {
			animation: 'rotating',
			speed: 500,
			messages: [],
			ajaxUrl: '',
			nonce: '',
			messagesAction: '',
		};

		trnAnimator.init( {
			animation: viewConfig.animation,
			speed: viewConfig.speed,
			messages: viewConfig.messages,
			ajaxUrl: viewConfig.ajaxUrl,
			nonce: viewConfig.nonce,
			messagesAction: viewConfig.messagesAction,
		} );
	} );
} )( jQuery, window, document );
