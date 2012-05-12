<?php get_header(); ?>
<div id="roll"><div title="回到顶部" id="roll_top"></div><div title="转到底部" id="fall"></div></div>
<div id="content">
<div class="main">
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
<ul <?php post_class(); ?> id="post-<?php the_ID(); ?>">
<li>
<div class="post_date"><span class="date_m"><?php echo date('M',get_the_time('U'));?></span><span class="date_d"><?php the_time('d') ?></span><span class="date_y"><?php the_time('Y') ?></span></div>
<div class="article">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="详细阅读 <?php the_title_attribute(); ?>"><?php the_title(); ?></a><span class="new"><?php include('includes/new.php'); ?></span></h2>
<?php if (get_option('swt_thumbnail') == 'Display') { ?>
        <?php if (get_option('swt_articlepic') == 'Display') { ?>
<?php include('includes/articlepic.php'); ?>
    <?php { echo ''; } ?>
			<?php } else { include(TEMPLATEPATH . '/includes/thumbnail.php'); } ?>
<?php { echo ''; } ?><?php } else { } ?>
<div class="entry_post"><span><?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 365,"..."); ?></span><span class="more"><a href="<?php the_permalink() ?>" title="详细阅读 <?php the_title(); ?>" rel="bookmark">阅读全文</a></span></div>
<div class="clear"></div>
<div class="info">作者：<?php the_author() ?> | 分类：<?php the_category(', ') ?> | 阅读：<?php if(function_exists(the_views)) { the_views(' 次', true);}?> | <?php the_tags('标签：', ', ', ''); ?></div><div class="comments_num"><?php comments_popup_link ('抢沙发','1条评论','%条评论'); ?></div>
</div></li></ul><div class="clear"></div>
		<?php endwhile; ?>
		<?php endif; ?> 
<div class="navigation"><?php pagination($query_string); ?></div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
