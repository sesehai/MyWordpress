<?php
/*
Plugin Name: 评论啦社会化评论系统
Plugin URI: http://pinglun.la
Description: 评论啦将当前相对独立、隔绝的互联网评论系统，连接成一张具有社会化效应的大网。通过评论啦提供的社会化功能，网站主可以有效的提高用户的活跃度和回访率。用户使用评论啦，可以存储、管理自己在互联网上的评论记录。
Version: 0.17
Author: pinglun.la
Author URI: http://pinglun.la
License: GPLv2
*/
require_once('pinglunla-utils.php');

if(!defined("PLL_URL")) {
    define("PLL_URL", "pinglun.la");
}

function pinglunla_create_menu() {
	add_submenu_page(
         'edit-comments.php',
         '评论啦社会化评论系统',
         '评论啦',
         'moderate_comments',
         'pinglunla',
         'pinglunla_comments_manage_page'
     );
}
add_action( 'admin_menu', 'pinglunla_create_menu' , 10);

function pinglunla_menu_admin_head() {
?>
<script type="text/javascript">
jQuery(function($) {
    // fix menu
    var mc = $('#menu-comments');
    mc.find('a.wp-has-submenu').attr('href', 'edit-comments.php?page=pinglunla').end().find('.wp-submenu  li:has(a[href="edit-comments.php?page=pinglunla"])').prependTo(mc.find('.wp-submenu ul'));
});
</script>
<?php
}
add_action('admin_head', 'pinglunla_menu_admin_head');

if (get_option('pinglunla_sina') or get_option('pinglunla_tencent')) {
    add_action('admin_head', 'pinglunla_add_connect_sidebox');
    add_action('publish_post', 'pinglunla_wp_connect_publish', 1);
    add_action('publish_page', 'pinglunla_wp_connect_publish', 1);
    add_action('wp_footer', 'pinglunla_get_comments');
}

