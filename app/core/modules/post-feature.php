<?php
/**
* Feature post meta
*
* Use to mark post as featured or not
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
	die;
}


new Knife_Post_Feature;

class Knife_Post_Feature {
	private $meta = '_knife-feature';

	public function __construct() {
		add_action('save_post', [$this, 'save_meta']);

		// feature post meta
		add_action('post_submitbox_misc_actions', [$this, 'print_checkbox']);
	}


	/**
	 * Prints checkbox in post publish action section
	 */
	public function print_checkbox() {
		$post_id = get_the_ID();

		if(get_post_type($post_id) !== 'post')
			return;

 		$feature = get_post_meta($post_id, $this->meta, true);

		printf(
			'<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
			esc_attr($this->meta),
			__('Добавить запись в фичер', 'knife-theme'),
			checked($feature, 1, false)
		);
	}


	/**
	 * Save feature post meta
	 */
	public function save_meta($post_id) {
		if(get_post_type($post_id) !== 'post')
			return;

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if(!current_user_can('edit_post', $post_id))
			return;

		if(!empty($_REQUEST[$this->meta]))
			update_post_meta($post_id, $this->meta, 1);
		else
			delete_post_meta($post_id, $this->meta);
	}
}
