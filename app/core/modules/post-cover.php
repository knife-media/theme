<?php
/**
* Cover post meta
*
* Indicates how to show post in lists
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
	die;
}


new Knife_Post_Cover;

class Knife_Post_Cover {
	private $meta = '_knife-cover';

	public function __construct() {
		add_action('save_post', [$this, 'save_meta']);

		// add checkbox above thumnail widget
		add_filter('admin_post_thumbnail_html', [$this, 'print_checkbox'], 10, 3);
	}


 	/**
	 * Prints checkbox in post thumbnail editor metabox
	 */
	public function print_checkbox($content, $post_id, $thumbnail_id = '')  {
		if(get_post_type($post_id) !== 'post' || empty($thumbnail_id))
			return $content;

		$cover = get_post_meta($post_id, $this->meta, true);

		$checkbox = sprintf(
			'<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
			esc_attr($this->meta),
			__('Использовать подложку в списках', 'knife-theme'),
			checked($cover, 1, false)
		);

		return $checkbox . $content;
	}


	/**
	 * Save cover meta
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
