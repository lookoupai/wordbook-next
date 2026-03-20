( function() {
	const storageKey = window.wordbookNextTheme && window.wordbookNextTheme.storageKey ? window.wordbookNextTheme.storageKey : 'wordbook-next-reading';
	const root = document.documentElement;
	const body = document.body;
	const overlay = document.querySelector( '[data-wb-overlay]' );
	const navToggle = document.querySelector( '[data-wb-action="toggle-nav"]' );
	const sidebar = document.querySelector( '[data-wb-sidebar]' );
	const mainContent = document.querySelector( '[data-wb-main]' );
	const drawerMedia = window.matchMedia( '(max-width: 980px)' );
	const states = {
		theme: [ 'light', 'sepia', 'night' ],
		font: [ 'sans', 'serif' ],
		scale: [ 0, 1, 2, 3, 4 ]
	};
	const defaults = {
		theme: 'light',
		font: 'sans',
		scale: 2
	};

	let settings = loadSettings();
	let lastFocusedElement = null;

	function isDrawerMode() {
		return drawerMedia.matches;
	}

	function getFocusableElements( container ) {
		if ( ! container ) {
			return [];
		}

		return Array.from(
			container.querySelectorAll( 'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])' )
		).filter( function( element ) {
			return ! element.hasAttribute( 'hidden' ) && element.offsetParent !== null;
		} );
	}

	function syncMainContentState( isOpen ) {
		if ( ! mainContent || ! isDrawerMode() ) {
			return;
		}

		mainContent.setAttribute( 'aria-hidden', isOpen ? 'true' : 'false' );

		if ( 'inert' in mainContent ) {
			mainContent.inert = isOpen;
		}
	}

	function focusSidebar() {
		if ( ! sidebar ) {
			return;
		}

		const focusables = getFocusableElements( sidebar );

		if ( focusables.length > 0 ) {
			focusables[ 0 ].focus();
			return;
		}

		sidebar.focus();
	}

	function getDirectChildBySelector( parent, selector ) {
		return Array.from( parent.children ).find( function( child ) {
			return child.matches( selector );
		} ) || null;
	}

	function isCurrentBranch( item ) {
		return item.classList.contains( 'current-menu-item' ) ||
			item.classList.contains( 'current-menu-ancestor' ) ||
			item.classList.contains( 'current-page-ancestor' ) ||
			item.classList.contains( 'current_page_item' );
	}

	function setBranchState( item, toggle, submenu, expanded ) {
		item.classList.toggle( 'is-expanded', expanded );
		item.classList.toggle( 'is-collapsed', ! expanded );
		submenu.hidden = ! expanded;
		toggle.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
		toggle.setAttribute( 'aria-label', expanded ? '收起子目录' : '展开子目录' );
	}

	function initDocTree() {
		const docTree = document.querySelector( '[data-wb-doc-tree]' );

		if ( ! docTree ) {
			return;
		}

		docTree.querySelectorAll( '.menu-item-has-children, .page_item_has_children' ).forEach( function( item ) {
			const submenu = getDirectChildBySelector( item, '.sub-menu, .children' );
			const link = getDirectChildBySelector( item, 'a' );

			if ( ! submenu || ! link ) {
				return;
			}

			item.classList.add( 'wb-doc-branch' );
			link.classList.add( 'wb-doc-branch__link' );

			let toggle = getDirectChildBySelector( item, '.wb-doc-nav__toggle' );

			if ( ! toggle ) {
				toggle = document.createElement( 'button' );
				toggle.type = 'button';
				toggle.className = 'wb-doc-nav__toggle';
				toggle.innerHTML = '<span aria-hidden="true">▾</span>';
				item.insertBefore( toggle, submenu );
			}

			const expanded = isCurrentBranch( item );
			setBranchState( item, toggle, submenu, expanded );

			toggle.addEventListener( 'click', function( event ) {
				event.preventDefault();
				event.stopPropagation();
				setBranchState( item, toggle, submenu, ! item.classList.contains( 'is-expanded' ) );
			} );
		} );
	}

	function loadSettings() {
		try {
			const saved = JSON.parse( window.localStorage.getItem( storageKey ) || '{}' );
			return {
				theme: states.theme.includes( saved.theme ) ? saved.theme : defaults.theme,
				font: states.font.includes( saved.font ) ? saved.font : defaults.font,
				scale: states.scale.includes( saved.scale ) ? saved.scale : defaults.scale
			};
		} catch ( error ) {
			return { ...defaults };
		}
	}

	function saveSettings() {
		try {
			window.localStorage.setItem( storageKey, JSON.stringify( settings ) );
		} catch ( error ) {
			// 忽略本地存储不可用的情况。
		}
	}

	function applySettings() {
		root.dataset.readingTheme = settings.theme;
		root.dataset.readingFont = settings.font;
		root.dataset.readingScale = String( settings.scale );
	}

	function cycleSetting( key ) {
		const values = states[ key ];
		const currentIndex = values.indexOf( settings[ key ] );
		const nextIndex = currentIndex === values.length - 1 ? 0 : currentIndex + 1;
		settings[ key ] = values[ nextIndex ];
		applySettings();
		saveSettings();
	}

	function setScale( nextScale ) {
		settings.scale = Math.min( 4, Math.max( 0, nextScale ) );
		applySettings();
		saveSettings();
	}

	function toggleNavigation() {
		const isOpen = ! body.classList.contains( 'wb-nav-open' );
		body.classList.toggle( 'wb-nav-open', isOpen );

		if ( overlay ) {
			overlay.hidden = ! isOpen;
		}

		if ( navToggle ) {
			navToggle.setAttribute( 'aria-expanded', String( isOpen ) );
		}

		if ( isOpen ) {
			lastFocusedElement = document.activeElement;
			syncMainContentState( true );
			focusSidebar();
			return;
		}

		syncMainContentState( false );
	}

	function closeNavigation( options ) {
		const shouldRestoreFocus = ! options || false !== options.restoreFocus;

		body.classList.remove( 'wb-nav-open' );

		if ( overlay ) {
			overlay.hidden = true;
		}

		if ( navToggle ) {
			navToggle.setAttribute( 'aria-expanded', 'false' );
		}

		syncMainContentState( false );

		if ( shouldRestoreFocus && lastFocusedElement && 'function' === typeof lastFocusedElement.focus ) {
			lastFocusedElement.focus();
		}
	}

	function trapFocus( event ) {
		if ( ! body.classList.contains( 'wb-nav-open' ) || ! isDrawerMode() || ! sidebar ) {
			return;
		}

		const focusables = getFocusableElements( sidebar );

		if ( 0 === focusables.length ) {
			event.preventDefault();
			sidebar.focus();
			return;
		}

		const first = focusables[ 0 ];
		const last = focusables[ focusables.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	function handleDrawerModeChange() {
		if ( isDrawerMode() ) {
			return;
		}

		closeNavigation( { restoreFocus: false } );
		mainContent && mainContent.removeAttribute( 'aria-hidden' );
		if ( mainContent && 'inert' in mainContent ) {
			mainContent.inert = false;
		}
	}

	document.addEventListener( 'click', function( event ) {
		const actionTarget = event.target.closest( '[data-wb-action]' );

		if ( ! actionTarget ) {
			return;
		}

		switch ( actionTarget.dataset.wbAction ) {
			case 'toggle-nav':
				toggleNavigation();
				break;
			case 'toggle-theme':
				cycleSetting( 'theme' );
				break;
			case 'toggle-font':
				cycleSetting( 'font' );
				break;
			case 'increase-font':
				setScale( settings.scale + 1 );
				break;
			case 'decrease-font':
				setScale( settings.scale - 1 );
				break;
		}
	} );

	document.addEventListener( 'click', function( event ) {
		const navLink = event.target.closest( '.wb-doc-nav a, .wb-utility-nav a' );

		if ( navLink && body.classList.contains( 'wb-nav-open' ) && isDrawerMode() ) {
			closeNavigation( { restoreFocus: false } );
		}
	} );

	if ( overlay ) {
		overlay.addEventListener( 'click', closeNavigation );
	}

	document.addEventListener( 'keydown', function( event ) {
		if ( 'Escape' === event.key && body.classList.contains( 'wb-nav-open' ) ) {
			closeNavigation();
		}

		if ( 'Tab' === event.key ) {
			trapFocus( event );
		}
	} );

	if ( 'function' === typeof drawerMedia.addEventListener ) {
		drawerMedia.addEventListener( 'change', handleDrawerModeChange );
	} else if ( 'function' === typeof drawerMedia.addListener ) {
		drawerMedia.addListener( handleDrawerModeChange );
	}

	initDocTree();

	const currentMenuItem = document.querySelector( '.wb-doc-nav .current-menu-item > a, .wb-doc-nav .current_page_item > a' );

	if ( currentMenuItem ) {
		currentMenuItem.scrollIntoView( {
			block: 'center',
			inline: 'nearest'
		} );
	}

	applySettings();
	handleDrawerModeChange();
}() );
