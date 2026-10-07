<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OliForge_FAQ_Plugin {

	const POST_TYPE = 'oliforge_faq';
	const META_KEY  = '_oliforge_faq_items';
	const NONCE     = 'oliforge_faq_save';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_items' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'admin_notices', array( $this, 'render_header' ) );
		add_shortcode( 'oliforge_faq', array( $this, 'shortcode' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'elementor/widgets/register', array( $this, 'register_elementor_widget' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_elementor_category' ) );
	}

	/* ---------- Post type ---------- */

	public function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => __( 'FAQ', 'oliforge-faq' ),
					'singular_name' => __( 'FAQ', 'oliforge-faq' ),
					'add_new_item'  => __( 'Add New FAQ', 'oliforge-faq' ),
					'edit_item'     => __( 'Edit FAQ', 'oliforge-faq' ),
					'new_item'      => __( 'New FAQ', 'oliforge-faq' ),
					'all_items'     => __( 'All FAQs', 'oliforge-faq' ),
					'search_items'  => __( 'Search FAQs', 'oliforge-faq' ),
					'not_found'     => __( 'No FAQs found.', 'oliforge-faq' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-editor-help',
				'menu_position'       => 25,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
			)
		);
	}

	/* ---------- Data ---------- */

	public static function get_items( $post_id ) {
		$items = get_post_meta( $post_id, self::META_KEY, true );
		if ( ! is_array( $items ) ) {
			return array();
		}
		return array_values(
			array_filter(
				$items,
				static function ( $i ) {
					return is_array( $i ) && ! empty( $i['q'] );
				}
			)
		);
	}

	/* ---------- Meta box ---------- */

	public function add_meta_boxes() {
		add_meta_box( 'oliforge_faq_items', __( 'Questions & Answers', 'oliforge-faq' ), array( $this, 'render_items_box' ), self::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'oliforge_faq_usage', __( 'Usage', 'oliforge-faq' ), array( $this, 'render_usage_box' ), self::POST_TYPE, 'side', 'default' );
	}

	private function shortcode_markup( $post_id ) {
		$code = sprintf( '[oliforge_faq id="%d"]', (int) $post_id );
		return '<code class="oliforge-faq-shortcode">' . esc_html( $code ) . '</code>'
			. '<button type="button" class="button oliforge-faq-copy" data-copy="' . esc_attr( $code ) . '" title="' . esc_attr__( 'Copy shortcode', 'oliforge-faq' ) . '">'
			. '<img src="' . esc_url( OLIFORGE_FAQ_URL . 'assets/img/icon-copy.png' ) . '" alt="' . esc_attr__( 'Copy', 'oliforge-faq' ) . '" width="18" height="18" /></button>';
	}

	public function render_header() {
		$screen = get_current_screen();
		if ( ! $screen || self::POST_TYPE !== $screen->post_type || ! in_array( $screen->base, array( 'edit', 'post' ), true ) ) {
			return;
		}
		$sub = 'edit' === $screen->base ? __( 'List', 'oliforge-faq' ) : __( 'Editor', 'oliforge-faq' );
		?>
		<div class="oliforge-header">
			<div class="oliforge-header__brand">
				<img class="oliforge-header__logo" src="<?php echo esc_url( OLIFORGE_FAQ_URL . 'assets/img/OliForge_logo.png' ); ?>" alt="<?php esc_attr_e( 'OliForge', 'oliforge-faq' ); ?>" width="64" height="64" />
				<div class="oliforge-header__brandtext">
					<span class="oliforge-header__name"><?php esc_html_e( 'OliForge', 'oliforge-faq' ); ?></span>
					<span class="oliforge-header__tagline"><?php esc_html_e( 'Engineering without complexity.', 'oliforge-faq' ); ?></span>
				</div>
			</div>
			<div class="oliforge-header__title">
				<h1><?php esc_html_e( 'FAQ', 'oliforge-faq' ); ?> <span class="oliforge-accent"><?php echo esc_html( $sub ); ?></span></h1>
				<span class="oliforge-badge oliforge-badge--version">v<?php echo esc_html( OLIFORGE_FAQ_VERSION ); ?></span>
			</div>
		</div>
		<?php
	}

	public function render_usage_box( $post ) {
		if ( 'auto-draft' === $post->post_status ) {
			echo '<p>' . esc_html__( 'Save the FAQ to get its shortcode.', 'oliforge-faq' ) . '</p>';
			return;
		}
		printf( '<p>%s</p>', $this->shortcode_markup( $post->ID ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
		echo '<p class="description">' . esc_html__( 'Or use the "FAQ" block in Gutenberg / the "OliForge FAQ" widget in Elementor.', 'oliforge-faq' ) . '</p>';
	}

	public function render_items_box( $post ) {
		wp_nonce_field( self::NONCE, self::NONCE . '_nonce' );
		$items = self::get_items( $post->ID );
		?>
		<div id="oliforge-faq-items" class="oliforge-faq-admin">
			<div class="oliforge-faq-rows">
				<?php foreach ( $items as $i => $item ) : ?>
					<?php $this->render_row( $i, $item['q'], isset( $item['a'] ) ? $item['a'] : '' ); ?>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button button-primary" id="oliforge-faq-add"><?php esc_html_e( 'Add question', 'oliforge-faq' ); ?></button></p>
			<script type="text/template" id="oliforge-faq-template">
				<?php $this->render_row( '__INDEX__', '', '' ); ?>
			</script>
		</div>
		<?php
	}

	private function render_row( $index, $q, $a ) {
		?>
		<div class="oliforge-faq-row">
			<div class="oliforge-faq-row__head">
				<span class="oliforge-faq-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'oliforge-faq' ); ?>"></span>
				<input type="text" class="widefat" name="oliforge_faq_items[<?php echo esc_attr( $index ); ?>][q]" value="<?php echo esc_attr( $q ); ?>" placeholder="<?php esc_attr_e( 'Question', 'oliforge-faq' ); ?>">
				<button type="button" class="button-link button-link-delete oliforge-faq-remove"><?php esc_html_e( 'Remove', 'oliforge-faq' ); ?></button>
			</div>
			<textarea class="widefat" rows="4" name="oliforge_faq_items[<?php echo esc_attr( $index ); ?>][a]" placeholder="<?php esc_attr_e( 'Answer (basic HTML allowed)', 'oliforge-faq' ); ?>"><?php echo esc_textarea( $a ); ?></textarea>
		</div>
		<?php
	}

	public function save_items( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE . '_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE . '_nonce' ] ) ), self::NONCE ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$clean = array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
		$raw = isset( $_POST['oliforge_faq_items'] ) ? wp_unslash( $_POST['oliforge_faq_items'] ) : array();
		if ( is_array( $raw ) ) {
			foreach ( $raw as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$q = isset( $row['q'] ) ? sanitize_text_field( $row['q'] ) : '';
				if ( '' === $q ) {
					continue;
				}
				$clean[] = array(
					'q' => $q,
					'a' => isset( $row['a'] ) ? wp_kses_post( $row['a'] ) : '',
				);
			}
		}
		update_post_meta( $post_id, self::META_KEY, $clean );
	}

	public function admin_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || self::POST_TYPE !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php', 'edit.php' ), true ) ) {
			return;
		}
		wp_enqueue_style( 'oliforge-faq-admin', OLIFORGE_FAQ_URL . 'assets/css/admin.css', array(), OLIFORGE_FAQ_VERSION );
		wp_enqueue_script( 'oliforge-faq-copy', OLIFORGE_FAQ_URL . 'assets/js/copy.js', array(), OLIFORGE_FAQ_VERSION, true );
		if ( 'edit.php' === $hook ) {
			return;
		}
		wp_enqueue_script( 'oliforge-faq-admin', OLIFORGE_FAQ_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), OLIFORGE_FAQ_VERSION, true );
	}

	/* ---------- List table ---------- */

	public function columns( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['oliforge_faq_count'] = __( 'Questions', 'oliforge-faq' );
				$new['oliforge_faq_code']  = __( 'Shortcode', 'oliforge-faq' );
			}
		}
		return $new;
	}

	public function column_content( $col, $post_id ) {
		if ( 'oliforge_faq_count' === $col ) {
			echo (int) count( self::get_items( $post_id ) );
		} elseif ( 'oliforge_faq_code' === $col ) {
			echo $this->shortcode_markup( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
		}
	}

	/* ---------- Frontend ---------- */

	public function register_assets() {
		wp_register_style( 'oliforge-faq', OLIFORGE_FAQ_URL . 'assets/css/frontend.css', array(), OLIFORGE_FAQ_VERSION );
	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'     => 0,
				'open'   => 'none',
				'schema' => 'yes',
			),
			$atts,
			'oliforge_faq'
		);
		return self::render( (int) $atts['id'], $atts['open'], 'no' !== strtolower( $atts['schema'] ) );
	}

	/**
	 * @param int    $post_id FAQ post ID.
	 * @param string $open    none|first|all.
	 * @param bool   $schema  Output FAQPage JSON-LD.
	 */
	public static function render( $post_id, $open = 'none', $schema = true ) {
		$post = $post_id ? get_post( $post_id ) : null;
		if ( ! $post || self::POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
			return '';
		}
		$items = self::get_items( $post_id );
		if ( ! $items ) {
			return '';
		}

		wp_enqueue_style( 'oliforge-faq' );

		$html = '<div class="oliforge-faq" data-faq-id="' . (int) $post_id . '">';
		$ld   = array();
		foreach ( $items as $i => $item ) {
			$is_open = 'all' === $open || ( 'first' === $open && 0 === $i );
			$answer  = wpautop( wp_kses_post( isset( $item['a'] ) ? $item['a'] : '' ) );
			$html   .= '<details class="oliforge-faq__item"' . ( $is_open ? ' open' : '' ) . '>';
			$html   .= '<summary class="oliforge-faq__question">' . esc_html( $item['q'] ) . '</summary>';
			$html   .= '<div class="oliforge-faq__answer">' . $answer . '</div>';
			$html   .= '</details>';
			$ld[]    = array(
				'@type'          => 'Question',
				'name'           => $item['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
		$html .= '</div>';

		if ( $schema ) {
			static $schema_done = false; // Google expects a single FAQPage per URL.
			if ( ! $schema_done ) {
				$schema_done = true;
				$html       .= '<script type="application/ld+json">' . wp_json_encode(
					array(
						'@context'   => 'https://schema.org',
						'@type'      => 'FAQPage',
						'mainEntity' => $ld,
					),
					JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
				) . '</script>';
			}
		}
		return $html;
	}

	/* ---------- Gutenberg ---------- */

	public function register_block() {
		wp_register_script(
			'oliforge-faq-block-editor',
			OLIFORGE_FAQ_URL . 'blocks/faq/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-server-side-render', 'wp-i18n' ),
			OLIFORGE_FAQ_VERSION,
			true
		);
		register_block_type(
			OLIFORGE_FAQ_PATH . 'blocks/faq',
			array( 'render_callback' => array( $this, 'render_block' ) )
		);
	}

	public function render_block( $attrs ) {
		$attrs = wp_parse_args(
			$attrs,
			array(
				'faqId'  => 0,
				'open'   => 'none',
				'schema' => true,
			)
		);
		$html = self::render( (int) $attrs['faqId'], $attrs['open'], (bool) $attrs['schema'] );
		return $html ? '<div ' . get_block_wrapper_attributes() . '>' . $html . '</div>' : '';
	}

	/* ---------- Elementor ---------- */

	public function register_elementor_category( $manager ) {
		$manager->add_category( 'oliforge', array( 'title' => 'OliForge', 'icon' => 'eicon-help-o' ) );
	}

	public function register_elementor_widget( $widgets_manager ) {
		require_once OLIFORGE_FAQ_PATH . 'includes/class-oliforge-faq-elementor-widget.php';
		$widgets_manager->register( new OliForge_FAQ_Elementor_Widget() );
	}
}
