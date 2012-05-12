<h3><span>最新日志</span><span class="selected">热评日志</span><span>随机日志</span></h3>
	<div id="tab-content">
		<ul class="hide"><?php $myposts = get_posts('numberposts=10&offset=0');foreach($myposts as $post) :?>
					<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="详细阅读 <?php the_title_attribute(); ?>"><?php echo cut_str($post->post_title,37); ?></a></li>
					<?php endforeach; ?></ul>
		<ul><?php simple_get_most_viewed(); ?>
</ul>
		<ul class="hide"><?php $myposts = get_posts('numberposts=10&orderby=rand');foreach($myposts as $post) :?>
					<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="详细阅读 <?php the_title_attribute(); ?>"><?php echo cut_str($post->post_title,37); ?></a></li>
					<?php endforeach; ?></ul>
					</div>