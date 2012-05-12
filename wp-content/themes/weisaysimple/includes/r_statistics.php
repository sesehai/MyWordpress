<h3>博客统计</h3>
   <ul>
<li>日志总数：<?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?> 篇</li>
<li>评论总数：<?php echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments where comment_author!='".(get_option('swt_user'))."'");?> 篇</li>
<li>标签数量：<?php echo $count_tags = wp_count_terms('post_tag'); ?> 个</li>
<li>链接总数：<?php $link = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'"); echo $link; ?> 个</li>
<li>建站日期：<?php echo get_option('swt_builddate'); ?></li>
<li>运行天数：<?php echo floor((time()-strtotime(get_option('swt_builddate')))/86400); ?> 天</li>
<li>最后更新：<?php $last = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");$last = date('Y-n-j', strtotime($last[0]->MAX_m));echo $last; ?></li>
   </ul>