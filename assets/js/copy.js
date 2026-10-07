document.addEventListener( 'click', function ( e ) {
	var btn = e.target.closest( '.oliforge-faq-copy' );
	if ( ! btn || ! navigator.clipboard ) {
		return;
	}
	navigator.clipboard.writeText( btn.getAttribute( 'data-copy' ) ).then( function () {
		var old = btn.title;
		btn.title = '✓';
		setTimeout( function () { btn.title = old; }, 1200 );
	} );
} );
