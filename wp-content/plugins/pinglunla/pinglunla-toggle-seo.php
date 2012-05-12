<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}
$pinglunla_seo = get_option("pinglunla_seo", -1);
$oper = '开启';
if($pinglunla_seo == -1) {
    add_option("pinglunla_seo", 1);
} else 
{
    $wanted_seo = intval($_GET["seo"]);
    update_option("pinglunla_seo", $wanted_seo);
    if(0 == $wanted_seo) {
        $oper =  '关闭';
    }
}
echo "<script>parent.document.getElementById('pinglunla_toggle_seo').innerText = 'SEO成功$oper!';</script>";
?>
