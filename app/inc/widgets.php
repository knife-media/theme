<?php
/**
 * Custom theme widgets
 *
 * It uses just predefined query args for now.
 * Later it will be represent WP_Query builder
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Stripe_Widget extends WP_Widget {
	private $knife_templates = [];

    public function __construct() {
		$this->knife_templates = [





		];

        $widget_ops = [
            'classname' => 'knife-stripe',
            'description' => __('Выводит полосу с записями по заданному критерию.', 'knife-theme'),
        ];

        parent::__construct('knife_theme_stripe', __('Полоса записей', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
		// We don't want to show title or before/after content for this widget now
		$template = isset($instance['template']) ? esc_attr($instance['template']) : '';

		if($template === 'television') :
			$q = new WP_Query(['ignore_sticky_posts' => 1, 'post_status' => 'publish', 'posts_per_page' => 1, 'offset' => 3]);

			echo '<div style="width: 100%; max-width: calc(100% - 19.75rem)">';
			echo '<section class="stripe stripe--single">';

			while($q->have_posts()) : $q->the_post();
				get_template_part('template-parts/stripe', 'cover');
			endwhile;

			wp_reset_postdata();

			echo '</section>';




 			$q = new WP_Query(['ignore_sticky_posts' => 1, 'post_status' => 'publish', 'posts_per_page' => 3, 'offset' => 1]);

			echo '<section class="stripe stripe--triple">';

			while($q->have_posts()) : $q->the_post();
				get_template_part('template-parts/stripe');
			endwhile;

			wp_reset_postdata();

			echo '</section>';

			echo '</div>';

  			get_sidebar('front');

		endif;

 		if($template === 'transparent_beauty') :
			$q = new WP_Query(['ignore_sticky_posts' => 1, 'post_status' => 'publish', 'posts_per_page' => 4, 'category__in' => [14]]);

			echo '<section class="stripe stripe--spacing">';

			while($q->have_posts()) : $q->the_post();
				get_template_part('template-parts/stripe', 'spacing');
			endwhile;

			wp_reset_postdata();

			echo '</section>';

		endif;
    }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$template = isset($instance['template']) ? esc_attr($instance['template']) : '';

		$list = [
			'television' => __('Телевизор', 'knife-theme'),
			'transparent_beauty' => __('Прозрачный: Прекрасное', 'knife-theme'),
			'double_seks' => __('Двойной: Секс', 'knife-theme'),
			'single_classic' => __('Растяжка: Классика', 'knife-theme'),
			'triple_classic' => __('Тройной: Классика', 'knife-theme')
		];
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Заголовок:', 'knife-theme'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
 			<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Шаблон:', 'knife-theme'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>">
<?php foreach($list as $key => $value) : ?>
				<option <?php selected($key, $template); ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php endforeach; ?>
			</select>
		</p>
<?php
	}


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
        // processes widget options to be saved

        foreach( $new_instance as $key => $value ) {
            $updated_instance[$key] = sanitize_text_field($value);
        }

        return $updated_instance;
    }
}


// It is time to register all required theme widgets
add_action('widgets_init', function() {
	register_widget('Knife_Stripe_Widget');
});
