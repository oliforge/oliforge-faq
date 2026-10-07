jQuery( function ( $ ) {
	var $box = $( '#oliforge-faq-items' );
	var $rows = $box.find( '.oliforge-faq-rows' );
	var tpl = $( '#oliforge-faq-template' ).html();
	var next = $rows.children().length;

	function reindex() {
		$rows.children().each( function ( i ) {
			$( this ).find( '[name]' ).each( function () {
				this.name = this.name.replace( /oliforge_faq_items\[[^\]]*\]/, 'oliforge_faq_items[' + i + ']' );
			} );
		} );
		next = $rows.children().length;
	}

	$( '#oliforge-faq-add' ).on( 'click', function () {
		$rows.append( tpl.replace( /__INDEX__/g, next ) );
		next++;
		$rows.children().last().find( 'input' ).trigger( 'focus' );
	} );

	$box.on( 'click', '.oliforge-faq-remove', function () {
		$( this ).closest( '.oliforge-faq-row' ).remove();
		reindex();
	} );

	$rows.sortable( { handle: '.oliforge-faq-handle', axis: 'y', update: reindex } );
} );
