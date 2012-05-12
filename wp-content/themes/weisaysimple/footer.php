<div class="clear"></div>
<div id="footer">
<a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" rel="license">
		<img src="http://creativecommons.org/images/public/somerights20.png" style="border-width: 0" alt="Creative Commons License">
	</a><br>
	本网站作品采用<a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" rel="license">知识共享署名-非商业性使用-相同方式共享 2.5 许可协议</a>进行许可。<br>
Copyright <?php echo comicpress_copyright(); ?> <?php bloginfo('name'); ?>. Powered by <a href="http://www.wordpress.org/" rel="external">WordPress</a>.
 Theme by <a href="http://www.weisay.com/" rel="external">Weisay</a>.
 <?php if (get_option('swt_beian') == 'Display') { ?><a href="http://www.miitbeian.gov.cn/" rel="external"><?php echo stripslashes(get_option('swt_beianhao')); ?></a><?php { echo '.'; } ?><?php } else { } ?> <?php if (get_option('swt_tj') == 'Display') { ?><?php echo stripslashes(get_option('swt_tjcode')); ?><?php { echo '.'; } ?>	<?php } else { } ?>
 </div>
<?php wp_footer(); ?>
</div>
</body>
</html>