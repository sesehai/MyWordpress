<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

if (isset($_GET['SINAUNBINDED'])) {
    update_option('pinglunla_sina', 0);
?>
    <script>
        //window.opener.jQuery("#pinglunla_sina_bind_content").html("点击按钮绑定");
        //window.opener.jQuery("#pinglunla_sinaconnectWrapper").attr("value", "0"); 
        //window.close();
		
		window.opener.jQuery("#pinglunla_sina_bind_content").html("");
		window.opener.jQuery("#pinglunla_sinaconnectWrapper").removeClass("disconnectSINA").addClass("connectSINA");
		window.opener.jQuery("#pinglunla_sinaconnectWrapper").attr("value", "0"); 
		window.close();
		
    </script>
<?php
    exit();
}

if (isset($_GET['SINA_APP_KEY']) && isset($_GET['SINA_APP_SECRET'])) {
    update_option('SINA_APP_KEY', $_GET['SINA_APP_KEY']);
    update_option('SINA_APP_SECRET', $_GET['SINA_APP_SECRET']);

    include("sina_auth.php");
}

?>
