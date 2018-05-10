<?php
/**
* Post sticker meta
*
* Custom optional image for posts
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
	die;
}


new Knife_Custom_Background;

class Knife_Custom_Background {
    private $meta = '_knife-term-background';

	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'add_assets']);

		// term edit page
		add_action('special_edit_form_fields', [$this, 'print_row'], 10, 2);
		add_action('edited_special', [$this, 'save_meta']);

		// frontend styles
        add_action('wp_enqueue_scripts', [$this, 'print_background'], 13);


		add_action('customize_register', [$this, 'update_customizer']);
	}


	/**
	 * Enqueue assets to term edit screen only
	 */
	public function add_assets($hook) {
		if(!in_array($hook, ['term.php']))
			return;

		$version = wp_get_theme()->get('Version');
		$include = get_template_directory_uri() . '/core/include';

 		// insert admin styles
 		wp_enqueue_style('knife-custom-background', $include . '/styles/custom-background.css', [], $version);

		// insert wp media scripts
		wp_enqueue_media();

        // insert admin scripts
		wp_enqueue_script('knife-custom-background', $include . '/scripts/custom-background.js', ['jquery'], $version);

		$options = [
			'choose' => __('Выберите фоновое изображение', 'knife-theme')
		];

		wp_localize_script('knife-custom-background', 'knife_custom_background', $options);
	}


	/**
	 * Update background controls in admin customizer
	 */
	public function update_customizer($wp_customize) {
		// We don't need these options at the moment
		$wp_customize->remove_control('background_preset');
		$wp_customize->remove_control('background_attachment');
		$wp_customize->remove_control('background_repeat');
		$wp_customize->remove_control('background_position');
	}


 	/**
	 * Display custom background options row
	 */
	public function print_row($term, $taxonomy) {
		$include = get_template_directory() . '/core/include';

		include_once($include . '/templates/background-row.php');
	}


	/**
	 * Save image meta
	 */
	public function save_meta($term_id) {
 		if(!current_user_can('edit_term', $term_id))
			return;

 		if(!empty($_REQUEST[$this->meta]))
			update_term_meta($term_id, $this->meta, $_REQUEST[$this->meta]);
		else
			delete_term_meta($term_id, $this->meta);
	}


	/**
	 * Print fixed element with custom background
	 */
	public function print_background() {
		$backdrop = [];

		// set color style
		$color = get_background_color();

		if($color === get_theme_support('custom-background', 'default-color'))
			$color = false;

		if($color)
			$backdrop['color'] = $color;


		// set background image style
		$image = $this->apply_meta('image', get_background_image());

		if($image) {
			$backdrop['image'] = set_url_scheme($image);

            // set background size style
            $size = $this->apply_meta('size', get_theme_mod('background_size'));

            if(in_array($size, ['auto', 'contain', 'cover'], true))
                $backdrop['size'] = $size;
        }


        if(count($backdrop) > 0)
            wp_localize_script('knife-theme', 'knife_backdrop', $backdrop);
	}


	private function apply_meta($option, $default) {
		$meta = $this->get_meta();

		if(empty($meta[$option]))
			return $default;

		return $meta[$option];
	}


	/**
	 * Get custom term meta if exists
	 */
	private function get_meta() {
		$meta = false;

		// taxonomy archive
		if(is_tax('special')) {
			$meta = get_term_meta(get_queried_object_id(), $this->meta, true);
		}

		// single post with term
		if(is_single() && has_term('', 'special')) {
			$term = wp_get_post_terms(get_queried_object_id(), 'special');

			// check only first term
			$meta = get_term_meta($term[0]->term_id, $this->meta, true);
		}

		return $meta;
	}
}
