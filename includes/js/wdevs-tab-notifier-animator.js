import jQuery from 'jquery';

class WtnAnimator {
	constructor( options ) {
		this.settings = {
			animation: 'rotating', // 'rotating' or 'scrolling'
			speed: 500,
			messages: [],
			originalTitle: document.title,
			isTabActive: true,
			animationId: null,
			separatorSymbol: '|',
			preview: false,
		};

		this.init( options );
	}

	init( options ) {
		const vm = this;
		vm.settings = jQuery.extend( {}, vm.settings, options );
		if ( ! vm.settings.preview ) {
			vm.settings.originalTitle = document.title;
			vm.setupEventHandlers();
		} else {
			vm.startAnimation();
		}
	}

	setMessages( messages ) {
		this.settings.messages = messages;
	}

	setupEventHandlers() {
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
	}

	startAnimation() {
		if ( this.settings.isTabActive && ! this.settings.preview ) return;
		this.stopAnimation();

		if ( this.settings.animation === 'rotating' ) {
			this.startRotatingAnimation();
		} else if ( this.settings.animation === 'scrolling' ) {
			this.startScrollingAnimation();
		}
	}

	stopAnimation() {
		if ( this.settings.animationId ) {
			clearInterval( this.settings.animationId );
			this.settings.animationId = null;
		}
	}

	startRotatingAnimation() {
		const vm = this;

		let currentIndex = 0;

		vm.settings.animationId = setInterval( function () {
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
	}

	startScrollingAnimation() {
		const vm = this;
		const separator = ' ' + vm.settings.separatorSymbol + ' ';
		const fullMessage = vm.settings.messages.join( separator ) + separator;
		let chars = Array.from( fullMessage );

		vm.settings.animationId = setInterval( function () {
			chars.push( chars.shift() );
			vm.updateTitleWithMessage( chars.join( '' ) );
		}, vm.settings.speed );
	}

	updateTitleWithMessage( message ) {
		if ( ! this.settings.preview ) {
			document.title = message;
		} else {
			const titleElement = document.getElementById( 'wdtano-tab-title' );
			if ( titleElement ) {
				titleElement.textContent = message;
			}
		}
	}
}

export default WtnAnimator;
