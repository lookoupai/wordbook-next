( function() {
	const storageKey = window.wordbookNextTheme && window.wordbookNextTheme.storageKey ? window.wordbookNextTheme.storageKey : 'wordbook-next-reading';
	const root = document.documentElement;
	const body = document.body;
	const overlay = document.querySelector( '[data-wb-overlay]' );
	const navToggle = document.querySelector( '[data-wb-action="toggle-nav"]' );
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
	}

	function closeNavigation() {
		body.classList.remove( 'wb-nav-open' );

		if ( overlay ) {
			overlay.hidden = true;
		}

		if ( navToggle ) {
			navToggle.setAttribute( 'aria-expanded', 'false' );
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

	if ( overlay ) {
		overlay.addEventListener( 'click', closeNavigation );
	}

	document.addEventListener( 'keydown', function( event ) {
		if ( 'Escape' === event.key ) {
			closeNavigation();
		}
	} );

	const currentMenuItem = document.querySelector( '.wb-doc-nav .current-menu-item > a, .wb-doc-nav .current_page_item > a' );

	if ( currentMenuItem ) {
		currentMenuItem.scrollIntoView( {
			block: 'center',
			inline: 'nearest'
		} );
	}

	applySettings();
}() );
