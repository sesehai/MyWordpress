<?php
session_start();

require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}
require_once("tencent/Weibo.php");

$tencent_app_key = get_option('TENCENT_APP_KEY');
$tencent_app_secret = get_option('TENCENT_APP_SECRET');

Pinglunla_OpenSDK_Tencent_Weibo::init($tencent_app_key, $tencent_app_secret);

if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
    $response = Pinglunla_OpenSDK_Tencent_Weibo::getAccessToken($_GET['oauth_verifier']);
    update_option('tencent_openid', $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OPENID]);
    update_option('tencent_openkey', $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OPENKEY]);
    update_option('tencent_access_token', $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::ACCESS_TOKEN]);
    update_option('tencent_token_secret', $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET]);
    update_option('pinglunla_tencent', 1);
}
?>
<script>
    window.opener.jQuery("#pinglunla_tencent_bind_content").html("");
	window.opener.jQuery("#pinglunla_tencentconnectWrapper").removeClass("connectTencent").addClass("disconnectTencent");
    window.opener.jQuery("#pinglunla_tencentconnectWrapper").attr("value", "1"); 
    window.close();
</script>
