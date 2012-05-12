<?php

require_once('weibo1.0/weibooauth.php');
require_once('tencent/Weibo.php');

/* creates a compressed zip file */
function pinglunla_create_zip($files = array(),$destination = '',$overwrite = false) {
  //if the zip file already exists and overwrite is false, return false
  if(file_exists($destination) && !$overwrite) { return false; }
  //vars
  $valid_files = array();
  //if files were passed in...
  if(is_array($files)) {
    //cycle through each file
    foreach($files as $file) {
      //make sure the file exists
      if(file_exists($file)) {
        $valid_files[] = $file;
      }
    }
  }
  //if we have good files...
  if(count($valid_files)) {
    //create the archive
    $zip = new ZipArchive();
    if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
      return false;
    }
    //add the files
    foreach($valid_files as $file) {
      $zip->addFile($file,$file);
    }
    //debug
    //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
    
    //close the zip -- done!
    $zip->close();
    
    //check to make sure the file exists
    return file_exists($destination);
  }
  else
  {
    return false;
  }
}

/* post files without curl */
function pinglunla_post_to($url, $post_data)
{
    $opts = array("http" =>
        array(
            'method' => "POST",
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' =>  http_build_query( $post_data )
            )
        );
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}

/* post files with curl */
function pinglunla_post_to2($url, $post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function pinglunla_cur_page_url() 
{
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function pinglunla_plugins_url_s($path) {
// WP < 2.6
	if ( !function_exists('plugins_url') ) {
		return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__)).'/'. $path;
	}
	return plugins_url(plugin_basename(dirname(__FILE__)));
}

function pinglunla_plugins_url($path = '', $plugin = '')
{
    if (function_exists('plugins_url'))
        return plugins_url($path, $plugin);
    else
        return pinglunla_plugins_url_s($path);
}

function pinglunla_add_connect_sidebox() {
    if (get_option("pinglunla_sina") or get_option('pinglunla_tencent')) {
        if (function_exists("add_meta_box")) {
            if (version_compare($wp_version, '2.7', '<')) {
                add_meta_box('pinglunla_connect_sidebox', '文章微博同步设置', 'pinglunla_connect_sidebox', 'page', 'normal', 'high');
                add_meta_box('pinglunla_connect_sidebox', '文章微博同步设置', 'pinglunla_connect_sidebox', 'post', 'normal', 'high');
            } else {
                add_meta_box('pinglunla_connect_sidebox', '文章微博同步设置', 'pinglunla_connect_sidebox', 'page', 'side', 'high');
                add_meta_box('pinglunla_connect_sidebox', '文章微博同步设置', 'pinglunla_connect_sidebox', 'post', 'side', 'high');
            }
        }
    }
}

function pinglunla_connect_sidebox() {
    if (get_option('pinglunla_sina') == 1) {
        echo '<p><label><input type="checkbox" name="publish_sync_sina" value="1" checked="true"/>同步到新浪微博[已绑定站长账号]</label></p>';
        echo '<p><label>新浪微博发布时@<input type="text" name="publish_and_at_sina" style="width:100px;padding-left:5px;margin-left:5px;"/></label></p>';
    }
    if (get_option('pinglunla_tencent') == 1) {
        echo '<p><label><input type="checkbox" name="publish_sync_tencent" value="1" checked="true"/>同步到腾迅微博[已绑定站长账号]</label></p>';
        echo '<p><label>腾迅微博发布时@<input type="text" name="publish_and_at_tencent" style="width:100px;padding-left:5px;margin-left:5px;"/></label></p>';
    }

    if (get_option('pinglunla_sina') or get_option('pinglunla_tencent')) {
?>
        <div class="postbox">
            <div class="handlediv" title="点击以切换"><br></div>
            <h3 class="hndle"><span>自定义微博同步内容</span></h3>
            <div class="inside">
                <label class="screen-reader-text" for="pinglunla_sns_content">同步内容</label>
                <textarea name="pinglunla_sns_content" rows="2" cols="30"></textarea>
                <p><label><input type="checkbox" name="pinglunla_has_sns_pic" value="1" checked="true"/>抓取文章图片</label>
                    <input type="text" name="pinglunla_sns_pic_url"/></p>
					<p class="howto"><b>自定义微博同步内容及图片说明：</b><br />
					1、若要自定义微博同步内容请在输入框中输入，限140字；若留空则抓取文章内容同步<br />
					2、若要自定义微博同步图片，请在下方输入框中输入网络图片URL；若勾选“抓取文章图片”则默认抓取文章中第一张图片同步
					
					</p>
            </div>
        </div>
<?php
    }
}

