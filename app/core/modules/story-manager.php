<?php
/**
* Story manager
*
* Custom story post type
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
	die;
}

new Knife_Story_Manager;

class Knife_Story_Manager {
 	/**
	* Unique slug using for custom post type register and url
	*
	* @since	1.3
	* @access	private
	* @var		string
	*/
	private $slug = 'story';

	public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // register story post type
        add_action('init', [$this, 'register_story']);

		// core post metabox
		add_action('add_meta_boxes', [$this, 'add_metabox']);

        // save story meta
 		add_action('save_post', [$this, 'save_meta']);
    }


    /**
	 * Enqueue assets to admin post screen only
	 */
	public function add_assets($hook) {
		if(!in_array($hook, ['post.php', 'post-new.php']))
			return;

		$post_id = get_the_ID();

		if(get_post_type($post_id) !== $this->slug)
			return;

		$version = wp_get_theme()->get('Version');
		$include = get_template_directory_uri() . '/core/include';

		// insert admin styles
 		wp_enqueue_style('knife-story-manager', $include . '/styles/story-manager.css', [], $version);

        // insert admin scripts
		wp_enqueue_script('knife-story-manager', $include . '/scripts/story-manager.js', ['jquery', 'jquery-ui-sortable'], $version);

		$options = [
			'choose' => __('Выберите фоновое изображение', 'knife-theme')
		];

		wp_localize_script('knife-story-manager', 'knife_story_manager', $options);
	}


     /**
     * Register story post type
     */
    public function register_story() {
		register_post_type($this->slug, [
			'labels'                => [
				'menu_name'             => __('Истории', 'knife-theme'),
				'name_admin_bar'        => __('Истории', 'knife-theme'),
				'parent_item_colon'     => __('Родительская история:', 'knife-theme' ),
				'all_items'             => __('Все истории', 'knife-theme' ),
				'add_new_item'          => __('Добавить новую историю', 'knife-theme' ),
				'add_new'               => __('Добавить новую', 'knife-theme' ),
				'new_item'              => __('Новая история', 'knife-theme' ),
				'edit_item'             => __('Редактировать историю', 'knife-theme' ),
				'update_item'           => __('Обновить историю', 'knife-theme' ),
				'view_item'             => __('Просмотреть историю', 'knife-theme' ),
				'view_items'            => __('Просмотреть истории', 'knife-theme' ),
				'search_items'          => __('Искать историю', 'knife-theme'),
				'insert_into_item'      => __('Добавить в историю', 'knife-theme'),
				'not_found'             => __('Историй не найдено', 'knife-theme'),
				'not_found_in_trash'    => __('В корзине ничего не найдено', 'knife-theme')
			],
 			'label'                 => __('Истории', 'knife-theme'),
			'description'           => __('Слайды с интерактивными историями', 'knife-theme'),
			'supports'              => ['title', 'thumbnail', 'revisions'],
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 10,
			'menu_icon'             => 'dashicons-slides',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		]);
    }

 	/**
	 * Add story manage metabox
	 */
	public function add_metabox() {
		add_meta_box('knife-story-metabox', __('Слайды истории', 'knife-theme'), [$this, 'display_metabox'], $this->slug, 'normal', 'high');
	}


	/**
	 * Display push sending metabox
	 */
	public function display_metabox() {
		$include = get_template_directory() . '/core/include';

  		include_once($include . '/templates/story-metabox.php');
	}


    /**
	 * Save post options
	 */
	public function save_meta($post_id) {
		if(get_post_type($post_id) !== $this->slug)
			return;

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if(!current_user_can('edit_post', $post_id))
			return;

        // Delete post meta to create it again below
        delete_post_meta($post_id, $this->slug);

        if(empty($_REQUEST[$this->slug]))
            return;

        $meta = $this->_generate_meta();

        foreach($meta as $item) {
            add_post_meta($post_id, $this->slug, $item);
        }
	}


    /**
     * Generate meta from post request
     */
    private function _generate_meta($meta = [], $i = 0) {
        foreach($_REQUEST[$this->slug] as $args) {
            foreach($args as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i]))
                    $i++;

                if(empty($value))
                    continue;

                switch($key) {
                    case 'image':
                        $value = esc_url($value);

                        break;

                    case 'color':
                        $value = absint($value);

                        break;

                    case 'text':
                        $value = esc_html($value);

                        break;
                }

                $meta[$i][$key] = $value;
            }
        }

        return $meta;
    }
}
