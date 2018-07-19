<?php
/**
* Lead post meta
*
* Insert lead metabox to admin post screen
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


(new Knife_Post_Lead)->init();

class Knife_Post_Lead {
   /**
    * Backward compatibility meta name
    *
    * @access  private
    * @var     string
    */
    private $meta = 'lead-text';


   /**
    * Default post type lead text availible
    *
    * @access  private
    * @var     array
    */
    private $type = ['post'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public function init() {
        add_action('save_post', [$this, 'save_meta']);

        // add lead-text metabox
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        // update type array by filters
        add_action('init', [$this, 'set_type'], 20);
    }


    /**
     * Update type array by modules filters
     */
    public function set_type() {
        /**
         * Filter post lead support post types
         *
         * @since 1.3
         * @param array $type
         */
        $this->type = apply_filters('knife_post_lead_type', $this->type);
    }


    /**
     * Add lead-text metabox
     */
    public function add_metabox() {
        add_meta_box('knife-lead-metabox', __('Лид текст'), [$this, 'print_metabox'], $this->type, 'normal', 'low');
    }


    /**
     * Print wp-editor based metabox for lead-text meta
     */
    public function print_metabox($post, $box) {
        $lead = get_post_meta($post->ID, $this->meta, true);

        wp_editor($lead, 'knife-lead-editor', [
            'media_buttons' => false,
            'textarea_name' =>
            $this->meta,
            'teeny' => true,
            'tinymce' => false,
            'editor_height' => 100
        ]);
    }


    /**
     * Get post meta
     */
    public function get_meta($post = 0) {
        if(!$post = get_post($post))
            return false;

        $lead = get_post_meta($post->ID, $this->meta, true);

        if(strlen($lead) === 0)
            return false;

        return wpautop($lead);
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

        // Save lead-text meta
        if(!empty($_REQUEST[$this->meta]))
            update_post_meta($post_id, $this->meta, $_REQUEST[$this->meta]);
        else
            delete_post_meta($post_id, $this->meta);
    }
}