/*
 * Post page to t.sina.com
 */
function pinglunla_wp_replace($str) {
  $a = array('&#160;', '&#038;', '&#8211;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&amp;', '&lt;', '&gt', '&ldquo;', '&rdquo;', '&nbsp;', 'Posted by Wordmobi');
  $b = array(' ', '&', '-', '‘', '’', '“', '”', '&', '<', '>', '“', '”', ' ', '');
  $str = str_replace($a, $b, strip_tags($str));
  return trim($str);
}

function pinglunla_wp_multi_media_url($content) {
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"].*>/isU', $content, $image);
    $p_sum = count($image[1]);
    if ($p_sum > 0) {
        $url = $image[1][0];
    } 
    if (substr($url, 0, 4) != "http")
        $url = '';
    return $url;
}

function pinglunla_wp_video_url($content) {
    preg_match_all('/<embed[^>]+src=[\'"]([^\'"]+)[\'"].*>/isU', $content, $video);
    $v_sum = count($video[1]);
    if ($v_sum > 0) {
        $url = $video[1][0];
    } 
    return $url;
}

function pinglunla_wp_connect_publish($post_ID) {
    $title = pinglunla_wp_replace(get_the_title($post_ID));
    $postlink = get_permalink($post_ID);
    $shortlink = get_bloginfo('url') . "/?p=" . $post_ID;
    $thePost = get_post($post_ID);
    $content = $thePost->post_content;
    //$excerpt = $thePost->post_excerpt;
    $excerpt = $_POST["pinglunla_sns_content"];
    $post_author_ID = $thePost->post_author;
    $post_date = strtotime($thePost->post_date);
    $post_modified = strtotime($thePost->post_modified);
    $post_content = pinglunla_wp_replace($content);
    
    if (isset($_POST['pinglunla_has_sns_pic'])) {
        if ($_POST['pinglunla_sns_pic_url'] != null) {
            $pic = $_POST['pinglunla_sns_pic_url'];
        } else {
            $pic = pinglunla_wp_multi_media_url($content);
        }
    }
    $video = pinglunla_wp_video_url($content);

    if ($excerpt) {
        $post_content = pinglunla_wp_replace($excerpt);
    }

    if (!isset($_POST['publish_sync_sina']) and !isset($_POST['publish_sync_tencent']))
        return;

    /* Delete title
    $title = trim('#' . $title . '#' . '-' . $post_content);
    $title = trim($post_content);
     */
    $title = trim('[' . $title . ']' . '-' . $post_content);
    $title = preg_replace("'([\r\n])[\s]+'", " ", $title);
    /*
    $page_part = explode('http://', $shortlink);
    $page = $_SERVER['HTTP_HOST'].'_'.$page_part[1];
     */
    $page = $post_ID;

    if ($_POST['publish_sync_tencent']) {
        $at_str = trim($_POST['publish_and_at_tencent']);
        if($at_str == '')
            $ats = array();
        else
            $ats = preg_split("/[\s,]+/", trim($at_str));

        pinglunla_sychronize_post_to_tencent($title, $page, $postlink, $video, $pic, $ats);
        unset($_POST['publish_sync_tencent']);
    }

    if($_POST['publish_sync_sina']) {
        $at_str = trim($_POST['publish_and_at_sina']);
        if($at_str == '')
            $ats = array();
        else
            $ats = preg_split("/[\s,]+/", trim($at_str));

        $trace_link = $postlink;
        pinglunla_sychronize_post_to_sina($title, $page, $trace_link, $video, $pic, $ats);
        unset($_POST['publish_sync_sina']);
    }

}

function pinglunla_getSinaShortURL($longURL) {
    $url = 'http://api.t.sina.com.cn/short_url/shorten.json?source=507593302&url_long=' . $longURL;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    $output_json = json_decode($output);
    $short_url =  $output_json[0]->url_short;
    return $short_url;
}

