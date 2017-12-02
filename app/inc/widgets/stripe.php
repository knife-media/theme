<?php
/**
 * Stripe widget
 *
 * Include different size units
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Stripe_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = [
			'classname' => 'stripe',
			'description' => __('Выводит полосу постов по заданному критерию в виде карточек.', 'knife-theme'),
			'customize_selective_refresh' => true
		];

		parent::__construct('knife_theme_stripe', __('[НОЖ] Карточки постов', 'knife-theme'), $widget_ops);
	}


	/**
	 * Outputs the content of the widget
	 */
	public function widget($args, $instance) {
 		$defaults = ['title' => '', 'posts_per_page' => 10, 'cat' => 0, 'template' => 'triple'];
		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);

 		$q = new WP_Query([
			'cat' => $cat,
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
		]);

		if($q->have_posts()) :

 			while($q->have_posts()) : $q->the_post();
// TODO: Rework
				$cover = get_post_meta(get_the_ID(), '_knife-theme-cover', true);
		$class = ['unit'];

				if($cover)
					$class[] = 'unit--cover';

				$class[] = 'unit--' . $template;

				set_query_var('unit_class', $class);


				get_template_part('template-parts/units/' . $template);

 			endwhile;

			wp_reset_query();

		endif;
  }


	/**
	 * Outputs the options form on admin
	 */
	function form($instance) {
		$defaults = ['title' => '', 'posts_per_page' => 10, 'cat' => 0, 'template' => 'triple', 'cover' => 0];
		$instance = wp_parse_args((array) $instance, $defaults);

		$template = [
			'triple' => __('Три в ряд', 'knife-theme'),
			'double' => __('Два в ряд', 'knife-theme'),
			'single' => __('На всю ширину', 'knife-theme')
		];

		$cover = [
			'defalut' => __('По умолчанию', 'knife-theme'),
			'cover' => __('Использовать подложку', 'knife-theme'),
			'nocover' => __('Убрать подложку', 'knife-theme')
		];

		$category = wp_dropdown_categories([
 			'id' => esc_attr($this->get_field_id('cat')),
			'name' => esc_attr($this->get_field_name('cat')),
			'selected' => esc_attr($instance['cat']),
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
			'<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('template')),
 			esc_attr($this->get_field_name('template')),
			__('Шаблон виджета:', 'knife-theme')
		);

		foreach($template as $name => $title) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['template'], $name, false));
		}

		echo '</select>';

		printf(
			'<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('cover')),
 			esc_attr($this->get_field_name('cover')),
			__('Подложка карточек:', 'knife-theme')
		);

		foreach($cover as $name => $title) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['cover'], $name, false));
		}

		echo '</select>';

		printf(
			'<p><label for="%1$s">%2$s</label>%3$s</p>',
			esc_attr($this->get_field_id('cat')),
			__('Рубрика записей:', 'knife-theme'),
			$category
		);

		echo '<ul>';
		wp_category_checklist();
		echo '</ul>';
	}


	/**
	 * Processing widget options on save
	 */
  public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['cat'] = (int) $new_instance['cat'];
		$instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['template'] = sanitize_text_field($new_instance['template']);
		$instance['cover'] = sanitize_text_field($new_instance['cover']);

		return $instance;
	}
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Stripe_Widget');
});
