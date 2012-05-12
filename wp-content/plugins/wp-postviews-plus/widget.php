<?php
function is_selected($id, $check) {
	if( in_array($id, $check) ) {
		return ' selected="selected"';
	}
}

add_action('widgets_init', 'pvp_widget_views_init');
function pvp_widget_views_init() {
	register_widget('WP_Widget_PostViews_Plus');
}

class WP_Widget_PostViews_Plus extends WP_Widget {
	function WP_Widget_PostViews_Plus() {
		$widget_ops = array('description' => __('WP-PostViews plus views statistics', 'wp-postviews-plus'));
		$this->WP_Widget('views-plus', __('Views Stats', 'wp-postviews-plus'), $widget_ops);
		add_action('save_post', array(&$this, 'flush_widget_cache'));
		add_action('deleted_post', array(&$this, 'flush_widget_cache'));
	}
	function widget($args, $instance) {
		$cache = wp_cache_get('widget_postviews_plus', 'widget');
		if( !is_array($cache) ) {
			$cache = array();
		}
		if( !isset($args['widget_id']) ) {
			$args['widget_id'] = $this->id;
		}
		if( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}
		ob_start();
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$type = esc_attr($instance['type']);
		$mode = esc_attr($instance['mode']);
		$withbot = esc_attr($instance['withbot']);
		$limit = intval($instance['limit']);
		$chars = intval($instance['chars']);
		$cat_ids = $instance['cat_ids'];
		if( !is_array($cat_ids) ) {
			$cat_ids = explode(',', $car_ids);
		}
		$tag_ids = explode(',', esc_attr($instance['tag_ids']));
		echo $before_widget.$before_title.$title.$after_title;
		echo '<ul>'."\n";
		switch($type) {
			case 'most_viewed':
				get_most_viewed($mode, $limit, $chars, true, $withbot);
				break;
			case 'most_viewed_category':
				get_most_viewed_category($cat_ids, 'post', $limit, $chars, true, $withbot);
				break;
			case 'most_viewed_tag':
				get_most_viewed_tag($tag_ids, 'post', $limit, $chars, true, $withbot);
				break;
		}
		echo '</ul>'."\n";
		echo $after_widget;
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_postviews_plus', $cache, 'widget');
	}
	function update($new_instance, $old_instance) {
		if( !isset($new_instance['submit']) ) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = strip_tags($new_instance['type']);
		if( !in_array($instance['type'], array('most_viewed', 'most_viewed_category', 'most_viewed_tag')) ) {
			$instance['type'] = 'most_viewed';
		}
		$instance['mode'] = strip_tags($new_instance['mode']);
		if( !in_array($instance['mode'], array('both', 'post', 'page')) ) {
			$instance['mode'] = 'both';
		}
		$instance['withbot'] = ($new_instance['withbot'] == 1) ? 1 : 0;
		$instance['limit'] = intval($new_instance['limit']);
		if( $instance['limit'] <= 0 ) {
			$instance['limit'] = 10;
		}
		$instance['chars'] = intval($new_instance['chars']);
		if( $instance['limit'] <= 0 ) {
			$instance['limit'] = 100;
		}
		$instance['cat_ids'] = $new_instance['cat_ids'];
		if( !is_array($instance['cat_ids']) ) {
			$instance['cat_ids'] = array(1);
		}
		$instance['tag_ids'] = strip_tags($new_instance['tag_ids']);
		$this->flush_widget_cache();
		return $instance;
	}
	function flush_widget_cache() {
		wp_cache_delete('widget_postviews_plus', 'widget');
	}
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Views', 'wp-postviews-plus'), 'type' => 'most_viewed', 'mode' => 'both', 'limit' => 10, 'chars' => 100, 'cat_ids' => '0', 'tag_ids' => '0', 'withbot' => '1'));
		$title = esc_attr($instance['title']);
		$type = esc_attr($instance['type']);
		$mode = esc_attr($instance['mode']);
		$withbot = esc_attr($instance['withbot']);
		$limit = intval($instance['limit']);
		$chars = intval($instance['chars']);
		$cat_ids = $instance['cat_ids'];
		if( !is_array($cat_ids) ) {
			$cat_ids = explode(',', $car_ids);
		}
		$tag_ids = esc_attr($instance['tag_ids']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-postviews-plus'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Statistics Type:', 'wp-postviews-plus'); ?></label><br />
			<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
				<option value="most_viewed"<?php selected('most_viewed', $type); ?>><?php _e('Most Viewed', 'wp-postviews-plus'); ?></option>
				<option value="most_viewed_category"<?php selected('most_viewed_category', $type); ?>><?php _e('Most Viewed By Category', 'wp-postviews-plus'); ?></option>
				<option value="most_viewed_tag"<?php selected('most_viewed_tag', $type); ?>><?php _e('Most Viewed By Tag', 'wp-postviews-plus'); ?></option>
			</select>
		</p>
		<p id="<?php echo $this->get_field_id('mode'); ?>_p" <?php if( $this->number > 0 && $type != 'most_viewed') { echo('style="display:none;"'); } ?>>
			<label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Include Views From:', 'wp-postviews-plus'); ?></label>
			<select name="<?php echo $this->get_field_name('mode'); ?>" id="<?php echo $this->get_field_id('mode'); ?>">
				<option value="both"<?php selected('both', $mode); ?>><?php _e('Posts &amp; Pages', 'wp-postviews-plus'); ?></option>
				<option value="post"<?php selected('post', $mode); ?>><?php _e('Posts Only', 'wp-postviews-plus'); ?></option>
				<option value="page"<?php selected('page', $mode); ?>><?php _e('Pages Only', 'wp-postviews-plus'); ?></option>
			</select>
			<br /><small><?php _e('Only work with ', 'wp-postviews-plus'); _e('Most Viewed', 'wp-postviews-plus'); ?></small>
		</p>
		<p id="<?php echo $this->get_field_id('cat_ids'); ?>_p" <?php if( $this->number > 0 && $type != 'most_viewed_category') { echo('style="display:none;"'); } ?>>
			<label for="<?php echo $this->get_field_id('cat_ids'); ?>"><?php _e('Category IDs:', 'wp-postviews-plus'); ?></label>
			<select name="<?php echo $this->get_field_name('cat_ids'); ?>[]" size="3" multiple="multiple" class="widefat" id="<?php echo $this->get_field_id('cat_ids'); ?>" style="height:auto;" >
				<?php
				$args = array('orderby' => 'id', 'hide_empty' => 0, 'taxonomy' => 'category');
				$cats = get_categories($args);
				foreach( $cats AS $cat ) {
					echo('<option value="' . $cat->term_id . '"' . is_selected($cat->term_id, $cat_ids) . '>' . esc_html($cat->name) . '</option>');
				}
				?>
		        </select>
			<small><?php _e('Seperate mutiple categories with commas.', 'wp-postviews-plus'); ?></small>
		</p>
		<p id="<?php echo $this->get_field_id('tag_ids'); ?>_p" <?php if( $this->number > 0 && $type != 'most_viewed_tag') { echo('style="display:none;"'); } ?>>
			<label for="<?php echo $this->get_field_id('tag_ids'); ?>"><?php _e('Tag IDs:', 'wp-postviews-plus'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('tag_ids'); ?>" name="<?php echo $this->get_field_name('tag_ids'); ?>" type="text" value="<?php echo $tag_ids; ?>" />
			<small><?php _e('Seperate mutiple categories with commas.', 'wp-postviews-plus'); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('No. Of Records To Show:', 'wp-postviews-plus'); ?></label>
			<input name="<?php echo $this->get_field_name('limit'); ?>" type="text" id="<?php echo $this->get_field_id('limit'); ?>" value="<?php echo $limit; ?>" size="4" maxlength="2" />
			<br />
			<label for="<?php echo $this->get_field_id('chars'); ?>"><?php _e('Maximum Post Title Length (Characters):', 'wp-postviews-plus'); ?></label>
			<input id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo $chars; ?>" size="4" /><br /><small><?php _e('<strong>0</strong> to disable.', 'wp-postviews-plus'); ?> <?php _e(' Chinese characters to calculate the two words!', 'wp-postviews-plus'); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('withbot'); ?>"><?php _e('With BOT Views:', 'wp-postviews-plus'); ?></label>
			<select name="<?php echo $this->get_field_name('withbot'); ?>" id="<?php echo $this->get_field_id('withbot'); ?>">
				<option value="1"<?php selected('1', $withbot); ?>><?php _e('With BOT', 'wp-postviews-plus'); ?></option>
				<option value="0"<?php selected('0', $withbot); ?>><?php _e('Without BOT', 'wp-postviews-plus'); ?></option>
			</select>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
		<?php if( $this->number > 0 ) { ?>
		<script type="text/javascript">
		jQuery('#<?php echo $this->get_field_id('type'); ?>').change(function(){
			if(jQuery(this).val()=='most_viewed'){jQuery('#<?php echo $this->get_field_id('mode'); ?>_p').show();jQuery('#<?php echo $this->get_field_id('cat_ids'); ?>_p').hide();jQuery('#<?php echo $this->get_field_id('tag_ids'); ?>_p').hide();}
			if(jQuery(this).val()=='most_viewed_category'){jQuery('#<?php echo $this->get_field_id('mode'); ?>_p').hide();jQuery('#<?php echo $this->get_field_id('cat_ids'); ?>_p').show();jQuery('#<?php echo $this->get_field_id('tag_ids'); ?>_p').hide();}
			if(jQuery(this).val()=='most_viewed_tag'){jQuery('#<?php echo $this->get_field_id('mode'); ?>_p').hide();jQuery('#<?php echo $this->get_field_id('cat_ids'); ?>_p').hide();jQuery('#<?php echo $this->get_field_id('tag_ids'); ?>_p').show();}
		});
		</script>
		<?php } ?>
<?php
	}
}
?>