function pinglunla_correctNumberWords($str) {
    return (mb_strlen($str, 'utf8') + strlen($str))/2;
}

function pinglunla_create_sync_content($title, $trace_link, $video, $pic, $ats) {
    $at_str = '';
    if (count($ats) != 0) {
        $at_str = ' by ';
        foreach($ats as $at) {
            $at_str .= '@' . $at;
        }
    }

    $short_url = pinglunla_getSinaShortURL($trace_link);
    
    if ($video == null) {
        $content = $title . ' ' . $short_url . $at_str;
        if (pinglunla_correctNumberWords($content) > 280) {
            $content = mb_substr($title, 0, 138 - strlen($short_url)/2 - mb_strlen($at_str, 'utf8'),
                'utf8') . '...' . $short_url . $at_str;
        }
    } else {
        $content = $title . ' ' . $video ." " .  $short_url . $at_str;
        if (pinglunla_correctNumberWords($content) > 280) {
            $content = mb_substr($title, 0, 138 - strlen($short_url ." ". $video)/2 - mb_strlen($at_str, 'utf8'),
                'utf8') . '...' . $video . " ". $short_url . $at_str;
        }
    }
    return $content;
}

function pinglunla_sychronize_post_to_sina($title, $page, $trace_link, $video, $pic, $ats) {
    $content = pinglunla_create_sync_content($title, $trace_link, $video, $pic, $ats);
    $sina_app_key = get_option('SINA_APP_KEY', '');
    $sina_app_secret = get_option('SINA_APP_SECRET');
    $sina_app_token = get_option('SINA_APP_TOKEN');

    if ($sina_app_key != '' and $sina_app_token != '') {
        $c = new Pinglunla_WeiboClient($sina_app_key, $sina_app_secret, $sina_app_token['oauth_token'],
                        $sina_app_token['oauth_token_secret']);
    } else {
        return;
    }
    
    if ($pic == null) {
        $ret = $c->update($content);
    } else {
        $ret = $c->upload($content, $pic);
    }


    if (get_option('is_create_sync') != 1) {
        pinglunla_create_sync_list();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "pll_sync";
    $rows = $wpdb->get_results("select * from $table_name where pid = $page;");
    if (count($rows) == 0) {
        $row_affected = $wpdb->insert($table_name, array('pid' => $page,
            'sid' => $ret['mid'], 'since_id' => 0));
    } else {
        $wpdb->update($table_name,
            array("sid" => $ret['mid'], 'since_id' => 0), array('pid' => $page));
    }
}

function pinglunla_init_tencent_weibo() {
    $appkey = get_option('TENCENT_APP_KEY');
    $appsecret = get_option('TENCENT_APP_SECRET');
    Pinglunla_OpenSDK_Tencent_Weibo::init($appkey, $appsecret);
    $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OPENID] = get_option('tencent_openid');
    $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OPENKEY] = get_option('tencent_openkey');
    $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::ACCESS_TOKEN] = get_option('tencent_access_token');
    $_SESSION[Pinglunla_OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] = get_option('tencent_token_secret');
}

function pinglunla_sychronize_post_to_tencent($title, $page, $trace_link, $video, $pic, $ats) {
    $at_str = '';
    if (count($ats) != 0) {
        $at_str = ' by ';
        foreach($ats as $at) {
            $at_str .= '@' . $at;
        }
    }
    $short_url = pinglunla_getSinaShortURL($trace_link);
    $content = $title . ' ' . $short_url . $at_str;
    if (pinglunla_correctNumberWords($content) > 280) {
        $content = mb_substr($title, 0, 138 - strlen($short_url)/2 - mb_strlen($at_str, 'utf8'),
            'utf8') . '...' . $short_url . $at_str;
    }

    session_start();
    pinglunla_init_tencent_weibo();
    $ret = Pinglunla_OpenSDK_Tencent_Weibo::call('t/add_multi',
        array('format' => 'json', 'content' => $content, 'clientip' => '127.0.0.1',
            'pic_url' => $pic, 'video_url' => $video), 'post');
    session_destroy();

    if (get_option('is_create_sync') != 1) {
        pinglunla_create_sync_list();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "pll_sync";
    $rows = $wpdb->get_results("select * from $table_name where pid = $page;");
    if (count($rows) == 0) {
        $row_affected = $wpdb->insert($table_name, array('pid' => $page,
            'tid' => $ret["data"]["id"], 'tsince_id' => 0));
    } else {
        $wpdb->update($table_name,
            array("tid" => $ret["data"]["id"], 'tsince_id' => 0), array('pid' => $page));
    }
}

function pinglunla_create_sync_list() {
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    global $wpdb;
    $table_name = $wpdb->prefix . "pll_sync";

    $sql = "CREATE TABLE " . $table_name . "(
            pid bigint(20) not null primary key,
            sid bigint(20),
            since_id bigint(20),
            tid bigint(20),
            tsince_id bigint(20));
        ";
    dbDelta($sql);
    update_option('is_create_sync', 1);
}

