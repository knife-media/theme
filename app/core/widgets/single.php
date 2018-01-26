<?php
/**
 * Single widget
 *
 * Full-width widget
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Single_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = [
			'classname' => 'single',
			'description' => __('Выводит пост в виде широкого модуля', 'knife-theme'),
			'customize_selective_refresh' => true
		];

		parent::__construct('knife_theme_single', __('[НОЖ] На всю ширину', 'knife-theme'), $widget_ops);
	}


 	/**
	 * Outputs the content of the widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget($args, $instance) {
		$defaults = [
			'title' => '',
			'cover' => 'default',
			'link' => '',
		];

		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);

		// Check cache before creating WP_Query object
		$q = get_transient($this->id);

		if($q === false) :

			$exclude = get_query_var('widget_exclude', []);

			$q = new WP_Query([
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'ignore_sticky_posts' => 1,
				'post__in' => [url_to_postid($link)]
			]);

			set_transient($this->id, $q, 24 * HOUR_IN_SECONDS);
			set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($q->posts, 'ID')));

		endif;


		if($q->have_posts()) :

 			while($q->have_posts()) : $q->the_post();

				echo $args['before_widget'];

				set_query_var('widget_cover', $cover);

				get_template_part('template-parts/widgets/single');

				echo $args['after_widget'];

 			endwhile;

			wp_reset_query();

		endif;
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
 		$instance = $old_instance;

		$instance['link'] = esc_url($new_instance['link']);
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['cover'] = sanitize_text_field($new_instance['cover']);

		return $instance;
	}

    /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$defaults = [
			'title' => '',
			'link' => '',
			'cover' => 'default'
		];

		$instance = wp_parse_args((array) $instance, $defaults);

		$cover = [
			'defalut' => __('По умолчанию', 'knife-theme'),
			'cover' => __('Использовать подложку', 'knife-theme'),
			'nocover' => __('Убрать подложку', 'knife-theme')
		];


		// Widget title
		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
			esc_attr($this->get_field_id('title')),
			esc_attr($this->get_field_name('title')),
			__('Заголовок:', 'knife-theme'),
			esc_attr($instance['title']),
			__('Не будет отображаться на странице', 'knife-theme')
		);


		// Cover manage
		printf(
			'<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('cover')),
 			esc_attr($this->get_field_name('cover')),
			__('Подложка карточки:', 'knife-theme')
		);

		foreach($cover as $name => $title) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['cover'], $name, false));
		}

		echo '</select>';


		// Post url
		printf(
			'<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
			esc_attr($this->get_field_id('link')),
			esc_attr($this->get_field_name('link')),
			__('Ссылка на запись:', 'knife-theme'),
			esc_attr($instance['link']),
			__('Абсолютная ссылка с этого сайта', 'knife-theme')
		);
	}
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Single_Widget');
});
