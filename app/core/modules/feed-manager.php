<?php
/**
* Feed manager
*
* Allow users to manage feed options from admin dashboard
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
	die;
}


new Knife_Feed_Manager;

class Knife_Feed_Manager {
	private $meta = '_knife-feed';

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

 		$exclude = get_post_meta($post_id, $this->meta, true);

		printf(
			'<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
			esc_attr($this->meta),
			__('Исключить запись из RSS', 'knife-theme'),
			checked($exclude, 1, false)
		);
	}


	/**
	 * Save feed post meta
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
