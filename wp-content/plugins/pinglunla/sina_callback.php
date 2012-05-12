<?php

header('Content-type: text/html; charset=utf-8');
session_start();
require_once( './weibo1.0/weibooauth.php' );
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

$sina_app_key = get_option('SINA_APP_KEY');
$sina_app_secret = get_option('SINA_APP_SECRET');

/* Get access_token */
if (!empty($_REQUEST["oauth_verifier"])) {
    $sina_o = new Pinglunla_WeiboOAuth($sina_app_key, $sina_app_secret, $_SESSION['key']['oauth_token'], 
                                $_SESSION['key']['oauth_token_secret']);
    $_SESSION['token'] = $sina_o->getAccessToken($_REQUEST['oauth_verifier']);

    update_option("SINA_APP_TOKEN", $_SESSION['token']);
    update_option("pinglunla_sina", 1);

?>
<script>
    //window.opener.jQuery("#pinglunla_sina_bind_content").html("已绑定，点击按钮解除");
	window.opener.jQuery("#pinglunla_sina_bind_content").html("");
	window.opener.jQuery("#pinglunla_sinaconnectWrapper").removeClass("connectSINA").addClass("disconnectSINA");
    window.opener.jQuery("#pinglunla_sinaconnectWrapper").attr("value", "1"); 
    window.close();
</script>

<?php
} else {
    echo "<h1>授权失败，请重新绑定</h1>";
}
?>
