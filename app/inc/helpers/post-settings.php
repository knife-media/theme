<?php
/**
* Custom theme post settings
*
* Providing post cover on loops functionality and custom sub-title highlight
*
* @package knife-theme
* @since 1.1
*/

new Knife_Post_Settings;

class Knife_Post_Settings {
	private $cover   = '_knife-theme-cover';
	private $tagline = '_knife-theme-tagline';
	private $feature = '_knife-theme-feature';

	public function __construct() {
		add_action('admin_print_styles-post.php', [$this, 'print_admin_styles']);
		add_action('save_post_post', [$this, 'save_post_meta']);

		add_action('save_post', [$this, 'clear_widget_cache']);
 		add_action('deleted_post', [$this, 'clear_widget_cache']);

		add_filter('admin_post_thumbnail_html', [$this, 'print_cover_checkbox'], 10, 3);
		add_action('post_submitbox_misc_actions', [$this, 'print_feature_checkbox']);
		add_action('edit_form_after_title', [$this, 'print_tagline_input']);

		add_filter('the_title', [$this, 'add_post_tagline'], 10, 2);
 		add_filter('document_title_parts', [$this, 'add_site_tagline'], 10, 1);
	}


 	/**
	 * Filter the post title on the Posts screen, and on the front-end
	 */
	public function add_post_tagline($title, $post_id) {
		$tagline = get_post_meta($post_id, $this->tagline, true);

		if(empty($tagline))
			return $title;

		if(is_admin())
			return "{$tagline} {$title}";

		return "<strong>{$tagline}</strong> {$title}";

	}

	/**
	 * Filter the document title in the head.
	 */
	public function add_site_tagline($title) {
		global $post;

		if(!is_singular() || !isset($post->ID))
			return $title;

		$tagline = get_post_meta($post->ID, $this->tagline, true);
		$tagline = sanitize_text_field($tagline);

		if(empty($tagline))
			return $title;

		$title['title'] = "{$tagline} {$title['title']}";

		return $title;
	}


	/**
	 * Shows tagline input right after post title form on admin page
	 */
	public function print_tagline_input() {
		$post_id = get_the_ID();

		if (get_post_type($post_id) !== 'post')
			return;

		$tagline = get_post_meta($post_id, $this->tagline, true);
 		$tagline = sanitize_text_field($tagline);

		printf(
			'<input id="knife-theme-tagline" type="text" class="large-text" value="%1$s" name="%2$s" placeholder="%3$s">',
			esc_attr($tagline),
			esc_attr($this->tagline),
			__('Подзаголовок', 'knife-theme')
		);
	}


 	/**
	 * Prints checkbox in post publish action section
	 *
	 * We using feature post meta for feature widget
	 */
	public function print_feature_checkbox() {
		$post_id = get_the_ID();

		if(get_post_type($post_id) !== 'post')
			return;

 		$feature = get_post_meta($post_id, $this->feature, true);

		printf(
			'<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
			esc_attr($this->feature),
			__('Добавить запись в фичер', 'knife-theme'),
			checked($feature, 1, false)
		);
	}


	/**
	 * Prints checkbox in post thumbnail editor metabox
	 */
	public function print_cover_checkbox($content, $post_id, $thumbnail_id = '')  {
		if(get_post_type($post_id) !== 'post' || empty($thumbnail_id))
			return $content;

		$cover = get_post_meta($post_id, $this->cover, true);

		$checkbox = sprintf(
			'<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
			esc_attr($this->cover),
			__('Использовать подложку в списках', 'knife-theme'),
			checked($cover, 1, false)
		);

		return $checkbox . $content;
	}


	/**
	 * Save post options
	 */
	public function save_post_meta($post_id) {
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if(!current_user_can('edit_post', $post_id))
			return;

		// Save tagline meta
		if(!empty($_REQUEST[$this->tagline]))
			update_post_meta($post_id, $this->tagline, trim($_REQUEST[$this->tagline]));
		else
			delete_post_meta($post_id, $this->tagline);


		// Save cover meta
		if(!empty($_REQUEST[$this->cover]))
			update_post_meta($post_id, $this->cover, 1);
		else
			delete_post_meta($post_id, $this->cover);

 		// Save feature meta
		if(!empty($_REQUEST[$this->feature]))
			update_post_meta($post_id, $this->feature, 1);
		else
			delete_post_meta($post_id, $this->feature);
	}


	/**
	 * Remove widgets cache on save or delete post
	 */
	public function clear_widget_cache() {
		$sidebars = get_option('sidebars_widgets');

		foreach($sidebars as $sidebar) {
			if(!is_array($sidebar))
				continue;

			foreach($sidebar as $widget)
				delete_transient($widget);
		}
	}


	/**
	 * Remove useless tinymce toolbars and fix tagline admin style
	 */
	public function print_admin_styles() {
	?>
		<style type="text/css">
			.wp-admin .mce-inline-toolbar-grp {
				display: none;
			}

			.wp-admin #post-body-content {
				position: relative;
			}

			.wp-admin #poststuff #titlewrap {
				padding-left: 205px;
			}

			.wp-admin #knife-theme-tagline {
				position: absolute;
				top: 0;
				left: 0;
				padding: 3px 8px;
				font-size: 1.7em;
				line-height: 100%;
				height: 1.7em;
				width: 200px;
				outline: 0;
				margin: 0 0 .5em;
			}
		</style>
	<?php
	}
}
