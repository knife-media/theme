<?php
/**
 * Feature widget
 *
 * Recent posts widget showing as bright links
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Feature_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'feature',
            'description' => __('Выводит фичер на всю ширину со стикером', 'knife-theme'),
			'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_feature', __('[НОЖ] Фичер', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
		$defaults = ['title' => '', 'feature' => 1, 'head' => '', 'link' => '', 'base' => 0];
		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);

		$q = [
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page' => 1,
			'meta_query' => [
				[
					'key' => '_knife-theme-feature',
					'value' => 1,
					'compare' => '='
				]
			]
		];

		// Get post if feature option seltected
		if($feature === 1) :

			// Check cache before get posts from database
			$posts = get_transient($this->id) ?: get_posts($q);

			foreach($posts as $post) {
				$head = get_the_title($post->ID);
				$link = get_permalink($post->ID);
				$base = $post->ID;

				break;
			}

			set_transient($this->id, $posts, 24 * HOUR_IN_SECONDS);

		endif;

		// Don't show empty link
		if(empty($head) || empty($link))
			return;

		echo $args['before_widget'];

		set_query_var('widget_head', $head);
		set_query_var('widget_link', $link);
 		set_query_var('widget_base', $base);

		get_template_part('template-parts/widgets/feature');

		echo $args['after_widget'];
    }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
		$defaults = ['title' => '', 'feature' => 1, 'head' => '', 'link' => ''];
		$instance = wp_parse_args((array) $instance, $defaults);

		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
			esc_attr($this->get_field_id('title')),
			esc_attr($this->get_field_name('title')),
			__('Название виджета:', 'knife-theme'),
			esc_attr($instance['title']),
 			__('Не будет отображаться на странице', 'knife-theme')
		);

 		printf(
			'<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
			esc_attr($this->get_field_id('feature')),
			esc_attr($this->get_field_name('feature')),
			__('Вывести последний фичер пост', 'knife-theme'),
			checked($instance['feature'], 1, false)
		);

		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('head')),
			esc_attr($this->get_field_name('head')),
			__('Заголовок статьи', 'knife-theme'),
			esc_attr($instance['head'])
		);

 		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('link')),
			esc_attr($this->get_field_name('link')),
			__('Ссылка с фичера', 'knife-theme'),
			esc_attr($instance['link'])
		);
?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var feature = '#' + '<?php echo $this->get_field_id('feature'); ?>';

				jQuery(document).on('change', feature, function() {
					var filter = jQuery(this).closest('.widget-content').find('p:nth-child(n+3)');

					if(jQuery(this).prop('checked') === true)
						return filter.hide();

					return filter.show();
				});

				return jQuery(feature).trigger('change');
			});
		</script>
<?php
	}


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field($new_instance['title']);
 		$instance['head'] = sanitize_text_field($new_instance['head']);
  		$instance['link'] = esc_url($new_instance['link']);
		$instance['feature'] = $new_instance['feature'] ? 1 : 0;

        return $instance;
    }


 	/**
	 * Remove transient on widget update
	 */
 	private function remove_cache() {
		delete_transient($this->id);
	}
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Feature_Widget');
});
