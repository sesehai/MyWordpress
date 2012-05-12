<?php

session_start();

require_once("weibo1.0/weibooauth.php");
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

$sina_app_key =  get_option('SINA_APP_KEY');
$sina_app_secret =  get_option('SINA_APP_SECRET');


/* 
 * Get URL of User's Sites
 */
$callback = get_bloginfo('wpurl') . "/wp-content/plugins/pinglunla/sina_callback.php";

$o = new Pinglunla_WeiboOAuth($sina_app_key, $sina_app_secret);
$keys = $o->getRequestToken();

$sina_ourl = $o->getAuthorizeURL($keys['oauth_token'], false, $callback); 
$_SESSION['key'] = $keys;

header("Location: " . $sina_ourl);

?>
