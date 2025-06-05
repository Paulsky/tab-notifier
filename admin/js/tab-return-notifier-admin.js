import jQuery from 'jquery';

( function ( $ ) {
	'use strict';

	$( function () {
		$( '.health-check-accordion' ).on(
			'click',
			'.health-check-accordion-trigger',
			function () {
				const isExpanded = 'true' === $( this ).attr( 'aria-expanded' );
				const accordionId = $( this ).attr( 'aria-controls' );

				if ( isExpanded ) {
					$( this ).attr( 'aria-expanded', 'false' );
					$( '#' + accordionId ).attr( 'hidden', true );
				} else {
					$( this ).attr( 'aria-expanded', 'true' );
					$( '#' + accordionId ).attr( 'hidden', false );
				}

				let accordionStates = JSON.parse(
					localStorage.getItem( 'trnAccordionStates' ) || '{}'
				);

				accordionStates[ accordionId ] = ! isExpanded;

				localStorage.setItem(
					'trnAccordionStates',
					JSON.stringify( accordionStates )
				);
			}
		);

		function openAccordions() {
			const savedStates = JSON.parse(
				localStorage.getItem( 'trnAccordionStates' ) || {}
			);

			$( '.health-check-accordion-trigger' ).each( function () {
				const accordionId = $( this ).attr( 'aria-controls' );
				const hidden = $( '#' + accordionId ).attr( 'hidden' );

				if ( hidden && savedStates.hasOwnProperty( accordionId ) ) {
					const shouldBeExpanded = savedStates[ accordionId ];

					$( this ).attr(
						'aria-expanded',
						shouldBeExpanded ? 'true' : 'false'
					);
					$( '#' + accordionId ).attr( 'hidden', ! shouldBeExpanded );
				}
			} );
		}

		openAccordions();
	} );
} )( jQuery );
