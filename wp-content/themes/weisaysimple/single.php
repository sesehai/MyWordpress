<?php get_header(); ?>
<div id="roll"><div title="回到顶部" id="roll_top"></div><div title="查看评论" id="ct"></div><div title="转到底部" id="fall"></div></div>
<div id="content">
<div class="main"><?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="left">
<div class="post_date"><span class="date_m"><?php echo date('M',get_the_time('U'));?></span><span class="date_d"><?php the_time('d') ?></span><span class="date_y"><?php the_time('Y') ?></span></div>
<div class="article">
<h2><?php the_title(); ?></h2>
<div class="article_info">作者：<?php the_author() ?> &nbsp; 发布：<?php the_time('Y-m-d H:i') ?> &nbsp; 分类：<?php the_category(', ') ?> &nbsp; 阅读：<?php if(function_exists(the_views)) { the_views(' 次', true);}?> &nbsp; <?php comments_popup_link ('抢沙发','1条评论','%条评论'); ?> &nbsp; <?php edit_post_link('编辑', ' [ ', ' ] '); ?></div><div class="clear"></div>
        <div class="context">
        <?php if (get_option('swt_adb') == 'Display') { ?><div style="float:right;border:1px #ccc solid;padding:2px;overflow:hidden;margin:12px 0 1px 2px;"><?php echo stripslashes(get_option('swt_adbcode')); ?></div><?php { echo ''; } ?><?php } else { } ?><?php the_content('Read more...'); ?><p>本文固定链接: <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_permalink() ?> | <?php bloginfo('name');?></a></p></div>
        <?php if (get_option('swt_adc') == 'Display') { ?><p style="text-align:center;"><?php echo stripslashes(get_option('swt_adccode')); ?></p><?php { echo ''; } ?><?php } else { } ?>
</div>
</div>
<div class="articles">
<div class="author_pic">
					<a href="#" title="<?php the_author_description(); ?>"><?php echo get_avatar( get_the_author_email(), '48' ); ?></a>
				</div>
<div class="author_text">
			<span class="spostinfo">
				该日志由 <?php the_author() ?> 于<?php the_time('Y年m月d日') ?>发表在 <?php the_category(', ') ?> 分类下，
				<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {?>
				你可以<a href="#respond">发表评论</a>，并在保留<a href="<?php the_permalink() ?>" rel="bookmark">原文地址</a>及作者的情况下<a href="<?php trackback_url(); ?>" rel="trackback">引用</a>到你的网站或博客。
				<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) { ?>
				通告目前不可用，你可以至底部留下评论。
				<?php } ?><br/>
				原创文章转载请注明: <a href="<?php the_permalink() ?>" rel="bookmark" title="本文固定链接 <?php the_permalink() ?>"><?php the_title(); ?> | <?php bloginfo('name');?></a><br/>
				<?php the_tags('关键字: ', ', ', ''); ?>
			</span>
				</div>
</div>

<div class="articles">
<?php previous_post_link('【上一篇】%link') ?><br/><?php next_post_link('【下一篇】%link') ?>
</div>

<div class="articles">
<?php include('includes/related.php'); ?>
<div class="clear"></div>
</div>

<div class="articles">
<?php comments_template(); ?>
</div>

	<?php endwhile; else: ?>
	<?php endif; ?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>