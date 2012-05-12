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

if ($_GET['pll_reset'] == 1) {
    update_option('pinglunla_exported_cid', 0);
    update_option('pll_total_exported', 0);
    die();
}

$pinglunla_sid = get_option("pinglunla_sid", '');
$pinglunla_export_times = get_option("pinglunla_export_times", 0);
$grand_total = get_option("pll_total_exported", 0);

/*
if($pinglunla_export_times) {
	$result = "success";
	$status = "complete";
	$msg = "你已经导出过评论了，如需再导，请联系我们的支持QQ：2407672560。";
    $response = compact('result', 'comment_at', 'status', 'post_id', 'msg', 'total_exported');
    header('Content-type: text/javascript');
    echo json_encode($response);
    exit(0);
}
 */

global $wpdb;
$total_exported = 0;
define('EXPORT_CHUNK_SIZE', 50);

if (current_user_can('manage_options') && $pinglunla_sid != null) {
    if (!empty($_GET['cf_action'])) {
        switch ($_GET['cf_action']) {
            case 'sync_comments':
                break;
            case 'export_comments':
                $comment_id = intval(get_option("pinglunla_exported_cid", 0));

                $result = 'fail';
                $response = null;

                $comments = $wpdb->get_results( $wpdb->prepare("SELECT wpc.*, wpp.ID, wpp.post_name, wpp.post_title
                    FROM $wpdb->comments as wpc, $wpdb->posts as wpp
                    WHERE comment_ID > %d
                    AND comment_approved = 1
                    AND comment_agent != 'pinglunla'
                    AND wpp.ID = wpc.comment_post_ID
                    ORDER BY comment_ID ASC
                    LIMIT ".EXPORT_CHUNK_SIZE,
                    $comment_id));

                if ($comments) {
                    $wxr = pinglunla_export_wp($comments);
                    $post_data = array(
                        "ict" => "common_json",
                        "import_type" => "raw",
                        "Filedata" => $wxr,
                        "import_ws_id" => $pinglunla_sid
                    );

                    $res = send_comments_to_pinglunla($post_data);

                    /* error condition, can't detect that. */
                    $right = "恭喜您";
                    if (strncmp($res, $right, strlen($right)) == 0) {
                        $result = 'success';
                        $total_exported = count($comments);
                        $grand_total += $total_exported;

                        update_option("pinglunla_exported_cid",
                            $comments[$total_exported-1]->comment_ID);
                        update_option("pll_total_exported", $grand_total);
                    } else {
                        $msg = "Network error, please try it later";
                    }
                }

                $max_comment_id = $wpdb->get_var( $wpdb->prepare("
                    SELECT MAX(comment_ID)
                    FROM $wpdb->comments
                    WHERE comment_agent != 'pinglunla'
                    AND comment_approved = 1
                    "));
                $comment_id = get_option("pinglunla_exported_cid");
                if ($max_comment_id == $comment_id) {
                    // update_option("pinglunla_export_times", $pinglunla_export_times+1);
                    $status = 'complete';
                    $msg = '您的评论已全部导入到评论啦。';
                }
                else {
                    $status = 'partial';
                    $msg = "";
                }

                $response = compact('result', 'status', 'msg', 'total_exported', 'grand_total');
                header('Content-type: text/javascript');
                echo json_encode($response);
                die();
        }
    }
} else {
	echo "Permission denied...<br>";
}


function pinglunla_export_wp($comments) {
    $total_rows = count($comments);
    $count = 0;
    $pll_json = "[";
    foreach ($comments as $comment) {
        $arr = array(
            "author"=>$comment->comment_author,
            "content"=>$comment->comment_content,
            "page_url"=>get_permalink($comment->ID),
            "post_name"=>$comment->post_name,
            "ctime"=>$comment->comment_date,
            "page_title"=>$comment->post_title,
            "author_email"=>$comment->comment_author_email,
            "author_homepage"=>$comment->comment_author_url
        );
        $count++;
        if ($count < $total_rows) {
            $w_str = json_encode($arr).",";
        } else {
            $w_str = json_encode($arr);
        }
        $pll_json .= $w_str;

    }
    $pll_json .= "]";
    return $pll_json;
}

function send_comments_to_pinglunla($post_data) {
    if(function_exists('curl_init')) {
            if (!function_exists('curl_setopt_array')) {
                function curl_setopt_array(&$ch, $curl_options)
                {
                    foreach ($curl_options as $option => $value) {
                        if (!curl_setopt($ch, $option, $value)) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            return pinglunla_post_to2('http://'.PLL_URL.'/manage2/upload_xml/', $post_data);
        } else if(ini_get('allow_url_fopen') && function_exists('stream_get_contents')) {
            return pinglunla_post_to('http://'.PLL_URL.'/manage2/upload_xml/', $post_data);
        } else {
        }
}

?>
