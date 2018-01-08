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
	/**
	* Unique nonce for widget ajax requests
	*
	* @since	1.1
	* @access	private
	* @var		string
	*/
	private $nonce = 'knife-single-nonce';

	public function __construct() {
		$widget_ops = [
			'classname' => 'single',
			'description' => __('Выводит пост в виде широкого модуля', 'knife-theme'),
			'customize_selective_refresh' => true
		];

		parent::__construct('knife_theme_single', __('[НОЖ] На всю ширину', 'knife-theme'), $widget_ops);

		add_action('wp_ajax_knife-single-terms', [$this, 'widget_terms']);
		add_action('in_admin_footer', [$this, 'widget_script']);
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
			'posts_per_page' => 1,
			'offset' => 0,
			'cover' => 'default',
			'taxonomy' => 'category',
			'termlist' => []
		];

		$instance = wp_parse_args((array) $instance, $defaults);

		extract($instance);


		// Check cache before creating WP_Query object
		$q = get_transient($this->id);

		if($q === false) :

			$q = new WP_Query([
				'post_status' => 'publish',
				'ignore_sticky_posts' => 1,
				'offset' => $offset,
				'posts_per_page' => $posts_per_page,
				'tax_query' => [
					[
						'field' => 'id',
						'taxonomy' => $taxonomy,
						'terms' => $termlist
					],
				]
			]);

			set_transient($this->id, $q, 24 * HOUR_IN_SECONDS);

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

		if(taxonomy_exists($new_instance['taxonomy'])) {
			$taxonomy = $new_instance['taxonomy'];

			if(isset($_REQUEST['widget-id']) && $_REQUEST['widget-id'] == $this->id) {
				$posted_terms = [];

				if(isset($_POST['post_category']))
					$posted_terms = $_POST['post_category'];

				if(isset($_POST['tax_input'][$taxonomy]))
					$posted_terms = $_POST['tax_input'][$taxonomy];

				foreach($posted_terms as $term) {
					if(term_exists(absint($term), $taxonomy))
						$terms[] = absint($term);
				}
			}
		}

 		$instance = $old_instance;

		$instance['posts_per_page'] = absint($new_instance['posts_per_page']);
		$instance['offset'] = absint($new_instance['offset']);
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['cover'] = sanitize_text_field($new_instance['cover']);
 		$instance['taxonomy'] = sanitize_text_field($new_instance['taxonomy']);
		$instance['termlist'] = $terms;


		$this->remove_cache();

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
			'posts_per_page' => 1,
			'offset' => 0,
			'cover' => 'default',
			'taxonomy' => 'category',
			'termlist' => []
		];

		$instance = wp_parse_args((array) $instance, $defaults);

		$cover = [
			'defalut' => __('По умолчанию', 'knife-theme'),
			'cover' => __('Использовать подложку', 'knife-theme'),
			'nocover' => __('Убрать подложку', 'knife-theme')
		];

		$taxes = get_taxonomies([
			'public' => true
		], 'object');

		$terms = wp_terms_checklist(0, [
			'taxonomy' => $instance['taxonomy'],
			'selected_cats' => $instance['termlist'],
			'echo' => false
		]);


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
			__('Подложка карточек:', 'knife-theme')
		);

		foreach($cover as $name => $title) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['cover'], $name, false));
		}

		echo '</select>';


		// Taxonomies filter
		printf(
			'<p><label for="%1$s">%3$s</label><select class="widefat knife-single-taxonomy" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('taxonomy')),
 			esc_attr($this->get_field_name('taxonomy')),
			__('Фильтр записей:', 'knife-theme')
		);

		foreach($taxes as $name => $object) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $object->label, selected($instance['taxonomy'], $name, false));
		}

		echo '</select>';


		// Terms filter
		printf(
			'<ul class="cat-checklist categorychecklist knife-single-termlist" id="%1$s">%2$s</ul>',
			esc_attr($this->get_field_id('termlist')),
			$terms
		);


		// Posts count
		printf(
			'<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
			esc_attr($this->get_field_id('posts_per_page')),
			esc_attr($this->get_field_name('posts_per_page')),
			__('Количество записей:', 'knife-theme'),
			esc_attr($instance['posts_per_page'])
		);


		// Posts offset
		printf(
			'<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
			esc_attr($this->get_field_id('offset')),
			esc_attr($this->get_field_name('offset')),
			__('Пропустить записей:', 'knife-theme'),
			esc_attr($instance['offset'])
		);

	}

	/**
	 * Ajax handler for terms load
	 */
	public function widget_script() {
?>
		<script type="text/javascript">
			(function() {
				jQuery(document).on('change', '.knife-single-taxonomy', function() {
					var list = jQuery(this).closest('.widget-content').find('.knife-single-termlist');

					var data = {
						action: 'knife-single-terms',
						filter: jQuery(this).val(),
						nonce: '<?php echo wp_create_nonce($this->nonce); ?>'
					}

					jQuery.post(ajaxurl, data, function(response) {
						list.html(response);

						return list.show();
					});

					return list.hide();
				});
			})();
		</script>
<?php
	}


	/**
	 * Custom terms form by taxonomy name
	 */
	public function widget_terms() {
		check_ajax_referer($this->nonce, 'nonce');

		wp_terms_checklist(0, [
			'taxonomy' => esc_attr($_POST['filter'])
		]);

		wp_die();
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
	register_widget('Knife_Single_Widget');
});