function pinglunla_get_comments() {
    if (is_single()) {
        set_time_limit(0);
        if (get_option('pinglunla_refresh', -1) == -1)
            update_option('pinglunla_refresh', 10);
        if (get_option('is_create_sync') != 1) {
            pinglunla_create_sync_list();
        }
        global $wpdb;
        $post_ID = get_the_ID();
        $table_name = $wpdb->prefix . "pll_sync";
        $res = $wpdb->get_results("select * from $table_name where pid = $post_ID;");

        if (count($res) == 0) {
            return;
        }

        $times = get_option('pinglunla_refresh');
        if ($times > 0) {
            update_option('pinglunla_refresh', --$times);
            return;
        } else {
            update_option('pinglunla_refresh', 10);
        }

        if ($res[0]->sid != null) {
            pinglunla_get_comments_from_sina($res, $post_ID);
        }
        if ($res[0]->tid != null) {
            pinglunla_get_comments_from_tencent($res, $post_ID);
       }
    }
}

function pinglunla_get_comments_from_sina($res, $post_ID) {
    $sina_app_key = get_option('SINA_APP_KEY', '');
    $sina_app_secret = get_option('SINA_APP_SECRET');
    $sina_app_token = get_option('SINA_APP_TOKEN');

    if ($sina_app_key != '' and $sina_app_token != '') {
        $c = new Pinglunla_WeiboClient($sina_app_key, $sina_app_secret, $sina_app_token['oauth_token'],
        $sina_app_token['oauth_token_secret']);
    } else {
        return;
    }

    $count = 10;
    $comments = $c->get_comments_by_sid($res[0]->sid, 1, $count);
    /* Post comments to pinglun.la
    echo $res[0]->since_id;
    var_dump($comments);
     */

    if (count($comments) != 0) {
        if (count($comments) == $count) {
            for ($page = 2; $page < 50; $page++) {
                $old_comments = $c->get_comments_by_sid($res[0]->sid, $page, $count);
                $post_data = pinglunla_create_post_data_from_sina($old_comments, $post_ID, $res);
                $result = post_comments_to_pinglunla($post_data);
                if (count($post_data["Filedata"]) < $count)
                    break;
            }
        }

        $post_data = pinglunla_create_post_data_from_sina($comments, $post_ID, $res);
        $result = post_comments_to_pinglunla($post_data);

        $right = "恭喜您";
        if (strncmp($result, $right, strlen($right)) == 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . "pll_sync";
            $wpdb->update($table_name,
                array('since_id' => $comments[0]["id"]),
                array('pid' => $post_ID));
        }
    }
}

function pinglunla_get_comments_from_tencent($res, $post_ID) {
   pinglunla_init_tencent_weibo();
    $comments = Pinglunla_OpenSDK_Tencent_Weibo::call('t/re_list',
        array('format' => 'json', 'flag' => 1, 'rootid' => $res[0]->tid,
        'reqnum' => 50, 'pageflag' => 0), 'get');

    if (count($comments["data"]["info"]) != 0) {
        if (count($comments["data"]["info"]) == 50) {
            for ($page = 2; $page < 50; $page++) {
                $old_comments = Pinglunla_OpenSDK_Tencent_Weibo::call('t/re_list',
                    array('format' => 'json', 'flag' => 1, 'rootid' => $res[0]->tid,
                    'reqnum' => 50, 'pageflag' => 1), 'get');
                $post_data = pinglunla_create_post_data_from_tencent($old_comments, $post_ID, $res);
                $result = post_comments_to_pinglunla($post_data);
                if (count($post_data["Filedata"]) < 50)
                    break;
            }
        }

        $post_data = pinglunla_create_post_data_from_tencent($comments, $post_ID, $res);
        $result = post_comments_to_pinglunla($post_data);

        $right = "恭喜您";
        if (strncmp($result, $right, strlen($right)) == 0) {
            global $wpdb; $table_name = $wpdb->prefix . "pll_sync";
            $wpdb->update($table_name,
                array('tsince_id' => $comments["data"]["info"][0]["timestamp"]),
                array('pid' => $post_ID));
        }
    }
}

