<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php include('includes/seo.php'); ?>
<?php if (get_option('swt_alt_stylesheet')==''):?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/style.css" />
<?php endif;?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico" type="image/x-icon" />
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
<?php if ( is_singular() ){ ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/comments-ajax.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/realgravatar.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/hoveraccordion.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/weisay.js"></script>
<?php include('includes/lazyload.php'); ?>
</head>

<body>
<div id="page">
<div id="header">
<div id="top">
<div id="top_logo">
            <?php if (get_option('swt_logo') == 'Display') { ?>
    <a href="<?php bloginfo('siteurl'); ?>" title="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>"><div class="logo"></div></a>
    <?php { echo ''; } ?>
			<?php } else { include(TEMPLATEPATH . '/includes/logo.php'); } ?>
</div>
		<div class="search">		
			<form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
				<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" size="30" />
				<button type="submit"><?php _e("Search"); ?></button>
			</form>
		</div><div class="clear"></div>

</div>
</div>

<div id="navigator">

<div id="navbar">

<div class="topnav">
<?php
if(function_exists('wp_nav_menu')) {
    wp_nav_menu(array('theme_location'=>'primary','menu_id'=>'nav','container'=>'ul'));
}
?></div>
<div id="rss"><ul>
<li class="rssfeed"><a href="<?php bloginfo('rss2_url'); ?>" target="_blank" class="icon1" title="欢迎订阅<?php bloginfo('name'); ?>"></a></li>
<?php if (get_option('swt_tqq') == 'Display') { ?><li class="tqq"><a href="<?php echo stripslashes(get_option('swt_tqqurl')); ?>" target="_blank" class="icon2" title="我的腾讯微博"></a></li><?php { echo ''; } ?><?php } else { } ?>
<?php if (get_option('swt_tsina') == 'Display') { ?><li class="tsina"><a href="<?php echo stripslashes(get_option('swt_tsinaurl')); ?>" target="_blank" class="icon3" title="我的新浪微博"></a></li><?php { echo ''; } ?><?php } else { } ?>
<?php if (get_option('swt_mailqq') == 'Display') { ?><li class="rssmail"><a href="http://mail.qq.com/cgi-bin/feed?u=<?php bloginfo('rss2_url'); ?>" target="_blank" class="icon4" title="用QQ邮箱阅读空间订阅我的博客"></a></li><?php { echo ''; } ?><?php } else { } ?>
</ul></div>
<div class="clear"></div>

</div>

</div>