function pinglunla_comments_manage_page() {
    
    $pinglunla_sid = get_option("pinglunla_sid", "");
    $pinglunla_seo = get_option("pinglunla_seo", 0);
    $pinglunla_sns_sina = get_option("pinglunla_sina", 0);
    $pinglunla_sns_tencent = get_option("pinglunla_tencent", 0);

    $wpurl = get_bloginfo("wpurl");
    if(empty($wpurl) || strlen($wpurl) < 8) {
        $host = $_SERVER["HTTP_HOST"];
    } else {
        $arr = parse_url($wpurl);
        $host = $arr['host'];
    }
    
    
    $relay_page = urlencode( pinglunla_plugins_url("relay.php", __FILE__) );
	if (version_compare($wp_version, '2.6', '<')) {
?>
    <script type="text/javascript" src="http://static.pinglun.la/md/js/j142min.js"></script>
<?php
	}
?>
<style type="text/css">
.pinglunla_clear {clear:both;}
.pinglunla_tabpage_item {clear:both;}
.pinglunla_tab {
width: 100%;
background: #FAFAFA;
height: 40px;
margin-bottom: 10px;
}

.pinglunla_tab .pinglunla_tab_wrapper {
margin: 0 auto;
width: 400px;
text-align: center;
padding-top: 10px;
height:30px;
}

.pinglunla_tab a {
font-size: 12px;
text-decoration: none;
line-height: 150%;
margin-right: 30px;
font-weight: bold;
color: #333;
padding: 7px;
*padding:3px;
*float:left;

}
.pinglunla_tab a:hover,  .pinglunla_tab a.selected  {
background: #333;
padding: 7px;
border-radius: 3px;
color: white;
*padding:3px;
}

.pinglunla_tabpages {width:1010px;margin:0 auto;}

.pinglunla_tabpages hr {border:none;height:1px;background-color:#eee;color:#eee;margin: 20px 0;
padding:0;}
.pinglunla_tabpages .pll_btn {
padding: 3px 10px;
font-size: 12px;
color: #333;
cursor: pointer;
line-height: 18px;
}

.pinglunla_tabpages p {margin:3px}
.pinglunla_tabpages .cancel_btn {color:#888;}
#pinglunla_alert_p {display:none;padding:6px;color:#E60B42;}

.pinglunla_tabpages h3 {
font-size: 14px;
font-weight: bold;
line-height: 22px;
margin:3px;
}
.pinglunla_tabpages .pinglunla_tabpage_item_left
{float: left;
background: #FAFAFA;
border: 1px solid #DFDFDF;
padding: 10px;
border-radius:10px;
width:280px;
}

.pinglunla_tabpages .pinglunla_tabpage_item_right{float: left;
background: #FAFAFA;
border: 1px solid #DFDFDF;
padding: 10px;margin-left:10px;
border-radius:10px;
}

.pinglunla_tabpages h4 {
font-size: 12px;
font-weight: bold;
margin:5px 0;
}

.pinglunla_tabpages button{margin:10px 0;}
.bindTitleIntro {font-weight:700;}
.pll-weibosnap {
background: url(../wp-content/plugins/pinglunla/screenshot-5.png) 0 0 no-repeat;
width: 291px;
height: 165px;
margin:10px;
}
.pll-weiboimg {
background: url(../wp-content/plugins/pinglunla/screenshot-4.png) 0 0 no-repeat;
width: 566px;
height: 309px;
}

.connectBTN {
width: 142px;
height: 32px;
display: block;
border: none;
cursor: pointer;
margin:10px 0;
}
.connectSINA {
background: url(../wp-content/plugins/pinglunla/weibo_button.png) 0 0 no-repeat;
}
.disconnectSINA {
background: url(../wp-content/plugins/pinglunla/weibo_button_cancel.png) 0 0 no-repeat;
}
.connectTencent {
background: url(../wp-content/plugins/pinglunla/qqweibo_button.png) 0 0 no-repeat;
}
.disconnectTencent {
background: url(../wp-content/plugins/pinglunla/qqweibo_button_cancel.png) 0 0 no-repeat;
}

.pinglunla-importing, .pinglunla-exporting {
background: url(../wp-content/plugins/pinglunla/wait.gif) left center no-repeat;
line-height: 16px;
padding-left: 20px;
}

p.status {
padding-top: 0;
padding-bottom: 0;
margin: 0;
}

</style>
<script type="text/javascript">
function pinglunla_alert(s)
{
    jQuery("#pinglunla_alert_p").text(s).show();
}
jQuery(function() {
    // init menu btns
    jQuery(".pinglunla_tab a").click(function() {
        jQuery(".pinglunla_tabpage_item").hide();
        jQuery(".pinglunla_tab a").removeClass("selected");
        jQuery(this).addClass("selected");
        jQuery(".pinglunla_tabpages ."+jQuery(this).attr("dv")).show();
        
        jQuery("#pinglunla_alert_p").hide();
    }).first().click();
    

    var pinglunla_fire_export = function (){
        jQuery('#pinglunla_export_comments').unbind().click(function() {
			jQuery(this).css("color", "#ccc").attr("disabled", "disabled");
            jQuery('#pinglunla_export_div').html('<p class="status"></p>');
            jQuery('#pinglunla_export_div .status').removeClass('pinglunla-export-fail').addClass('pinglunla-exporting').html('处理中....');
            pinglunla_export_comments();
            return false;
        });
    };
    pinglunla_fire_export();
    var pinglunla_export_comments = function() {
        var $ = jQuery;
		var total;
        var status = $('#pinglunla_export_div .status');
        var total = (status.attr('rel') || '0');
        $.get(
            '<?php echo pinglunla_plugins_url('export-comments.php', __FILE__); ?>',
            {
                cf_action: 'export_comments'
            },
            function(response) {
			
                switch (response.result) {
                    case 'success':
						total = parseInt(total) + parseInt(response.total_exported);
                        grand_total = parseInt(response.grand_total);
						status.html("<b>" + response.msg + "</b>");
						if (total > 0)
							status.append("<b>本次已导入" + total + "条评论，累计导入" + grand_total + "条评论。 (提示：传输过程中切勿关闭本页面!)</b>");
                        status.attr('rel', total);
                        switch (response.status) {
                            case 'partial':
                                pinglunla_export_comments();
                                break;
                            case 'complete':
                                status.removeClass('pinglunla-exporting').addClass('pinglunla-exported');
                                break;
                        }
                    break;
                    case 'fail':
                        status.parent().html(response.msg);
                        pinglunla_fire_export();
                    break;
                }
            },
            'json'
        );
    };

    var pinglunla_fire_import = function (){
        jQuery('#pinglunla_import_comments').unbind().click(function() {
			jQuery(this).css("color", "#ccc").attr("disabled", "disabled");
            jQuery('#pinglunla_import_div').html('<p class="status"></p>');
            jQuery('#pinglunla_import_div .status').removeClass('pinglunla-export-fail').addClass('pinglunla-importing').html("处理中");
            pinglunla_import_comments();
            return false;
        });
    };

    pinglunla_fire_import();

    var pinglunla_import_comments = function () {
        var $ = jQuery;
        var status = $('#pinglunla_import_div .status');
        var total = (status.attr('rel') || '0');
        $.get(
            '<?php echo pinglunla_plugins_url('import-comments.php', __FILE__); ?>', {},
            function(response) {
			
                switch (response.result) {
                    case 'success':
						total = parseInt(total) + parseInt(response.total);
                        grand_total = parseInt(response.grand_total);
						if (total > 0)
							status.html("<b>本次已导回" + total + "条评论,累计导回" + grand_total + "条评论。</b>");
                        status.attr('rel', total);
                        switch (response.status) {
                            case 'partial':
                                pinglunla_import_comments();
                                break;
                            case 'complete':
                                status.removeClass('pinglunla-importing').addClass('pinglunla-exported');
                                status.html("<b>本次已导回" + total + "条评论,累计导回" + grand_total + "条评论。</b>");
                                break;
                            case 'expired_time':
								status.removeClass('pinglunla-importing').addClass('pinglunla-exported');
                                status.html("我们限制为每隔一小时才能再次进行导回操作，请稍后再操作。");
                                break;
                        }
                    break;
                    case 'fail':
                        status.parent().html(response.msg);
                        pinglunla_fire_import();
                    break;
                }
            },
            'json'
        );
    };

    jQuery("#pinglunla_export_json_comments").click(function() {
        jQuery(this).css("color", "#ccc").attr("disabled", "disabled");
        jQuery("#pinglunla_debug_frame").attr("src", "<?php echo pinglunla_plugins_url('export-json-comments.php', __FILE__).'?host='.$host ?>");
    });
    
    jQuery("#pinglunla_clear_config").click(function() {
        jQuery(this).css("color", "#ccc").attr("disabled", "disabled");
        jQuery("#pinglunla_debug_frame").attr("src", "<?php echo pinglunla_plugins_url('pinglunla-clear-config.php', __FILE__) ?>");
    });
    
    jQuery("#pinglunla_toggle_seo").click(function() {
        jQuery(this).css("color", "#ccc").attr("disabled", "disabled");
        jQuery("#pinglunla_debug_frame").attr("src", "<?php echo pinglunla_plugins_url('pinglunla-toggle-seo.php?', __FILE__) ?>");
    });


    jQuery("#pinglunla_sinaconnectWrapper").click(function() {
        if (jQuery(this).attr("value") == "1")
            window.open("<?php echo pinglunla_plugins_url('sina_bind.php', __FILE__) . '?SINAUNBINDED=1';?>");
        else
            window.open("<?php echo pinglunla_plugins_url('sina_bind.php', __FILE__);?>" + '?SINA_APP_KEY=' + $("#sinaAppkey").val() + '&' + 'SINA_APP_SECRET=' + $("#sinaSecret").val(), '', 'width=600, height=500');
    });

    jQuery("#pinglunla_tencentconnectWrapper").click(function() {
        if (jQuery(this).attr("value") == "1")
            window.open("<?php echo pinglunla_plugins_url('tencent_bind.php', __FILE__) . '?TENCENTUNBINDED=1';?>");
        else
            window.open("<?php echo pinglunla_plugins_url('tencent_bind.php', __FILE__);?>" + '?TENCENT_APP_KEY=' + $("#tencentAppkey").val() + '&' + 'TENCENT_APP_SECRET=' + $("#tencentSecret").val());
    });
    
});
</script>
<div id="pinglunla_alert_p"></div>
<div class="pinglunla_tab">
<div class="pinglunla_tab_wrapper">
<a dv="pinglunla_comments_manage" href="javascript:;">评论管理</a>
<a id="pll_sns_options" dv="pinglunla_sns_manage" href="javascript:;">文章同步|评论回流</a>
<a id="pll_adv_options" dv="pinglunla_advanced_options" href="javascript:;">高级选项</a>
</div>
</div>

<div class="pinglunla_tabpages">
    <div class="pinglunla_tabpage_item pinglunla_comments_manage">
<?php
    echo '<iframe id="pinglunla_frame" frameBorder="0" width="100%" height="600px" src="http://'.PLL_URL.'/plugin_login/?host='.$arr["host"].'&relay_page='.$relay_page.'&sid='.$pinglunla_sid.'"></iframe>';
?>
    </div>
    
    <div class="pinglunla_tabpage_item pinglunla_sns_manage">
        <div>
            <h3>文章同步｜评论回流功能设置步骤</h3>
            <ol>
                <li>获取您的网站的Appkey和Appsecret：在微博开放平页面(新浪微博：http://open.weibo.com/ 腾讯微博：http://open.t.qq.com/)选择网站接入，
                    接入网站后，即可在“我的应用“项里获取Appkey和Appsecret
                </li>
                <li>绑定Appkey和Appsecret：将获得的网站Appkey和Appsecret填写在本页下方的相应位置并进行绑定。然后点击正文的绑定新浪
                    微博或腾讯微博按钮，将需要同步到新浪微博或腾讯微博账号绑定(可以不是申请网站接入的账号）
                </li>
                <li>完成以上步骤，您就可以使用文章同步和评论回流功能了
                </li>
            </ol>
        </div>
		
		
		<div class="pinglunla_tabpage_item_left">
		
		
		
		
        <div>
		
		



<h4>文章同步和评论回流功能优势：</h4>
1、发布文章时，可一键将文章同步到官方微博<br />
2、用户在官方微博评论，可同步到网站相关文章<br />
3、用户在官方微博评论，可转发相关文章<br />
<hr >
		
		
            <div class="bindTitleIntro">绑定APP及新浪微博账号, 发文章微博</div>
            <div class="inputAPPWrapper">
            <div class="inputAPPTitle">Appkey</div>
            <input type="text" name="sinaAppkey" id="sinaAppkey" value="<?php echo get_option('SINA_APP_KEY', '');?>"/>
            </div>

            <div class="inputAPPWrapper">
            <div class="inputAPPTitle">Appsecret</div>
            <input type="text" name="sinaSecret" id="sinaSecret" value="<?php echo get_option('SINA_APP_SECRET', '');?>"/>
            </div>
            
            <div>
<?php
    if ($pinglunla_sns_sina == 1) {
?>
			<a class="connectBTN disconnectSINA" id="pinglunla_sinaconnectWrapper" title="绑定新浪微博" value="<?php echo $pinglunla_sns_sina;?>"></a>
<?php
    } else {
?>
            <a class="connectBTN connectSINA" id="pinglunla_sinaconnectWrapper" title="点击解除绑定新浪微博" value="<?php echo $pinglunla_sns_sina;?>"></a>
<?php
    }
?>
<br />
            </div>
        </div>
        <div style="border-top:1px solid #eee;margin-top: 20px;padding-top: 20px;">
            <div class="bindTitleIntro">绑定APP及腾迅微博账号, 发文章微博</div>
            <div class="inputAPPWrapper">
            <div class="inputAPPTitle">Appkey</div>
            <input type="text" name="tencentAppkey" id="tencentAppkey" value="<?php echo get_option('TENCENT_APP_KEY', '');?>"/>
            </div>

            <div class="inputAPPWrapper">
            <div class="inputAPPTitle">Appsecret</div>
            <input type="text" name="tencentSecret" id="tencentSecret" value="<?php echo get_option('TENCENT_APP_SECRET', '');?>"/>
            </div>
            
            <div>
            
<?php
    if ($pinglunla_sns_tencent == 1) {
?>
		<a class="connectBTN disconnectTencent"  id="pinglunla_tencentconnectWrapper" title="绑定腾讯微博" value="<?php echo $pinglunla_sns_tencent;?>"></a>
<?php
    } else {
?>
		<a class="connectBTN connectTencent"  id="pinglunla_tencentconnectWrapper" title="点击解除绑定腾讯微博" value="<?php echo $pinglunla_sns_tencent;?>"></a>
<?php
    }
?>

            </div>
        </div>

    </div>
		



	
	<div class="pinglunla_tabpage_item_right">
<b>使用说明：</b><br />
绑定Appkey、Appsecret和新浪微博账号后，在发布文章页面右上角会出现如下选项<br />
<div class="pll-weibosnap"></div>
<b>微博样例：</b><br />
<div class="pll-weiboimg"></div>
</div>
</div>


	
    <div class="pinglunla_tabpage_item pinglunla_advanced_options">
        <h3>评论导入</h3>
        <p>将您的网站的原有评论导出并保存到评论啦</p>
        <button id="pinglunla_export_comments" class="pll_btn export_btn">一键导入</button>
			   <p style="color:red">（特别提示：如果评论数量较多，同步的时间会比较长，请耐心等待，切勿关闭本页面，有问题请联系QQ：2407672560）</p>
        <br />
        <div id="pinglunla_export_div"></div>
       <iframe name="pinglunla_debug_frame" frameBorder="0" id="pinglunla_debug_frame" width="93%" height="50px"></iframe>

        <br />
        <hr />
        <h3>导回评论</h3>
        <p>将评论从评论啦导回到Wordpress数据库中</p>
        <button id="pinglunla_import_comments" class="pll_btn import_btn">一键导回</button>
        <br />
        <div id="pinglunla_import_div"></div>

        <br />
		<hr />
        <h3>评论导出 json 文件</h3>
        <p>如果由于您的服务器设置造成无法“一键导入”评论内容，可使用此功能导出评论数据，然后到评论啦官方网导入此文件实现评论导入。</p>
        <button id="pinglunla_export_json_comments" class="pll_btn export_btn">导出 json 文件</button>
        <br />
		<hr />
        <h3>初始化评论啦插件</h3>
        <p>将评论系统还原到未认证状态，解决无法登录评论啦的问题。</p>
        <button id="pinglunla_clear_config" class="pll_btn export_btn">初始化评论啦</button>
        <br />
        <hr />
        <h3>SEO设置</h3>
        <p>开启SEO，方便Google、Baidu检索评论，速度上会有些减缓。</p>
        <button id="pinglunla_toggle_seo" class="pll_btn export_btn">
        <?php
        if($pinglunla_seo == 0) {
            echo '开启评论SEO';
        } else {
            echo '关闭评论SEO';
        }
        ?>
        </button>
        <br />
    </div>
</div>
<?php

if(empty($pinglunla_sid)) {
   echo '<script>jQuery("#pll_adv_options").hide();</script>'; 
   echo '<script>jQuery("#pll_sns_options").hide();</script>'; 
}

}

function pinglunla_comments_template($value) {
    global $post;
    if ( !( 'open' == $post->comment_status ) ) {
        return;
    }
    
    $pinglunla_sid = get_option("pinglunla_sid", "");
    return dirname(__FILE__) . '/comments.php';
}

add_filter('comments_template', 'pinglunla_comments_template');
?>
