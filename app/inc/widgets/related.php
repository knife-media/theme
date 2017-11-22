<?php
/**
 * Related widget
 *
 * Related posts widget showing as cards in column
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Related_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'related',
            'description' => __('Выводит похожие посты в виде карточек в сайдбар.', 'knife-theme'),
 			'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_related', __('[НОЖ] Похожие статьи', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
		global $post;

		extract($instance);

 		$q = new WP_Query([
			'post__not_in' => [$post->ID],
			'category__not_in' => [$category__not_in],
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
		]);

		if($q->have_posts()) :

			echo $args['before_widget'];

 			while($q->have_posts()) : $q->the_post();

				get_template_part('template-parts/widgets/related');

 			endwhile;

			wp_reset_query();

			echo $args['after_widget'];

		endif;
    }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
		$defaults = ['title' => '', 'posts_per_page' => 4, 'category__not_in' => 620];
		$instance = wp_parse_args((array) $instance, $defaults);

		$category = wp_dropdown_categories([
 			'id' => esc_attr($this->get_field_id('category__not_in')),
			'name' => esc_attr($this->get_field_name('category__not_in')),
			'selected' => esc_attr($instance['category__not_in']),
			'show_option_all' => __('Без фильтра', 'knife-theme'),
			'class' => 'widefat',
			'echo' => false,
		]);

		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('title')),
			esc_attr($this->get_field_name('title')),
			__('Заголовок:', 'knife-theme'),
			esc_attr($instance['title'])
		);

 		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
			esc_attr($this->get_field_id('posts_per_page')),
			esc_attr($this->get_field_name('posts_per_page')),
			__('Количество постов:', 'knife-theme'),
			esc_attr($instance['posts_per_page'])
		);

		printf(
			'<p><label for="%1$s">%2$s</label>%3$s</p>',
			esc_attr($this->get_field_id('category__not_in')),
			__('Не выводить рубрику:', 'knife-theme'),
			$category
		);
	}


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
		$instance = $old_instance;

 		$instance['category__not_in'] = (int) $new_instance['category__not_in'];
		$instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		$instance['title'] = sanitize_text_field($new_instance['title']);

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Related_Widget');
});
