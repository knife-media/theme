<?php
/**
 * Post tagline meta
 *
 * Add second title to post
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.7
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Post_Tagline {
   /**
    * Post meta name
    *
    * @access  private
    * @var     string
    */
    private static $post_meta = '_knife-tagline';


   /**
    * Default post type tagline availible
    *
    * @access  private
    * @var     array
    */
    private static $post_type = ['post', 'club', 'quiz', 'generator'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Include scripts admin page only
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // Save tagline meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Tagline post meta
        add_action('edit_form_after_title', [__CLASS__, 'print_input']);

        // Update title with tagline
        add_filter('the_title', [__CLASS__, 'post_tagline'], 10, 2);
        add_filter('document_title_parts', [__CLASS__, 'site_tagline'], 10, 1);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style('knife-tagline-input', $include . '/styles/tagline-input.css', [], $version);
    }


    /**
     * Shows tagline input right after post title form on admin page
     */
    public static function print_input() {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        $tagline = get_post_meta($post_id, self::$post_meta, true);
        $tagline = sanitize_text_field($tagline);

        printf(
            '<input id="knife-tagline-input" type="text" class="large-text" value="%1$s" name="%2$s" placeholder="%3$s">',
            esc_attr($tagline),
            esc_attr(self::$post_meta),
            __('Подзаголовок', 'knife-theme')
        );
    }


    /**
     * Filter the post title on the Posts screen, and on the front-end
     */
    public static function post_tagline($title, $post_id, $raw = true) {
        $tagline = get_post_meta($post_id, self::$post_meta, true);

        if(empty($tagline)) {
            return $title;
        }

        if(is_admin() && $raw) {
            return "{$title} {$tagline}";
        }

        return "{$title} <em>{$tagline}</em>";
    }


    /**
     * Filter the document title in the head.
     */
    public static function site_tagline($title) {
        global $post;

        if(!is_singular() || !isset($post->ID)) {
            return $title;
        }

        $tagline = get_post_meta($post->ID, self::$post_meta, true);
        $tagline = sanitize_text_field($tagline);

        if(empty($tagline)) {
            return $title;
        }

        $title['title'] = "{$title['title']} {$tagline}";

        return $title;
    }


    /**
     * Save post options
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save tagline meta
        if(empty($_REQUEST[self::$post_meta])) {
            return delete_post_meta($post_id, self::$post_meta);
        }

        return update_post_meta($post_id, self::$post_meta, trim($_REQUEST[self::$post_meta]));
    }
}


/**
 * Load current module environment
 */
Knife_Post_Tagline::load_module();
