( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useSelect = wp.data.useSelect;
	var ServerSideRender = wp.serverSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var C = wp.components;

	wp.blocks.registerBlockType( 'oliforge/faq', {
		edit: function ( props ) {
			var attrs = props.attributes;
			var faqs = useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'postType', 'oliforge_faq', { per_page: 100, status: 'publish', orderby: 'title', order: 'asc' } );
			}, [] );

			var options = [ { label: __( '— Select FAQ —', 'oliforge-faq' ), value: 0 } ].concat(
				( faqs || [] ).map( function ( f ) {
					return { label: ( f.title && f.title.rendered ) || '#' + f.id, value: f.id };
				} )
			);

			var panel = el(
				InspectorControls,
				null,
				el(
					C.PanelBody,
					{ title: __( 'FAQ settings', 'oliforge-faq' ) },
					el( C.SelectControl, {
						label: __( 'FAQ', 'oliforge-faq' ),
						value: attrs.faqId,
						options: options,
						onChange: function ( v ) { props.setAttributes( { faqId: parseInt( v, 10 ) || 0 } ); },
					} ),
					el( C.SelectControl, {
						label: __( 'Initially open', 'oliforge-faq' ),
						value: attrs.open,
						options: [
							{ label: __( 'None', 'oliforge-faq' ), value: 'none' },
							{ label: __( 'First item', 'oliforge-faq' ), value: 'first' },
							{ label: __( 'All items', 'oliforge-faq' ), value: 'all' },
						],
						onChange: function ( v ) { props.setAttributes( { open: v } ); },
					} ),
					el( C.ToggleControl, {
						label: __( 'Output FAQPage schema', 'oliforge-faq' ),
						checked: attrs.schema,
						onChange: function ( v ) { props.setAttributes( { schema: v } ); },
					} )
				)
			);

			var body = attrs.faqId
				? el( ServerSideRender, { block: 'oliforge/faq', attributes: attrs } )
				: el(
					C.Placeholder,
					{ icon: 'editor-help', label: __( 'OliForge FAQ', 'oliforge-faq' ) },
					el( C.SelectControl, {
						value: attrs.faqId,
						options: options,
						onChange: function ( v ) { props.setAttributes( { faqId: parseInt( v, 10 ) || 0 } ); },
					} )
				);

			return el( 'div', useBlockProps(), panel, body );
		},
		save: function () { return null; },
	} );
} )( window.wp );
