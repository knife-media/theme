<?php
/**
* Primary post tag
*
* Update current post tag metabox to add opportunity select primary tag
*
* @package knife-theme
* @since 1.3
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Primary_Tag;

class Knife_Primary_Tag {
    private $meta = 'primary-tag';

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // save meta on save post
        add_action('save_post', [$this, 'save_meta'], 15);

        // move primary tag to first position
        add_filter('get_the_tags', [$this, 'sort_tags']);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php']))
            return;

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'post')
            return;

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin scripts
        wp_enqueue_script('knife-primary-tag', $include . '/scripts/primary-tag.js', ['jquery'], $version);

        $options = [
            'howto' => __('Выберите главную метку поста. Она отобразится на карточке', 'knife-theme')
        ];

        // get current primary term
        $term_id = get_post_meta($post_id, $this->meta, true);

        if($term_id && $term = get_term($term_id)) {
            $options['primary'] = $term->name;
        }

        wp_localize_script('knife-primary-tag', 'knife_primary_tag', $options);
    }


    /**
     * Save post options
     */
    public function save_meta($post_id) {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if(!current_user_can('edit_post', $post_id))
            return;

        // Save meta
        if(empty($_REQUEST[$this->meta]))
            return delete_post_meta($post_id, $this->meta);

        $term = get_term_by('name', $_REQUEST[$this->meta], 'post_tag');

        if($term && $term->term_id > 0)
            update_post_meta($post_id, $this->meta, $term->term_id);
    }


    /**
     * Move primary tag to first position
     */
    public function sort_tags($items) {
        global $post;

        if($primary = get_post_meta($post->ID, $this->meta, true)) {
            $sorted = [];

            foreach($items as $item) {
                if($item->term_id === (int) $primary) {
                    array_unshift($sorted, $item);
                } else {
                    $sorted[] = $item;
                }
            }

            $items = $sorted;
        }

        return $items;
    }
}
