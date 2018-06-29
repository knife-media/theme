<?php
/**
* Post tagline meta
*
* Add second title to post
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Post_Tagline;

class Knife_Post_Tagline {
    private $meta = '_knife-tagline';

    private $type = ['post'];

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);
        add_action('save_post', [$this, 'save_meta']);

        // tagline post meta
        add_action('edit_form_after_title', [$this, 'print_input']);

        add_filter('the_title', [$this, 'post_tagline'], 10, 2);
        add_filter('document_title_parts', [$this, 'site_tagline'], 10, 1);
    }


     /**
     * Enqueue assets to admin post screen only
     */
    public function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php']))
            return;

        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), $this->type))
            return;

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style('knife-post-tagline', $include . '/styles/post-tagline.css', [], $version);
    }


    /**
     * Shows tagline input right after post title form on admin page
     */
    public function print_input() {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), $this->type))
            return;

        $tagline = get_post_meta($post_id, $this->meta, true);
        $tagline = sanitize_text_field($tagline);

        printf(
            '<input id="knife-tagline-input" type="text" class="large-text" value="%1$s" name="%2$s" placeholder="%3$s">',
            esc_attr($tagline),
            esc_attr($this->meta),
            __('Подзаголовок', 'knife-theme')
        );
    }


     /**
     * Filter the post title on the Posts screen, and on the front-end
     */
    public function post_tagline($title, $post_id) {
        $tagline = get_post_meta($post_id, $this->meta, true);

        if(empty($tagline))
            return $title;

        if(is_admin())
            return "{$title} {$tagline}";

        return "{$title} <em>{$tagline}</em>";

    }


    /**
     * Filter the document title in the head.
     */
    public function site_tagline($title) {
        global $post;

        if(!is_singular() || !isset($post->ID))
            return $title;

        $tagline = get_post_meta($post->ID, $this->meta, true);
        $tagline = sanitize_text_field($tagline);

        if(empty($tagline))
            return $title;

        $title['title'] = "{$title['title']} {$tagline}";

        return $title;
    }


    /**
     * Save post options
     */
    public function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), $this->type))
            return;

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if(!current_user_can('edit_post', $post_id))
            return;

        // Save tagline meta
        if(!empty($_REQUEST[$this->meta]))
            update_post_meta($post_id, $this->meta, trim($_REQUEST[$this->meta]));
        else
            delete_post_meta($post_id, $this->meta);
    }
}
