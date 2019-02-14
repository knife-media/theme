<?php
/**
 * Lead post meta
 *
 * Insert lead metabox to admin post screen
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.7
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Post_Lead {
   /**
    * Backward compatibility meta name
    *
    * @access  private
    * @var     string
    */
    private static $post_meta = 'lead-text';


   /**
    * Default post type lead text availible
    *
    * @access  private
    * @var     array
    */
    private static $post_type = ['post', 'club', 'select', 'generator', 'quiz'];


    /**
     * Lead save nonce
     *
     * @since   1.5
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-lead-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Save meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Add lead-text metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Prepend lead to feeds content
        add_filter('the_content_feed', [__CLASS__, 'upgrade_feeds'], 12);
    }


    /**
     * Add lead-text metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-lead-metabox', __('Лид текст'), [__CLASS__, 'print_metabox'], self::$post_type, 'normal', 'high');
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     */
    public static function print_metabox($post, $box) {
        $lead = get_post_meta($post->ID, self::$post_meta, true);

        wp_editor($lead, 'knife-lead-editor', [
            'media_buttons' => true,
            'textarea_name' => self::$post_meta,
            'teeny' => true,
            'tinymce' => [
                'toolbar1' => 'link'
            ],
            'quicktags' => [
                'buttons' => 'link'
            ],
            'editor_height' => 100,
            'drag_drop_upload' => false
        ]);

        wp_nonce_field('metabox', self::$metabox_nonce);
    }


    /**
     * Get lead post meta
     */
    public static function get_lead($post = 0, $post_lead = '') {
        if(!$post = get_post($post)) {
            return $lead;
        }

        $post_lead = get_post_meta($post->ID, self::$post_meta, true);

        if(strlen($post_lead) > 0) {
            $post_lead = wpautop($post_lead);
        }

        return $post_lead;
    }


    /**
     * Prepend lead to feed content
     *
     * @since 1.7
     */
    public static function upgrade_feeds($content) {
        $post_lead = self::get_lead();

        if(strlen($post_lead) > 0) {
            $content = $post_lead . $content;
        }

        return $content;
    }


    /**
     * Save post options
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save lead-text meta
        if(empty($_REQUEST[self::$post_meta])) {
            return delete_post_meta($post_id, self::$post_meta);
        }

        return update_post_meta($post_id, self::$post_meta, $_REQUEST[self::$post_meta]);
    }
}


Knife_Post_Lead::load_module();
