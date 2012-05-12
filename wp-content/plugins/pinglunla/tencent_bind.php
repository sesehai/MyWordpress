<?php
session_start();
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

require_once('tencent/Weibo.php');

if (isset($_GET['TENCENTUNBINDED'])) {
    update_option('pinglunla_tencent', 0);
?>
    <script>
        //window.opener.jQuery("#pinglunla_tencent_bind_content").html("点击按钮绑定");
        //window.opener.jQuery("#pinglunla_tencentconnectWrapper").attr("value", "0"); 
        //window.close();
		
		window.opener.jQuery("#pinglunla_tencent_bind_content").html("");
		window.opener.jQuery("#pinglunla_tencentconnectWrapper").removeClass("disconnectTencent").addClass("connectTencent");
		window.opener.jQuery("#pinglunla_tencentconnectWrapper").attr("value", "0"); 
		window.close();
    </script>
<?php
    exit();
}

if (isset($_GET['TENCENT_APP_KEY']) && isset($_GET['TENCENT_APP_SECRET'])) {
    update_option("TENCENT_APP_KEY", $_GET['TENCENT_APP_KEY']);
    update_option("TENCENT_APP_SECRET", $_GET['TENCENT_APP_SECRET']);
    pinglunla_tencent_auth($_GET['TENCENT_APP_KEY'], $_GET['TENCENT_APP_SECRET']);
}

function pinglunla_tencent_auth($appkey, $appsecret) {
    Pinglunla_OpenSDK_Tencent_Weibo::init($appkey, $appsecret);
    $callback = get_bloginfo('wpurl') . "/wp-content/plugins/pinglunla/tencent_callback.php";
    $request_token = Pinglunla_OpenSDK_Tencent_Weibo::getRequestToken($callback);
    $url = Pinglunla_OpenSDK_Tencent_Weibo::getAuthorizeURL($request_token);
    header("Location: " . $url);
}
?>
