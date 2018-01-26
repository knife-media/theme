<?php
/**
* Common widgets handler
*
* Use for cross-widget functions
*
* @package knife-theme
* @since 1.2
*/

if (!defined('WPINC')) {
	die;
}

new Knife_Widget_Handler;

class Knife_Widget_Handler {
 	/**
	* Unique nonce for widget ajax requests
	*
	* @since	1.2
	* @access	private
	* @var		string
	*/
	private $nonce = 'knife-widget-nonce';

	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'add_assets']);

		// include all widgets
		add_action('after_setup_theme', [$this, 'include_widgets']);

		// clear cache
 		add_action('added_post_meta', [$this, 'clear_cache']);
  		add_action('deleted_post_meta', [$this, 'clear_cache']);
   		add_action('updated_post_meta', [$this, 'clear_cache']);
 		add_action('deleted_post', [$this, 'clear_cache']);
		add_action('save_post', [$this, 'clear_cache']);
		add_action('widget_update_callback', [$this, 'clear_cache']);

		add_action('wp_ajax_knife_widget_terms', [$this, 'ajax_terms']);
	}


  	/**
	 * Enqueue assets to admin post screen only
	 */
	public function add_assets($hook) {
		$version = wp_get_theme()->get('Version');
		$include = get_template_directory_uri() . '/core/include';

		wp_enqueue_script('knife-widget-handler', $include . '/scripts/widget-handler.js', ['jquery'], $version);

		$options = [
			'nonce' => wp_create_nonce($this->nonce)
		];

		wp_localize_script('knife-widget-handler', 'knife_widget_handler', $options);
	}

	/**
	 * Include widgets classes
	 */
	public function include_widgets() {
		$widgets = get_template_directory() . '/core/widgets/';

		foreach(['recent', 'triple', 'double', 'single', 'feature', 'details', 'transparent'] as $id) {
			include_once($widgets . $id . '.php');
		}
	}

	/**
	 * Remove widgets cache on save or delete post
	 */
	public function clear_cache($instance) {
		$sidebars = get_option('sidebars_widgets');

		foreach($sidebars as $sidebar) {
			if(!is_array($sidebar))
				continue;

			foreach($sidebar as $widget)
				delete_transient($widget);
		}

		return $instance;
	}


	/**
	 * Custom terms form by taxonomy name
	 */
	public function ajax_terms() {
		check_ajax_referer($this->nonce, 'nonce');

		wp_terms_checklist(0, [
			'taxonomy' => esc_attr($_POST['filter'])
		]);

		wp_die();
	}
}