function post_comments_to_pinglunla($post_data) {
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
        //使用curl
    //var_dump($post_data);
        return pinglunla_post_to2('http://'. PLL_URL .'/manage2/upload_xml/', $post_data);
    } else if(ini_get('allow_url_fopen') && function_exists('stream_get_contents')) {
        //使用get_file_contents
        return pinglunla_post_to('http://'. PLL_URL .'/manage2/upload_xml/', $post_data);
    } else {
    }
}

function pinglunla_create_post_data_from_tencent($comments, $post_ID, $res) {
    global $wpdb;
    $rows = $wpdb->get_results("select post_title, post_name from $wpdb->posts where ID = $post_ID;");

    $pll_comments = "[";
    $count = 1;
    foreach ($comments["data"]["info"] as $comment) {
        if ($comment["timestamp"] <= $res[0]->tsince_id) {
            if (strlen($pll_comments) > 1)
                $pll_comments = substr($pll_comments, 0, strlen($pll_comments)-1);
            break;
        }
        date_default_timezone_set('Asia/Chongqing');
        $arr = array(
            "author"=>$comment["nick"],
            "content"=>$comment["text"],
            "page_url"=>get_permalink($post_ID),
            "ctime"=>date('Y-m-d H:i:s', $comment["timestamp"]),
            "page_title"=>$rows[0]->post_title,
            "post_name"=>$rows[0]->post_name,
            "author_email"=>'',
            "user_type" => "qqweibo",
            "user_avatar" => $comment["head"],
            "sns_id" => (string) $comment["openid"],
            "author_homepage"=> ''
        );
        $w_str = json_encode($arr);
        if ($count < count($comments["data"]["info"]))
            $w_str .= ",";

        $pll_comments .= $w_str;
        $count++;
    }
    $pll_comments .= "]";
    $post_data = array(
        "ict" => "common_json",
        "import_type" => "raw",
        "Filedata" => $pll_comments,
        "import_ws_id" => get_option('pinglunla_sid')
    );

    return $post_data;
}

function pinglunla_create_post_data_from_sina($comments, $post_ID, $res) {
    global $wpdb;
    $rows = $wpdb->get_results("select post_title, post_name from $wpdb->posts where ID = $post_ID;");

    $pll_comments = "[";
    $count = 1;
    foreach ($comments as $comment) {
        if ($comment["id"] <= $res[0]->since_id) {
            if (strlen($pll_comments) > 1)
                $pll_comments = substr($pll_comments, 0, strlen($pll_comments)-1);
            break;
        }
        date_default_timezone_set('Asia/Chongqing');
        $arr = array(
            "author"=>$comment["user"]["name"],
            "content"=>$comment["text"],
            "page_url"=>get_permalink($post_ID),
            "ctime"=>date('Y-m-d H:i:s', strtotime($comment["created_at"])),
            "page_title"=>$rows[0]->post_title,
            "post_name"=>$rows[0]->post_name,
            "author_email"=>'',
            "user_type" => "weibo",
            "user_avatar" => $comment["user"]["profile_image_url"],
            "sns_id" => (string) $comment["user"]["id"],
            "author_homepage"=>$comment["user"]["url"]
        );
        $w_str = json_encode($arr);
        if ($count < count($comments))
            $w_str .= ",";

        $pll_comments .= $w_str;
        $count++;
    }
    $pll_comments .= "]";
    $post_data = array(
        "ict" => "common_json",
        "import_type" => "raw",
        "Filedata" => $pll_comments,
        "import_ws_id" => get_option('pinglunla_sid')
    );

    return $post_data;
}

?>
