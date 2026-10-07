<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 *
 * FAQ sets are content, so they are kept unless the site owner opts in by defining
 * OLIFORGE_FAQ_REMOVE_DATA as true in wp-config.php.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'OLIFORGE_FAQ_REMOVE_DATA' ) || true !== OLIFORGE_FAQ_REMOVE_DATA ) {
	return;
}

$oliforge_faq_ids = get_posts(
	array(
		'post_type'      => 'oliforge_faq',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);

foreach ( $oliforge_faq_ids as $oliforge_faq_id ) {
	wp_delete_post( $oliforge_faq_id, true );
}
