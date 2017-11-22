<?php
/**
 * Transparent widget
 *
 * Transparent recent posts with optional stickers
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Transparent_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'transparent',
            'description' => __('Выводит список из четырех прозрачных постов со стикерами.', 'knife-theme'),
			'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_transparent', __('[НОЖ] Прозрачный', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
 		$defaults = ['title' => '', 'sticker' => 1, 'cat' => 0];
		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);

		$base = [
			'cat' => $cat,
			'posts_per_page' => 4,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1
		];

		$meta = [[
			'key' => 'post_icon',
			'value' => '',
			'compare' => '!='
		]];

		// If user selected option to show posts only with stickers
		if($sticker === 1) $base['meta_query'] = $meta;

		$q = new WP_Query($base);

		if($q->have_posts()) :

			echo $args['before_widget'];

 			while($q->have_posts()) : $q->the_post();

				get_template_part('template-parts/widgets/transparent');

 			endwhile;

			wp_reset_query();

			echo $args['after_widget'];

		endif;
    }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
		$defaults = ['title' => '', 'sticker' => 1, 'cat' => 0];
		$instance = wp_parse_args((array) $instance, $defaults);

		$category = wp_dropdown_categories([
 			'id' => esc_attr($this->get_field_id('cat')),
			'name' => esc_attr($this->get_field_name('cat')),
			'selected' => esc_attr($instance['cat']),
			'class' => 'widefat',
			'show_option_all' => __('Все рубрики', 'knife-theme'),
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
			'<p><label for="%1$s">%2$s</label>%3$s</p>',
			esc_attr($this->get_field_id('cat')),
			__('Рубрика записей:', 'knife-theme'),
			$category
		);

 		printf(
			'<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
			esc_attr($this->get_field_id('sticker')),
			esc_attr($this->get_field_name('sticker')),
			__('Только со стикерами:', 'knife-theme'),
			checked($instance['sticker'], 1, false)
		);
	}


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
		$instance = $old_instance;

 		$instance['cat'] = (int) $new_instance['cat'];
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['sticker'] = $new_instance['sticker'] ? 1 : 0;

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Transparent_Widget');
});
