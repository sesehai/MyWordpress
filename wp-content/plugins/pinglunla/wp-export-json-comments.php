<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}
/*
set_time_limit(3600);
header('Content-type: text/json; charset=UTF-8');
header('Content-type: application/json');
header('Content-Disposition: attachment; filename=pinglunla_comments.json');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
 */

$fp = fopen('./pinglunla_comments.json', 'w');

//echo "[\n";
fputs($fp, "[\n");

$posts = $wpdb->get_results("SELECT * from $wpdb->posts AS wpp");
$post_count = count($posts);
$post_counter = 0;
$is_first = 1;
foreach ($posts as $post) {
	$post_id = $post->ID;
	/*
	$sql = "SELECT wpc.*, wpp.ID, wpp.guid, wpp.post_name, wpp.post_title, wpp.post_name FROM $wpdb->comments AS wpc, $wpdb->posts AS wpp WHERE wpc.comment_post_ID = $post_id AND wpc.comment_type = '' AND comment_approved = '1'"; 
	 */
	$result = get_comments(array('post_id'=> $post_id));
	$total_rows = count($result);

	$row_count = 0;
	$post_counter++;
	foreach($result as $row) {
		if ($row_count == 0) {
			if ($is_first != 1) {
				//echo ",\n";
				fputs($fp, ",\n");
			} else {
				$is_first = 0;
			}
		}
	    $arr = array(
	    "author"=>$row->comment_author,
	    "content"=>$row->comment_content,
	    "page_url"=>get_permalink( $post->ID ),
	    "post_name"=>$post->post_name,
	    "ctime"=>$row->comment_date,
	    "page_title"=>$post->post_title,
	    "author_email"=>$row->comment_author_email,
	    "author_homepage"=>$row->comment_author_url
	    );
	    $row_count++;
	    $w_str = json_encode($arr);
	    if ($row_count < $total_rows)
		    $w_str .= ",\n";
	    //echo $w_str;
	    fputs($fp, $w_str);
	}
}
//echo "\n]";
fputs($fp, "\n]");
fclose($fp);
?>
