<?php
/**
* Lead post meta
*
* Insert lead metabox to admin post screen
*
* @package knife-theme
* @since 1.2
* @version 1.4
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
    private static $meta = 'lead-text';


   /**
    * Default post type lead text availible
    *
    * @access  private
    * @var     array
    */
    private static $type = ['post', 'club', 'select'];


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
    }


    /**
     * Add lead-text metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-lead-metabox', __('Лид текст'), [__CLASS__, 'print_metabox'], self::$type, 'normal', 'low');
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     */
    public static function print_metabox($post, $box) {
        $lead = get_post_meta($post->ID, self::$meta, true);

        wp_editor($lead, 'knife-lead-editor', [
            'media_buttons' => false,
            'textarea_name' =>
            self::$meta,
            'teeny' => true,
            'tinymce' => true,
            'tinymce' => [
                'toolbar1' => 'bold,italic,link',
                'block_formats' => 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4'
            ],
            'editor_height' => 100,
            'drag_drop_upload' => false
        ]);
    }


    /**
     * Get lead post meta
     */
    public static function get_lead($post = 0, $lead = '') {
        if(!$post = get_post($post)) {
            return $lead;
        }

        $lead = get_post_meta($post->ID, self::$meta, true);

        if(strlen($lead) > 0) {
            $lead = wpautop($lead);
        }

        return $lead;
    }

    /**
     * Save post options
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save lead-text meta
        if(empty($_REQUEST[self::$meta])) {
            return delete_post_meta($post_id, self::$meta);
        }

        return update_post_meta($post_id, self::$meta, $_REQUEST[self::$meta]);
    }
}


Knife_Post_Lead::load_module();
