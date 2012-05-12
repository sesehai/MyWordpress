<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}
delete_option("pinglunla_sid");
delete_option("pinglunla_export_times");
update_option("pinglunla_sina", 0);
update_option("pinglunla_tencent", 0);
echo '<script>parent.document.getElementById("pinglunla_clear_config").innerText = "初始化完成!";</script>';
?>
