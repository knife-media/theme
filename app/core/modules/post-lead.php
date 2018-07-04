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


new Knife_Post_Lead;

class Knife_Post_Lead {
    private $meta = 'lead-text';

    private $type = ['post'];

    public function __construct() {
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
        $default = $this->type;

        $this->type = apply_filters('knife_post_lead_type', $default);
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
