( function ( $ ) {
	'use strict';

	const VariableMenu = {
		settings: {
			variables: [
				{ key: '{{order_number}}', label: 'Order Number' },
				{ key: '{{customer_name}}', label: 'Customer Name' },
				{ key: '{{return_date}}', label: 'Return Date' },
			],
			menuId: 'trn-variable-menu',
		},

		init: function ( options ) {
			this.settings = $.extend( {}, this.settings, options );
			this.createMenu();
			this.setupEventHandlers();
			this.setupMessageContainerHandlers();
		},

		/**
		 * Creates the variable menu DOM element
		 */
		createMenu: function () {
			if ( $( '#' + this.settings.menuId ).length > 0 ) return;

			const $menu = $( '<div>', {
				id: this.settings.menuId,
				css: {
					display: 'none',
					position: 'absolute',
					zIndex: 9999,
					background: '#fff',
					border: '1px solid #ccc',
					padding: '5px',
				},
			} );

			const $ul = $( '<ul>', {
				css: { margin: 0, padding: 0, listStyle: 'none' },
			} );

			this.settings.variables.forEach( ( variable ) => {
				$( '<li>', {
					text: variable.label,
					'data-variable': variable.key,
					css: {
						cursor: 'pointer',
						padding: '2px 5px',
					},
				} ).appendTo( $ul );
			} );

			$menu.append( $ul );
			$( 'body' ).append( $menu );
		},

		/**
		 * Positions the menu when there's no text selection (empty range)
		 */
		positionMenuForEmptyRange: function ( $menu, range ) {
			const tempSpan = document.createElement( 'span' );
			tempSpan.textContent = '\u200b';
			range.insertNode( tempSpan );
			const tempRect = tempSpan.getBoundingClientRect();
			$menu.css( {
				display: 'block',
				left: tempRect.right + window.pageXOffset,
				top: tempRect.bottom + window.pageYOffset,
			} );
			tempSpan.remove();
		},

		/**
		 * Sets up all event handlers for the variable menu
		 */
		setupEventHandlers: function () {
			const $menu = $( '#' + this.settings.menuId );

			// Store selection range
			$( document ).on(
				'mouseup keyup',
				'.trn-editable-input',
				this.handleSelectionChange.bind( this )
			);

			// Show menu on variable button click
			$( document ).on(
				'click',
				'.trn-insert-variable',
				this.handleVariableButtonClick.bind( this )
			);

			// Handle variable selection from menu
			$menu.on(
				'click',
				'li',
				this.handleVariableSelection.bind( this )
			);

			// Close menu when clicking outside
			$( document ).on( 'click', this.handleOutsideClick.bind( this ) );

			// Select variable on click
			$( document ).on(
				'click',
				'.trn-editable-input .variable',
				this.handleVariableClick.bind( this )
			);

			// Handle key events in editable inputs
			$( document ).on(
				'keydown',
				'.trn-editable-input',
				this.handleKeyEvents.bind( this )
			);
		},

		/**
		 * Sets up handlers for message container actions
		 */
		setupMessageContainerHandlers: function () {
			const self = this;

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
						$( this )
							.closest( '.trn-message-input-group' )
							.remove();
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

			// Sync contenteditable to hidden inputs
			$( '.trn-messages-container' ).on(
				'input blur',
				'.trn-editable-input',
				function () {
					const $editable = $( this );
					const $hidden = $editable.siblings(
						'input[type="hidden"]'
					);
					$hidden.val(
						$editable
							.text()
							.trim()
							.replace( /[\u200B\u200C\u200D\uFEFF]/g, '' )
					);
				}
			);
		},

		/**
		 * Handles selection changes in editable inputs
		 */
		handleSelectionChange: function () {
			const selection = window.getSelection();
			if ( selection.rangeCount > 0 ) {
				this.lastRange = selection.getRangeAt( 0 ).cloneRange();
			}
		},

		/**
		 * Handles clicks on the variable button to show the menu
		 */
		handleVariableButtonClick: function ( e ) {
			const $button = $( e.currentTarget );
			const $menu = $( '#' + this.settings.menuId );
			this.showMenu( $button, e, $menu );
		},

		/**
		 * Handles variable selection from the menu
		 */
		handleVariableSelection: function ( e ) {
			const $li = $( e.currentTarget );
			this.insertVariable( $li.data( 'variable' ) );
			$( '#' + this.settings.menuId ).hide();
		},

		/**
		 * Handles clicks outside the menu to close it
		 */
		handleOutsideClick: function ( e ) {
			const $menu = $( '#' + this.settings.menuId );
			if (
				! $( e.target ).closest(
					'#' + this.settings.menuId + ', .trn-insert-variable'
				).length
			) {
				$menu.hide();
			}
		},

		/**
		 * Handles clicks on variable elements to select them
		 */
		handleVariableClick: function ( e ) {
			e.preventDefault();
			this.selectVariable( e.currentTarget, true );
		},

		/**
		 * Handles key events in editable inputs
		 */
		handleKeyEvents: function ( e ) {
			const selection = window.getSelection();

			// Handle Delete/Backspace near variables
			if ( e.key === 'Delete' || e.key === 'Backspace' ) {
				this.handleDeleteNearVariables( e, selection );
			} else {
				//this is needed
				//because if this is not here
				//and we type, while a variable is selected
				//the browser starts typing inside the element instead of removing the element
				const selection = window.getSelection();
				if ( ! selection.isCollapsed ) {
					if ( e.ctrlKey || e.altKey || e.metaKey ) {
						return;
					}
					if ( e.key.length === 1 ) {
						const range = selection.getRangeAt( 0 );
						range.deleteContents();
					}
				}
			}
		},

		/**
		 * Handles Delete/Backspace keys near variables
		 */
		handleDeleteNearVariables: function ( e, selection ) {
			if ( selection.rangeCount === 0 ) return;

			const range = selection.getRangeAt( 0 );
			let variableToSelect = null;

			if ( ! selection.isCollapsed ) {
				return;
			}

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

		/**
		 * Gets the node immediately before the caret position
		 */
		getNodeBeforeCaret: function ( range ) {
			let node = range.startContainer;
			let offset = range.startOffset;

			if ( node.nodeType === Node.TEXT_NODE ) {
				if ( offset > 0 ) {
					return node;
				} else {
					let prev = node.previousSibling;
					while ( ! prev && node.parentNode ) {
						node = node.parentNode;
						prev = node.previousSibling;
					}
					return prev;
				}
			} else if ( node.nodeType === Node.ELEMENT_NODE ) {
				if ( offset > 0 ) {
					return node.childNodes[ offset - 1 ];
				} else {
					let prev = node.previousSibling;
					while ( ! prev && node.parentNode ) {
						node = node.parentNode;
						prev = node.previousSibling;
					}
					return prev;
				}
			}
			return null;
		},

		/**
		 * Gets the node immediately after the caret position
		 */
		getNodeAfterCaret: function ( range ) {
			const container = range.startContainer;
			const offset = range.startOffset;

			if ( container.nodeType === Node.TEXT_NODE ) {
				// If we're at the end of a text node, get the next sibling
				if ( offset >= container.textContent.length ) {
					return container.nextSibling;
				}
			} else if ( container.nodeType === Node.ELEMENT_NODE ) {
				// If we're in an element, get the child at offset
				const childNodes = Array.from( container.childNodes );
				return childNodes[ offset ] || null;
			}
			return null;
		},

		/**
		 * Shows the variable menu at the appropriate position
		 */
		showMenu: function ( $button, event, $menu ) {
			const $group = $button.closest( '.trn-message-input-group' );
			const $editable = $group.find( '.trn-editable-input' );
			$editable.focus();

			// Check if lastRange exists and is within the current editable
			if ( this.lastRange ) {
				const rangeContainer = this.lastRange.startContainer;
				const containerElement =
					rangeContainer.nodeType === Node.TEXT_NODE
						? rangeContainer.parentNode
						: rangeContainer;

				// Check if the container is inside our current group
				const isInCurrentGroup = $.contains(
					$group[ 0 ],
					containerElement
				);

				if ( ! isInCurrentGroup ) {
					this.lastRange = null;
				}
			}

			// Create a range at the end if there's no selection
			if ( ! this.lastRange ) {
				const range = document.createRange();
				range.selectNodeContents( $editable[ 0 ] );
				range.collapse( false ); // Collapse to end
				this.lastRange = range;
			}

			const range = this.lastRange.cloneRange();
			const rect = range.getBoundingClientRect();

			if ( rect.width === 0 ) {
				this.positionMenuForEmptyRange( $menu, range );
			} else {
				$menu.css( {
					display: 'block',
					left: rect.right + window.pageXOffset,
					top: rect.bottom + window.pageYOffset,
				} );
			}
		},

		/**
		 * Inserts a variable at the current cursor position
		 */
		insertVariable: function ( variable ) {
			const selection = window.getSelection();
			if ( ! selection.rangeCount && ! this.lastRange ) return;

			const range = this.lastRange || selection.getRangeAt( 0 );
			range.deleteContents();

			// Insert the new variable
			const code = document.createElement( 'code' );
			code.className = 'variable';
			code.textContent = variable;
			range.insertNode( code );

			// Add zero-width space after the variable
			const textNode = document.createTextNode( '\u200B' );
			code.parentNode.insertBefore( textNode, code.nextSibling );

			// Position cursor after the variable
			const newRange = document.createRange();
			newRange.setStartAfter( textNode );
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
	};

	$( function () {
		VariableMenu.init();
	} );
} )( jQuery );
