<?php
/**
 * Plugin Name: OliForge FAQ
 * Description: FAQ sets as a custom post type. Each FAQ post holds questions and answers; output via shortcode, Gutenberg block or Elementor widget, with FAQPage schema.
 * Version:           0.1.0
 * Author:  OliForge™
 * Text Domain: oliforge-faq
 * Domain Path: /languages
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OLIFORGE_FAQ_VERSION = '0.1.0';
define( 'OLIFORGE_FAQ_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLIFORGE_FAQ_URL', plugin_dir_url( __FILE__ ) );

require_once OLIFORGE_FAQ_PATH . 'includes/class-oliforge-faq-plugin.php';

OliForge_FAQ_Plugin::instance();

register_activation_hook(
	__FILE__,
	static function () {
		OliForge_FAQ_Plugin::instance()->register_post_type();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
