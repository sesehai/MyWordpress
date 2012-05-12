<?php if (get_option('swt_type') == 'Display') { ?>
<h3>最新评论</h3>
<div class="r_comment">
<ul>
<?php
global $wpdb;
$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type,comment_author_url,comment_author_email, SUBSTRING(comment_content,1,16) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' AND user_id='0' ORDER BY comment_date_gmt DESC LIMIT 10";
$comments = $wpdb->get_results($sql);
$output = $pre_HTML;
foreach ($comments as $comment) {
$a= get_bloginfo('wpurl') .'/avatar/'.md5(strtolower($comment->comment_author_email)).'.jpg';
$output .= "\n<li><img src='". $a ."'  alt=\"$comment->comment_author\" class='avatar'/>$comment->comment_author:<br /><a href=\"" . get_permalink($comment->ID) ."#comment-" . $comment->comment_ID . "\" title=\"查看 " .$comment->post_title . "\">" . strip_tags($comment->com_excerpt)."</a></li>";
}
$output .= $post_HTML;
$output = convert_smilies($output);
echo $output;
?> 
</ul>
</div>
	<?php { echo ''; } ?>
		<?php } else { include(TEMPLATEPATH . '/includes/r_comment2.php'); } ?>