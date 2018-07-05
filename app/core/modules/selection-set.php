<?php
/**
* Selection set
*
* Custom post type for manual articles collection
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_Selection_Set;

class Knife_Selection_Set {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'select';

    /**
     * Unique meta using for saving post data
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $meta = '_knife-select';


    public function __construct() {
        add_action('save_post', [$this, 'save_meta']);

        // add scripts to admin page
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // add selection metabox
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        // register club post type
        add_action('init', [$this, 'register_selection']);

        // filter content to show custom links
        add_filter('the_content', [$this, 'update_content']);

        // add post lead to post type editor
        add_filter('knife_post_lead_type', function($default) {
            $default[] = $this->slug;

            return $default;
        });
    }


    /**
     * Register selection post type
     */
    public function register_selection() {
        register_post_type($this->slug, [
            'labels'                => [
                'name'              => __('Подборка', 'knife-theme'),
                'singular_name'     => __('Запись в подборку', 'knife-theme'),
                'add_new'           => __('Добавить запись', 'knife-theme'),
                'menu_name'         => __('Подборки', 'knife-theme')
            ],
            'label'                 => __('Подборка', 'knife-theme'),
            'description'           => __('Подборки статей', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-images-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Update content with custom links
     */
    public function update_content($content) {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== $this->slug)
            return;

        $items = get_post_meta($post_id, $this->meta . '-items');

        if(count($items) === 0)
            return $content;

        foreach($items as $item) {
            // check if link and text not empty
            if(empty($item['link']) || empty($item['text']))
                continue;

            $link_id = url_to_postid($item['link']);

            $output = '';

            if($link_id > 0) {

                $output .= knife_theme_meta([
                    'opts' => ['author', 'date', 'category'],
                    'before' => '<div class="post__links-meta meta">',
                    'after' => '</div>',
                    'echo' => false
                ]);
            }

            $output .= sprintf('<a class="post_links-title" href="%s">%s</a>',
                esc_url($item['link']),
                esc_html($item['text'])
            );

            $content .= '<div class="post_links-item">' . $output . '</div>';
        }

        return $content;
    }


    /**
     * Add selection metabox
     */
    public function add_metabox() {
        add_meta_box('knife-selection-metabox', __('Подборка статей'), [$this, 'display_metabox'], $this->slug, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public function add_assets($hook) {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== $this->slug)
            return;

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style('knife-selection-set', $include . '/styles/selection-set.css', [], $version);

        // insert admin scripts
        wp_enqueue_script('knife-selection-set', $include . '/scripts/selection-set.js', ['jquery', 'jquery-ui-sortable'], $version);
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     */
    public function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/selection-set.php');
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

        // update items meta
        $this->_update_items($this->meta . '-items', $post_id);
    }


    /**
     * Update selection items meta from post-metabox
     */
    private function _update_items($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query]))
            return;

        // delete selection post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            foreach($item as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i]))
                    $i++;

                switch($key) {
                    case 'text':
                        $value = sanitize_text_field($value);
                        break;

                    case 'link':
                        $value = esc_url($value);
                        break;
                }

                $meta[$i][$key] = $value;
            }
        }

        foreach($meta as $item) {
            if(!empty($item['text']) && !empty($item['link'])) {
                add_post_meta($post_id, $query, $item);
            }
        }
    }
}
