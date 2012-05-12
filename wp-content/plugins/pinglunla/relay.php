<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

if(isset($_GET["sid"])) {
    $pinglunla_sid = get_option("pinglunla_sid", '');
    if($pinglunla_sid == '') {
        add_option("pinglunla_sid", $_GET["sid"]);
    } else if($pinglunla_sid != $_GET["sid"]) {
        update_option("pinglunla_sid", $_GET["sid"]);
    }
    echo '<script>parent.jQuery("#pll_adv_options").show();parent.jQuery("#pll_sns_options").show();location.href="'.urldecode($_GET["back_url"]).'";</script>'; 
} else {
    exit("Where is your website id? -_-||");
}
?>
