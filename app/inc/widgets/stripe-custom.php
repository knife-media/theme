<?php
/**
 * Stripe widget
 *
 * Include different size stripes
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Custom_Widget extends WP_Widget {
     /**
     * Unique nonce for widget ajax requests
     *
     * @since	1.1
     * @access  private
     * @var     string
     */
	private $nonce = 'knife-widget-nonce';

	public function __construct() {
		$widget_ops = [
			'classname' => 'custom',
			'description' => __('Выводит полосу постов по заданному критерию в виде карточек.', 'knife-theme'),
			'customize_selective_refresh' => true
		];

		parent::__construct('knife_theme_custom', __('[НОЖ] Список записей', 'knife-theme'), $widget_ops);

		add_action('save_post', [$this, 'remove_cache']);
		add_action('deleted_post', [$this, 'remove_cache']);
		add_action('switch_theme', [$this, 'remove_cache']);

		add_action('wp_ajax_knife-widget-terms', [$this, 'widget_terms']);
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

		if(taxonomy_exists($new_instance['taxes'])) {
			$taxonomy = $new_instance['taxes'];

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

		$instance['cat'] = absint($new_instance['cat']);
		$instance['posts_per_page'] = absint($new_instance['posts_per_page']);
		$instance['offset'] = absint($new_instance['offset']);
		$instance['size'] = sanitize_text_field($new_instance['size']);
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['cover'] = sanitize_text_field($new_instance['cover']);
 		$instance['taxes'] = sanitize_text_field($new_instance['taxes']);
		$instance['terms'] = $terms;


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
	function form($instance) {
		$defaults = [
			'title' => '',
			'posts_per_page' => 3,
			'cat' => 0,
			'offset' => 0,
			'size' => 'triple',
			'cover' => 'default',
			'taxes' => 'category',
			'terms' => []
		];

		$instance = wp_parse_args((array) $instance, $defaults);

		$size = [
			'triple' => __('Три в ряд', 'knife-theme'),
			'double' => __('Два в ряд', 'knife-theme'),
			'single' => __('На всю ширину', 'knife-theme')
		];

		$cover = [
			'defalut' => __('По умолчанию', 'knife-theme'),
			'cover' => __('Использовать подложку', 'knife-theme'),
			'nocover' => __('Убрать подложку', 'knife-theme')
		];

		$taxes = get_taxonomies([
			'public' => true
		], 'object');

		$terms = wp_terms_checklist(0, [
			'taxonomy' => $instance['taxes'],
			'selected_cats' => $instance['terms'],
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


		// Template manage
		printf(
			'<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('size')),
 			esc_attr($this->get_field_name('size')),
			__('Шаблон виджета:', 'knife-theme')
		);

		foreach($size as $name => $title) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['size'], $name, false));
		}

		echo '</select>';


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
			'<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
			esc_attr($this->get_field_id('taxes')),
 			esc_attr($this->get_field_name('taxes')),
			__('Фильтр записей:', 'knife-theme')
		);

		foreach($taxes as $name => $object) {
			printf('<option value="%1$s"%3$s>%2$s</option>', $name, $object->label, selected($instance['taxes'], $name, false));
		}

		echo '</select>';


		// Terms filter
		printf(
			'<ul class="cat-checklist categorychecklist" id="%1$s">%2$s</ul>',
			esc_attr($this->get_field_id('terms')),
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


		// Some js for ajax loading
?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var taxonomy = '#' + '<?php echo $this->get_field_id('taxes'); ?>';
				var termlist = '#' + '<?php echo $this->get_field_id('terms'); ?>';

				jQuery(document).on('change', taxonomy, function() {
					var filter = jQuery(this).val();

					var data = {
						action: 'knife-widget-terms',
						filter: filter,
						nonce: '<?php echo wp_create_nonce($this->nonce); ?>'
					}

					jQuery(termlist).hide();

					jQuery.post(ajaxurl, data, function(response) {
						jQuery(termlist).html(response);

						return jQuery(termlist).show();
					});
				});
			});
		</script>
<?php
	}


	/**
	 * Remove cache on save and delete post
	 */
	public function remove_cache() {
		delete_transient($this->id);
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
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
	register_widget('Knife_Custom_Widget');
});
