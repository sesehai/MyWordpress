<?php
include_once(dirname(__FILE__)."/pinglunla-utils.php");

$pinglunla_sid = get_option("pinglunla_sid", '');
$pinglunla_seo = get_option("pinglunla_seo", 0);
$webpage_url = "http://".PLL_URL."/plugin_get_comments?url=".urlencode(pinglunla_cur_page_url());

if(empty($pinglunla_sid)) {
    echo '<span style="color:#aaaaaa">^_^ 提示: 请到WordPress后台管理评论啦插件, 首次登录后即可激活评论系统!<br /> (没有账号? 请到WordPress后台注册, 或前往<a style="color:#3b5998" href="http://pinglun.la/register/ " target="_blank">评论啦官网注册</a>, 商务QQ2313897338 技术QQ2407672560) </span>';
} else {
?>
<!-- PingLun.La Begin -->
<div id="pinglunla_here">
<?php
if($pinglunla_seo == 1) {
echo file_get_contents($webpage_url);
}
?>
</div>
<a href="http://<?php echo PLL_URL; ?>/" id="logo-pinglunla"></a><script type="text/javascript" src="http://<?php echo PLL_URL; ?>/<?php echo $pinglunla_sid ?>.js" charset="utf-8"></script>
<!-- PingLun.La End -->
<?php
}
?>
