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
		$defaults = ['title' => '', 'item' => '', 'link' => ''];
		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);

    $post = url_to_postid($link);

		// Don't show empty link
		if(empty($item) || empty($link))
			return;

    // Don't show self link inside single post
		if(is_single() && get_queried_object_id() === $post)
			return;

		echo $args['before_widget'];

		set_query_var('widget_item', $item);
		set_query_var('widget_link', $link);

    if($post > 0) {
      set_query_var('widget_post', $post);
      set_query_var('widget_slug', get_post_field('post_name', $post));
    }

		get_template_part('template-parts/widgets/feature');

		echo $args['after_widget'];
  }


  /**
   * Outputs the options form on admin
   */
  function form($instance) {
		$defaults = ['title' => '', 'item' => '', 'link' => ''];
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
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('item')),
			esc_attr($this->get_field_name('item')),
			__('Заголовок статьи', 'knife-theme'),
			esc_attr($instance['item'])
		);

 		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('link')),
			esc_attr($this->get_field_name('link')),
			__('Ссылка с фичера', 'knife-theme'),
			esc_attr($instance['link'])
		);
	}


  /**
   * Processing widget options on save
   */
  public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field($new_instance['title']);
 		$instance['item'] = sanitize_text_field($new_instance['item']);
  	$instance['link'] = esc_url($new_instance['link']);

    return $instance;
  }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Feature_Widget');
});
