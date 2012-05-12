<?php
require_once("../../../wp-includes/version.php");
if ( version_compare( $wp_version, '2.6', '>' ) ) {
	require_once("../../../wp-load.php");
} else {
	require_once("../../../wp-config.php");
	require_once("../../../wp-settings.php");
}
include_once("./pinglunla-utils.php");

set_time_limit(0);

$response = pll_get_comments();
header('Content-type: text/javascript');
echo json_encode($response);

function pll_get_comments() {
    $pinglunla_sid = get_option("pinglunla_sid", '');
    $pll_last_comment_id = get_option('pll_last_comment_id', 0);
    $grand_total = get_option("pll_total_imported", 0);
    $sync = true;
    $result = 'success';
    $msg = '';

    /* Time locker, each sync only last for a hour */
    $lock_time = get_option("pll_lock_time", 0);
    if ($lock_time == 0 or ((time() - $lock_time) > 2 * 60 * 60)) {
        update_option("pll_lock_time", time());
    }

    $lock_time = get_option("pll_lock_time");
    if ((time() - $lock_time) > 60 * 60) {
        $msg = time() - $lock_time;
        $status = "expired_time";
        $sync = false;
    }

    if ($sync) {
        $post_data = array(
            "id"        => $pinglunla_sid,
            "last_id"   => $pll_last_comment_id,
            "page_size" => 50,
            "compose_type" => 0,
            "need_id"   => 1
        );

        $response = pinglunla_post_to2('http://'.PLL_URL.'/manage2/export_comments2/', $post_data);

        try {
            $comments = json_decode($response);
        } catch(Exception $e) {
            $result = 'fail';
            $msg = "网络错误，请稍后再试。";
        }

        if ($comments != null)
            pll_sync_comments($comments);

        $total = count($comments);
        $grand_total += $total;
        update_option("pll_total_imported", $grand_total);
        if ($total > 0) {
            $status = "partial";   
        } else {
            $status = "complete";
            update_option("pll_lock_time", time() - 60 * 60);
        }
    }

    return compact('result', 'status', 'total', 'msg', 'grand_total');
}

function pll_sync_comments($comments) {
    foreach ($comments as $comment) {
        $commentdata = array(
            "comment_post_ID" => url_to_postid($comment->page_url),
            "comment_author"  => $comment->author,
            "comment_author_email" => $comment->author_email,
            "comment_author_url"   => $comment->author_homepage,
            "comment_content"      => $comment->content,
            "comment_agent"        => "pinglunla",
            "comment_date"         => $comment->ctime
        );
        wp_insert_comment($commentdata);
        if ($comment->comment_id != null) {
            update_option("pll_last_comment_id", $comment->comment_id);
        }
    }
}
?>
