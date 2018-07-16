<?php
/**
* Selection type
*
* Custom post type for manual articles select
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_Select_Links;

class Knife_Select_Links {
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

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // Add select metabox
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        // Register select post type
        add_action('init', [$this, 'register_select']);

        // Filter content to show custom links
        add_filter('the_content', [$this, 'update_content']);

        // Add post lead to post type editor
        add_filter('knife_post_lead_type', function($default) {
            $default[] = $this->slug;

            return $default;
        });
    }


    /**
     * Register select post type
     */
    public function register_select() {
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

        if(get_post_type($post_id) !== $this->slug) {
            return $content;
        }

        $html = false;//get_transient("knife_{$this->slug}_{$post_id}");

        if($html === false) {
            $items = get_post_meta($post_id, $this->meta . '-items');

            ob_start();

            foreach($items as $item) {
                $this->process_item($item);
            }

            $html = ob_get_clean();
            set_transient("knife_{$this->slug}_{$post_id}", $html, 24 * HOUR_IN_SECONDS);
        }

        return sprintf('<div class="post__content-selects">%s</div>', $html);
    }


    /**
     * Add select metabox
     */
    public function add_metabox() {
        add_meta_box('knife-select-metabox', __('Подборка статей'), [$this, 'display_metabox'], $this->slug, 'normal', 'high');
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
        wp_enqueue_style('knife-select-links', $include . '/styles/select-links.css', [], $version);

        // insert admin scripts
        wp_enqueue_script('knife-select-links', $include . '/scripts/select-links.js', ['jquery', 'jquery-ui-sortable'], $version);
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     */
    public function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/select-metabox.php');
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

        // Update items meta
        $this->update_items($this->meta . '-items', $post_id);

        // Remove html cache
        delete_transient("knife_{$this->slug}_{$post_id}");
    }


    /**
     * Update select items meta from post-metabox
     */
    private function update_items($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query]))
            return;

        // delete select post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            foreach($item as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i])) {
                    $i++;
                }

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


    private function process_item($item) {
        if(empty($item['text']) || empty($item['link'])) {
            return;
        }

        $post_id = url_to_postid($item['link']);

        echo '<div class="select">';

        if($post_id > 0) {
            global $post;

            $post = get_post($post_id);
            setup_postdata($post);

            the_info(
                '<div class="select__meta meta">', '</div>',
                ['author', 'date']
            );

            wp_reset_postdata();
        }

        printf('<a class="select__link" href="%2$s">%1$s</a>',
            esc_html($item['text']),
            esc_url($item['link'])
        );

        echo '</div>';
    }
}
