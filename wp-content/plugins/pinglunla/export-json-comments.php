#!/usr/bin/php
<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}

set_time_limit(3600);
header('Content-type: text/plain');
header('Content-Disposition: attachment; filename=pinglunla_comments.json');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');

global $wpdb;

$post_id = 0;
$total = 0;
$eof = 0;
$total_exported = 0;
$is_first = 1;
define('EXPORT_CHUNK_SIZE', 100);

$max_post_id = $wpdb->get_var($wpdb->prepare("
    SELECT MAX(ID)
    FROM $wpdb->posts
    WHERE post_type != 'revision'
    AND post_status = 'publish'
    AND comment_count > 0
    AND ID > %d
", $post_id));

//$fp = tmpfile();
//fwrite($fp, "[\n");
echo "[\n";

//echo " Exporting Max post id is $max_post_id <br>";
while ($post_id < $max_post_id) {
    $post = $wpdb->get_results($wpdb->prepare("
        SELECT *
        FROM $wpdb->posts
        WHERE post_type != 'revision'
        AND post_status = 'publish'
        AND comment_count > 0
        AND ID > %d
        ORDER BY ID ASC
        LIMIT 1
    ", $post_id));
    $post = $post[0];
    $post_id = $post->ID;

    //print('  Exporting comments for post id %d\n', $post_id);

    $query = $wpdb->get_results( $wpdb->prepare("SELECT COUNT(*) as total FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1 LIMIT ".EXPORT_CHUNK_SIZE, $post_id) );
    $total_comments = $query[0]->total;

    $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1 LIMIT ".EXPORT_CHUNK_SIZE, $post_id) );
    $at = 0;

    while ($at < $total_comments) {
        $wxr = "";
        if ($is_first) {
            $is_first = 0;
        } else {
            $wxr .= ",\n";
        }
        $wxr .= export_wp($post, $comments);
        echo $wxr;

        $total_exported += count($comments);
        $at += EXPORT_CHUNK_SIZE;
        $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d  LIMIT ".EXPORT_CHUNK_SIZE." OFFSET {$at}", $post->ID) );
    }

}
//fwrite("\n]");
//fclose($fp);
echo "\n]";

function export_wp($post, $comments) {
    $total_rows = count($comments);
    $count = 0;
    $pll_json = "";
    foreach ($comments as $comment) {
        $arr = array(
            "author"=>$comment->comment_author,
            "content"=>$comment->comment_content,
            "page_url"=>get_permalink($post->ID),
            "post_name"=>$post->post_name,
            "ctime"=>$comment->comment_date,
            "page_title"=>$post->post_title,
            "author_email"=>$comment->comment_author_email,
            "author_homepage"=>$comment->comment_author_url
        );
        $count++;
        if ($count < $total_rows) {
            $w_str = json_encode($arr).",\n";
        } else {
            $w_str = json_encode($arr);
        }
        $pll_json .= $w_str;
    }
    return $pll_json;
}
?>
