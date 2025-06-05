import jQuery from 'jquery';
import 'emoji-picker-element';

( function ( $, window ) {
	'use strict';

	const TrnElements = {
		settings: {
			variables: [],
			menuSelector: '#trn-variable-menu',
			emojiPickerSelector: 'emoji-picker',
		},

		init: function ( options ) {
			this.settings.variables = this.getVariables();
			this.settings = $.extend( {}, this.settings, options );
			this.setMenu();
			this.setEmojiPicker();
			this.setupEventHandlers();
		},

		getVariables() {
			const formatted = [];
			if ( typeof window.trnData === 'undefined' ) {
				return formatted;
			}

			if ( ! window.trnData.variables ) {
				return formatted;
			}

			const variables = window.trnData.variables;

			for ( const [ key, data ] of Object.entries( variables ) ) {
				formatted.push( {
					key: `{{${ key }}}`,
					label: data.label,
				} );
			}

			return formatted;
		},

		// Create variable menu DOM
		setMenu: function () {
			if ( $( this.settings.menuSelector ).length > 0 ) return;

			const $menu = $( '<div>', {
				id: 'trn-variable-menu',
				css: {
					display: 'none',
					position: 'absolute',
					zIndex: 9999,
				},
			} );

			const $ul = $( '<ul>' );
			this.settings.variables.forEach( ( variable ) => {
				$( '<li>', {
					text: variable.label,
					'data-variable': variable.key,
					class: 'trn-variable-menu-item',
				} ).appendTo( $ul );
			} );

			$menu.append( $ul );
			$( 'body' ).append( $menu );
		},

		setEmojiPicker() {
			const $picker = $( this.settings.emojiPickerSelector );
			if ( $picker.length === 0 ) return;

			if ( $picker.parent().get( 0 ) !== document.body ) {
				$picker.appendTo( document.body );
			}

			$picker.css( {
				display: 'none',
				position: 'absolute',
				zIndex: 9999,
			} );
		},

		// Setup all event handlers
		setupEventHandlers: function () {
			$( document ).on(
				'mouseup keyup',
				'.trn-editable-input',
				this.handleSelectionChange.bind( this )
			);
			$( document ).on(
				'click',
				'.trn-insert-variable',
				this.handleVariableButtonClick.bind( this )
			);
			$( document ).on(
				'click',
				'.trn-insert-emoji',
				this.handleEmojiButtonClick.bind( this )
			);
			$( document ).on( 'click', this.handleOutsideClick.bind( this ) );
			$( document ).on(
				'click',
				'.trn-editable-input .variable',
				this.handleVariableClick.bind( this )
			);
			$( document ).on(
				'keydown',
				'.trn-editable-input',
				this.handleKeyEvents.bind( this )
			);

			const $menu = $( this.settings.menuSelector );
			if ( $menu.length ) {
				$menu.on(
					'click',
					'li',
					this.handleVariableSelection.bind( this )
				);
			}

			const $picker = $( this.settings.emojiPickerSelector );
			if ( $picker.length ) {
				$picker.on(
					'emoji-click',
					this.handleEmojiSelection.bind( this )
				);
			}

			$( '.trn-messages-container' ).on(
				'input blur',
				'.trn-editable-input',
				this.handleInputChange.bind( this )
			);
		},

		handleSelectionChange: function () {
			const selection = window.getSelection();
			if ( selection.rangeCount > 0 ) {
				this.lastRange = selection.getRangeAt( 0 ).cloneRange();
			}
		},

		handleVariableButtonClick: function ( e ) {
			const $button = $( e.currentTarget );
			const $menu = $( this.settings.menuSelector );
			this.hideEmojiPicker();
			this.showElementAtCaret( $button, e, $menu );
		},

		handleEmojiButtonClick: function ( e ) {
			const $button = $( e.currentTarget );
			const picker = document.querySelector( 'emoji-picker' );
			const $picker = $( picker );
			this.hideMenu();
			this.showElementAtCaret( $button, e, $picker );
		},

		handleVariableSelection: function ( e ) {
			const $li = $( e.currentTarget );
			this.insertVariable( $li.data( 'variable' ) );
			this.hideMenu();
		},

		handleEmojiSelection: function ( e ) {
			this.insertEmoji( e.detail.unicode );
			this.hideEmojiPicker();
		},

		handleOutsideClick: function ( e ) {
			const $target = $( e.target );
			const isMenuClick =
				$target.closest( this.settings.menuSelector ).length > 0;
			const isPickerClick =
				$target.closest( this.settings.emojiPickerSelector ).length > 0;
			const isTriggerClick =
				$target.closest( '.trn-insert-variable, .trn-insert-emoji' )
					.length > 0;

			if ( ! isMenuClick && ! isPickerClick && ! isTriggerClick ) {
				this.hideMenu();
				this.hideEmojiPicker();
			}
		},

		handleVariableClick: function ( e ) {
			e.preventDefault();
			this.selectVariable( e.currentTarget, true );
		},

		handleKeyEvents: function ( e ) {
			const selection = window.getSelection();

			if ( e.key === 'Delete' || e.key === 'Backspace' ) {
				this.handleDeleteNearVariables( e, selection );
			} else {
				//this is needed
				//because if this is not here
				//and we type, while a variable is selected
				//the browser starts typing inside the element instead of removing the element
				if ( ! selection.isCollapsed ) {
					if ( e.ctrlKey || e.altKey || e.metaKey ) return;
					if ( e.key.length === 1 ) {
						const range = selection.getRangeAt( 0 );
						range.deleteContents();
					}
				}
			}
		},

		handleDeleteNearVariables: function ( e, selection ) {
			if ( selection.rangeCount === 0 ) return;

			const range = selection.getRangeAt( 0 );
			let variableToSelect = null;

			if ( ! selection.isCollapsed ) return;

			if ( e.key === 'Delete' ) {
				const nodeAfterCaret = this.getNodeAfterCaret( range );
				if ( nodeAfterCaret?.classList?.contains( 'variable' ) ) {
					variableToSelect = nodeAfterCaret;
				}
			} else if ( e.key === 'Backspace' ) {
				const nodeBeforeCaret = this.getNodeBeforeCaret( range );
				if ( nodeBeforeCaret ) {
					const el =
						nodeBeforeCaret.nodeType === Node.ELEMENT_NODE
							? nodeBeforeCaret
							: nodeBeforeCaret.parentElement;
					if ( el?.classList?.contains( 'variable' ) ) {
						variableToSelect = el;
					}
				}
			}

			if ( variableToSelect ) {
				e.preventDefault();
				this.selectVariable( variableToSelect );
			}
		},

		handleInputChange: function ( e ) {
			const $editable = $( e.target );
			const $hidden = $editable.siblings( 'input[type="hidden"]' );
			$hidden.val(
				$editable
					.text()
					.trim()
					.replace( /[\u200B\u200C\u200D\uFEFF]/g, '' )
			);
		},

		hideMenu: function () {
			const $menu = $( this.settings.menuSelector );
			if ( $menu.length ) $menu.hide();
		},

		hideEmojiPicker: function () {
			const $picker = $( this.settings.emojiPickerSelector );
			if ( $picker.length ) $picker.hide();
		},

		getNodeBeforeCaret: function ( range ) {
			let node = range.startContainer;
			let offset = range.startOffset;

			if ( node.nodeType === Node.TEXT_NODE ) {
				if ( offset > 0 ) return node;
				let prev = node.previousSibling;
				while ( ! prev && node.parentNode ) {
					node = node.parentNode;
					prev = node.previousSibling;
				}
				return prev;
			} else if ( node.nodeType === Node.ELEMENT_NODE ) {
				if ( offset > 0 ) return node.childNodes[ offset - 1 ];
				let prev = node.previousSibling;
				while ( ! prev && node.parentNode ) {
					node = node.parentNode;
					prev = node.previousSibling;
				}
				return prev;
			}
			return null;
		},

		getNodeAfterCaret: function ( range ) {
			const container = range.startContainer;
			const offset = range.startOffset;

			if ( container.nodeType === Node.TEXT_NODE ) {
				if ( offset >= container.textContent.length ) {
					return container.nextSibling;
				}
			} else if ( container.nodeType === Node.ELEMENT_NODE ) {
				const childNodes = Array.from( container.childNodes );
				return childNodes[ offset ] || null;
			}
			return null;
		},

		showElementAtCaret: function ( $button, event, $element ) {
			const $group = $button.closest( '.trn-message-input-group' );
			const $editable = $group.find( '.trn-editable-input' );
			$editable.focus();

			if ( this.lastRange ) {
				const rangeContainer = this.lastRange.startContainer;
				const containerElement =
					rangeContainer.nodeType === Node.TEXT_NODE
						? rangeContainer.parentNode
						: rangeContainer;
				const isInCurrentGroup = $.contains(
					$group[ 0 ],
					containerElement
				);
				if ( ! isInCurrentGroup ) this.lastRange = null;
			}

			if ( ! this.lastRange ) {
				const range = document.createRange();
				range.selectNodeContents( $editable[ 0 ] );
				range.collapse( false );
				this.lastRange = range;
			}

			const range = this.lastRange.cloneRange();
			const rect = range.getBoundingClientRect();

			if ( rect.width === 0 ) {
				this.positionElementForEmptyRange( $element, range );
			} else {
				$element.css( {
					display: 'block',
					left: rect.right + window.pageXOffset,
					top: rect.bottom + window.pageYOffset,
				} );
			}
		},

		positionElementForEmptyRange: function ( $element, range ) {
			const tempSpan = document.createElement( 'span' );
			tempSpan.textContent = '\u200b';
			range.insertNode( tempSpan );
			const tempRect = tempSpan.getBoundingClientRect();
			$element.css( {
				display: 'block',
				left: tempRect.right + window.pageXOffset,
				top: tempRect.bottom + window.pageYOffset,
			} );
			tempSpan.remove();
		},

		insertVariable: function ( variable ) {
			const selection = window.getSelection();
			if ( ! selection.rangeCount && ! this.lastRange ) return;

			const range = this.lastRange || selection.getRangeAt( 0 );
			range.deleteContents();

			// Insert zero-width space before
			const textNodeBefore = document.createTextNode( '\u200B' );
			range.insertNode( textNodeBefore );

			// Insert the variable
			const code = document.createElement( 'code' );
			code.className = 'variable';
			code.textContent = variable;
			range.insertNode( code );

			// Insert zero-width space after
			const textNodeAfter = document.createTextNode( '\u200B' );
			range.insertNode( textNodeAfter );

			// Position cursor after the last zero-width space
			const newRange = document.createRange();
			newRange.setStartAfter( textNodeBefore );
			newRange.collapse( true );

			selection.removeAllRanges();
			selection.addRange( newRange );
			this.lastRange = newRange.cloneRange();

			$( code ).closest( '.trn-editable-input' ).trigger( 'input' );
		},

		selectVariable: function ( variableElement ) {
			const range = document.createRange();
			range.selectNode( variableElement );
			const selection = window.getSelection();
			selection.removeAllRanges();
			selection.addRange( range );
			this.lastRange = range.cloneRange();
		},

		insertEmoji: function ( emoji ) {
			const selection = window.getSelection();
			if ( ! selection.rangeCount && ! this.lastRange ) return;

			const range = this.lastRange || selection.getRangeAt( 0 );
			range.deleteContents();

			const textNode = document.createTextNode( emoji );
			range.insertNode( textNode );

			const newRange = document.createRange();
			newRange.setStartAfter( textNode );
			newRange.collapse( true );

			selection.removeAllRanges();
			selection.addRange( newRange );
			this.lastRange = newRange.cloneRange();

			$( textNode ).closest( '.trn-editable-input' ).trigger( 'input' );
		},
	};

	$( function () {
		TrnElements.init();


		jQuery(document).ready(function($) {
			$('.trn-messages-container').sortable({
				items: '> .trn-message-input-group',
				handle: '.trn-drag-handle',
				axis: 'y',
			});
		});

		// Add new message field
		$( '.trn-messages-container' ).on(
			'click',
			'.trn-add-message',
			function () {
				const container = $( this ).closest(
					'.trn-messages-container'
				);
				const newGroup = container
					.find( '.trn-message-input-group' )
					.first()
					.clone();
				newGroup.find( 'input' ).val( '' );
				newGroup.find( '.trn-editable-input' ).html( '' );
				container.find( '.trn-add-message' ).before( newGroup );
			}
		);

		// Remove message field
		$( '.trn-messages-container' ).on(
			'click',
			'.trn-remove-message',
			function () {
				const container = $( this ).closest(
					'.trn-messages-container'
				);
				const groups = container.find( '.trn-message-input-group' );
				if ( groups.length > 1 ) {
					$( this ).closest( '.trn-message-input-group' ).remove();
				} else {
					$( this )
						.closest( '.trn-message-input-group' )
						.find( 'input' )
						.val( '' );
					$( this )
						.closest( '.trn-message-input-group' )
						.find( '.trn-editable-input' )
						.html( '' );
				}
			}
		);
	} );
} )( jQuery, window );
