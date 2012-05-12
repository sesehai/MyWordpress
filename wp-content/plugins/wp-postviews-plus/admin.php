<?php
add_filter('plugin_action_links', 'postviews_plus_links', 10, 2);
function postviews_plus_links($links, $file) {
	if( $file == plugin_basename(dirname(__FILE__) . '/postviews_plus.php' ) ) {
		$links[] = '<a href="options-general.php?page=postviews_plus">' . __('Settings', 'wp-postviews-plus') . '</a>';
	}
	return $links;
}

add_action('admin_menu', 'postviews_plus_admin');
function postviews_plus_admin() {
	add_submenu_page('options-general.php', 'WP-PostViews Plus', __('PostViews+', 'wp-postviews-plus'), 'manage_options', 'postviews_plus', 'postviews_plus_set');
}

function postviews_plus_set(){
global $views_options, $wpdb;
$views_settings = array('PVP_options', 'widget_views-plus');
$views_postmetas = array('views', 'bot_views');

if( !empty($_POST['Update']) ) {
	$views_options = array();
	$views_options['count'] = intval($_POST['views_count']);
	$views_options['check_reflash'] = intval($_POST['views_check_reflash']);
	$views_options['timeout'] = intval($_POST['views_timeout']);
	$views_options['display_home'] = intval($_POST['views_display_home']);
	$views_options['display_single'] = intval($_POST['views_display_single']);
	$views_options['display_page'] = intval($_POST['views_display_page']);
	$views_options['display_archive'] = intval($_POST['views_display_archive']);
	$views_options['display_search'] = intval($_POST['views_display_search']);
	$views_options['display_other'] = intval($_POST['views_display_other']);
	$views_options['template'] = trim($_POST['views_template_template']);
	$views_options['user_template'] = trim($_POST['views_template_user_template']);
	$views_options['bot_template'] = trim($_POST['views_template_bot_template']);
	$views_options['most_viewed_template'] = trim($_POST['views_template_most_viewed']);
	$botagent = explode("\r\n", trim($_POST['views_botagent']));
	if( !is_array($botagent) ) {
		$botagent = explode("\n", trim($_POST['views_botagent']));
	}
	if( !is_array($botagent) ) {
		$botagent = array('bot', 'spider', 'slurp');
	}
	$views_options['botagent'] = $botagent;
	if( $views_options['check_reflash'] ) {
		$wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->postviews_plus_reflash . '` (
			`post_id` BIGINT(20) unsigned NOT NULL DEFAULT "0",
			`user_ip` VARCHAR(100) NOT NULL DEFAULT "",
			`look_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`post_id`, `user_ip`),
			INDEX (`look_time`))');
	} else {
		$wpdb->query('DROP TABLE `' . $wpdb->postviews_plus_reflash . '`');
	}
	if( update_option('PVP_options', $views_options) ) {
		$text = '<span style="color:green">' . __('Updated Options Success', 'wp-postviews-plus') . '</span>';
	}
}

if( !empty($_POST['Default']) ) {
	delete_option('PVP_options');
	$views_options = array();
	$views_options['count'] = 1;
	$views_options['check_reflash'] = 0;
	$views_options['timeout'] = 300;
	$views_options['display_home'] = 0;
	$views_options['display_single'] = 0;
	$views_options['display_page'] = 0;
	$views_options['display_archive'] = 0;
	$views_options['display_search'] = 0;
	$views_options['display_other'] = 0;
	$views_options['template'] = '%VIEW_COUNT% ' . __('views', 'wp-postviews-plus');
	$views_options['user_template'] = '%VIEW_COUNT% ' . __('user views', 'wp-postviews-plus');
	$views_options['bot_template'] = '%VIEW_COUNT% ' . __('bot views', 'wp-postviews-plus');
	$views_options['botagent'] = array('bot', 'spider', 'slurp');
	$views_options['most_viewed_template'] = '<li><a href="%POST_URL%"  title="%POST_TITLE%">%POST_TITLE%</a> - %VIEW_COUNT% ' . __('views', 'wp-postviews-plus') . '</li>';
	if( update_option('PVP_options', $views_options) ) {
		$text = '<span style="color:green">' . __('Reset Optionsto Default Success', 'wp-postviews-plus') . '</span>';
	}
}

if( !empty($_POST['do']) ) {
	switch($_POST['do']) {		
		case __('UNINSTALL WP-PostViews Plus', 'wp-postviews-plus') :
			if( trim($_POST['uninstall_views_yes']) == 'yes' ) {
				echo '<div id="message" class="updated fade">';
				if( $wpdb->query('DROP TABLE `' . $wpdb->postviews_plus . '`') ) {
					echo '<p><span style="color:green">';
					printf(__('Post Meta Key \'%s\' has been deleted.', 'wp-postviews-plus'), "<strong><em>{$wpdb->postviews_plus}</em></strong>");
					echo '</span></p>';
				} else {
					echo '<p><span style="color:red">';
					printf(__('Error deleting Post Meta Key \'%s\'.', 'wp-postviews-plus'), "<strong><em>{$wpdb->postviews_plus}</em></strong>");
					echo '</span></p>';
				}
				if( $wpdb->query('DROP TABLE `' . $wpdb->postviews_plus_reflash . '`') ) {
					echo '<p><span style="color:green">';
					printf(__('Post Meta Key \'%s\' has been deleted.', 'wp-postviews-plus'), "<strong><em>{$wpdb->postviews_plus_reflash}</em></strong>");
					echo '</span></p>';
				} else {
					echo '<p><span style="color:red">';
					printf(__('Error deleting Post Meta Key \'%s\'.', 'wp-postviews-plus'), "<strong><em>{$wpdb->postviews_plus_reflash}</em></strong>");
					echo '</span></p>';
				}
				echo '<p>';
				foreach($views_settings as $setting) {
					if( delete_option($setting) ) {
						echo '<span style="color:green">';
						printf(__('Setting Key \'%s\' has been deleted.', 'wp-postviews-plus'), "<strong><em>{$setting}</em></strong>");
						echo '</span><br />';
					} else {
						echo '<span style="color:red">';
						printf(__('Error deleting Setting Key \'%s\'.', 'wp-postviews-plus'), "<strong><em>{$setting}</em></strong>");
						echo '</span><br />';
					}
				}
				echo '</p>';
				echo '<p>';
				foreach($views_postmetas as $postmeta) {
					$remove_postmeta = $wpdb->query('DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_key="' . $postmeta . '"');
					if( $remove_postmeta ) {
						echo '<span style="color:green">';
						printf(__('Post Meta Key \'%s\' has been deleted.', 'wp-postviews-plus'), "<strong><em>{$postmeta}</em></strong>");
						echo '</span><br />';
					} else {
						echo '<span style="color:red">';
						printf(__('Error deleting Post Meta Key \'%s\'.', 'wp-postviews-plus'), "<strong><em>{$postmeta}</em></strong>");
						echo '</span><br />';
					}
				}
				echo '</p>';
				echo '</div>'; 
				$deactivate_url = 'plugins.php?action=deactivate&amp;plugin=wp-postviews-plus/postviews_plus.php';
				if( function_exists('wp_nonce_url') ) { 
					$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_wp-postviews-plus/postviews_plus.php');
				}
				echo '<div class="wrap">';
				echo '<h2>' . __('Uninstall WP-PostViews Plus', 'wp-postviews-plus') . '</h2>';
				echo '<p><strong>' . sprintf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And WP-PostViews Plus Will Be Deactivated Automatically.', 'wp-postviews-plus'), $deactivate_url) . '</strong></p>';
				echo '</div>';
				return true;
			}
			break;
	}
}
?>
<?php if(!empty($text)) { echo '<div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap"><form method="post" action="">
	<?php screen_icon(); ?>
	<h2><?php _e('Post Views Plus Options', 'wp-postviews-plus'); ?></h2>
	<table class="widefat">
		<thead><tr>
			<th colspan="3"><?php _e('Basic Options', 'wp-postviews-plus'); ?></th>
		</tr></thead>
		<tr>
			<td valign="top" width="30%" colspan="2"><strong><?php _e('Count Views From:', 'wp-postviews-plus'); ?></strong></td>
			<td valign="top">
				<select name="views_count" size="1">
					<option value="0"<?php selected('0', $views_options['count']); ?>><?php _e('Everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['count']); ?>><?php _e('Guests Only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['count']); ?>><?php _e('Registered Users Only', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" rowspan="2"><strong><?php _e('Reflash check:', 'wp-postviews-plus'); ?></strong></td>
			<td></td>
			<td valign="top">
				<select name="views_check_reflash" size="1">
					<option value="0"<?php selected('0', $views_options['check_reflash']); ?>><?php _e('Close', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['check_reflash']); ?>><?php _e('Open', 'wp-postviews-plus'); ?></option>
				</select>
				<?php _e('Check is based on IP.', 'wp-postviews-plus'); ?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Reflash timeout:', 'wp-postviews-plus'); ?></td>
			<td valign="top">
				<input type="text" id="views_timeout" name="views_timeout" size="10" value="<?php echo($views_options['timeout']); ?>" /><?php _e('second.', 'wp-postviews-plus'); ?>
			</td>
		</tr>
		<tr>
			<td valign="top" rowspan="3">
				<strong><?php _e('Views Template:', 'wp-postviews-plus'); ?></strong>
			</td>
			<td><?php _e('All views:', 'wp-postviews-plus'); ?></td>
			<td valign="top">
				<input type="text" id="views_template_template" name="views_template_template" size="70" value="<?php echo htmlspecialchars(stripslashes($views_options['template'])); ?>" /><br />
				<?php _e('Allowed Variables:', 'wp-postviews-plus'); ?> - %VIEW_COUNT%
			</td>
		</tr>
		<tr>
			<td><?php _e('Only user views:', 'wp-postviews-plus'); ?></td>
			<td valign="top">
				<input type="text" id="views_template_user_template" name="views_template_user_template" size="70" value="<?php echo htmlspecialchars(stripslashes($views_options['user_template'])); ?>" /><br />
				<?php _e('Allowed Variables:', 'wp-postviews-plus'); ?> - %VIEW_COUNT%
			</td>
		</tr>
		<tr>
			<td><?php _e('Only bot views:', 'wp-postviews-plus'); ?></td>
			<td valign="top">
				<input type="text" id="views_template_bot_template" name="views_template_bot_template" size="70" value="<?php echo htmlspecialchars(stripslashes($views_options['bot_template'])); ?>" /><br />
				<?php _e('Allowed Variables:', 'wp-postviews-plus'); ?> - %VIEW_COUNT%
			</td>
		</tr>
		<tr>
			<td valign="top"colspan="2">
				<strong><?php _e('Most Viewed Template:', 'wp-postviews-plus'); ?></strong>
			</td>
			<td valign="top">
				<textarea cols="65" rows="4"  id="views_template_most_viewed" name="views_template_most_viewed"><?php echo htmlspecialchars(stripslashes($views_options['most_viewed_template'])); ?></textarea><br />
				<?php _e('Allowed Variables:', 'wp-postviews-plus'); ?><br /> - %VIEW_COUNT% - %POST_TITLE% - %POST_EXCERPT% - %POST_CONTENT% - %POST_DATE% - %POST_URL%
			</td>
		</tr>
		<tr>
			<td valign="top"colspan="2">
				<strong><?php _e('BOT User_agent:', 'wp-postviews-plus'); ?></strong>
			</td>
			<td valign="top">
				<textarea cols="30" rows="<?php echo(count($views_options['botagent'])+1); ?>"  id="views_botagent" name="views_botagent"><?php echo htmlspecialchars(stripslashes(implode("\n",$views_options['botagent']))); ?></textarea><br />
				<?php _e('For each BOT user_agent one line.', 'wp-postviews-plus'); ?>
			</td>
		</tr>
	</table>
	<p>&nbsp;</p>
	<table class="widefat">
		<thead><tr>
			<th colspan="3"><?php _e('Display Options', 'wp-postviews-plus'); ?></th>
		</tr></thead>		
		<tr>
			<td valign="top" width="30%"><strong><?php _e('Home Page:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_home" size="1">
					<option value="0"<?php selected('0', $views_options['display_home']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_home']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_home']); ?>><?php _e('Don\'t display on home page', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
			<td rowspan="6" width="25%"><?php _e('These options specify where the view counts should be displayed and to whom.<br />Note that the theme files must contain a call to <code>the_views()</code> in order for any view count to be displayed.', 'wp-postviews-plus'); ?></td>
		</tr>
		<tr>
			<td valign="top"><strong><?php _e('Singe Posts:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_single" size="1">
					<option value="0"<?php selected('0', $views_options['display_single']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_single']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_single']); ?>><?php _e('Don\'t display on single posts', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top"><strong><?php _e('Pages:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_page" size="1">
					<option value="0"<?php selected('0', $views_options['display_page']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_page']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_page']); ?>><?php _e('Don\'t display on pages', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top"><strong><?php _e('Archive Pages:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_archive" size="1">
					<option value="0"<?php selected('0', $views_options['display_archive']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_archive']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_archive']); ?>><?php _e('Don\'t display on archive pages', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top"><strong><?php _e('Search Pages:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_search" size="1">
					<option value="0"<?php selected('0', $views_options['display_search']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_search']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_search']); ?>><?php _e('Don\'t display on search pages', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top"><strong><?php _e('Other Pages:', 'wp-postviews-plus'); ?></strong></td>
			<td>
				<select name="views_display_other" size="1">
					<option value="0"<?php selected('0', $views_options['display_other']); ?>><?php _e('Display to everyone', 'wp-postviews-plus'); ?></option>
					<option value="1"<?php selected('1', $views_options['display_other']); ?>><?php _e('Display to registered users only', 'wp-postviews-plus'); ?></option>
					<option value="2"<?php selected('2', $views_options['display_other']); ?>><?php _e('Don\'t display on other pages', 'wp-postviews-plus'); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Update" class="button-primary" value="<?php _e('Save Changes', 'wp-postviews-plus'); ?>" />
		<input type="submit" name="Default" class="button-primary" value="<?php _e('Reset to Default', 'wp-postviews-plus'); ?>" />
	</p>
</form></div>
<div class="wrap"><form method="post" action="">
	<h3><?php _e('Uninstall WP-PostViews Plus', 'wp-postviews-plus'); ?></h3>
	<p><?php _e('Deactivating WP-PostViews Plus plugin does not remove any data that may have been created, such as the views data. To completely remove this plugin, you can uninstall it here.', 'wp-postviews-plus'); ?></p>
	<p style="color: red"><strong><?php _e('WARNING:', 'wp-postviews-plus'); ?></strong><br /><?php _e('Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.', 'wp-postviews-plus'); ?></p>
	<p style="color: red"><?php printf(__('The database table <strong>%s</strong> will be DELETED.', 'wp-postviews-plus'), $wpdb->postviews_plus); ?></p>
	<p style="color: red"><?php printf(__('The database table <strong>%s</strong> will be DELETED.', 'wp-postviews-plus'), $wpdb->postviews_plus_reflash); ?></p>
	<p style="color: red"><strong><?php _e('The following WordPress Options/PostMetas will be DELETED:', 'wp-postviews-plus'); ?></strong></p>
	<table class="widefat">
		<thead><tr>
			<th><?php _e('WordPress Options', 'wp-postviews-plus'); ?></th>
			<th><?php _e('WordPress PostMetas', 'wp-postviews-plus'); ?></th>
		</tr></thead>
		<tr>
			<td valign="top">
				<ol>
				<?php
					foreach($views_settings as $settings) {
						echo '<li>'.$settings.'</li>'."\n";
					}
				?>
				</ol>
			</td>
			<td valign="top" class="alternate">
				<ol>
				<?php
					foreach($views_postmetas as $postmeta) {
						echo '<li>'.$postmeta.'</li>'."\n";
					}
				?>
				</ol>
			</td>
		</tr>
	</table>
	<p style="text-align:center"><input type="checkbox" name="uninstall_views_yes" value="yes" />&nbsp;<?php _e('Yes', 'wp-postviews-plus'); ?></p>
	<p style="text-align:center"><input type="submit" name="do" value="<?php _e('UNINSTALL WP-PostViews Plus', 'wp-postviews-plus'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Uninstall WP-PostViews Plus From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.', 'wp-postviews-plus'); ?>')" /></p>
</form></div>
<div class="wrap">
	<h3><?php _e('Thank', 'wp-postviews-plus'); ?></h3>
	<p><?php _e('Translation contributors', 'wp-postviews-plus'); ?>：</p>
	<p>zh_CN(简体中文) By ddbiz</p>
</div>
<?php
}
?>