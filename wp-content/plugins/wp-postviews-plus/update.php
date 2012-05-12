<?php
add_action('init', 'WP_pvp_plugin_update');
function WP_pvp_plugin_update() {
	$version = get_option('PVP_version', '1.2.8.1');
	if( version_compare($version, '1.2.9', '<') ) {
		global $wpdb;
		$wpdb->query('TRUNCATE `' . $wpdb->postviews_plus . '`');
		$wpdb->query('ALTER TABLE `' . $wpdb->postviews_plus . '` ADD `add_time` INT UNSIGNED NOT NULL AFTER `count_id`');
		update_option('PVP_version', '1.2.9');
	}
}
?>