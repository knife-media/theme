<?php
/**
 * Primary post tag
 *
 * Update current post tag metabox to add opportunity select primary tag
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.12
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Primary_Tag {
    /**
     * Post meta name
     *
     * @access  public
     * @var     string
     */
    public static $meta_primary = '_knife-primary-tag';


    /**
     * Post types primary tag availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'generator', 'quiz'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Enqueue scripts only admin side
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // Save meta on save post
        add_action('save_post', [__CLASS__, 'save_meta'], 15);

        // Move primary tag to first position
        add_filter('get_the_tags', [__CLASS__, 'sort_tags']);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        if(!in_array(get_post_type($post->ID), self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin scripts
        wp_enqueue_script('knife-primary-tagbox', $include . '/scripts/primary-tagbox.js', ['jquery'], $version);

        $options = [
            'howto' => __('Выберите главную метку поста. Она отобразится на карточке', 'knife-theme')
        ];

        // get current primary term
        $term_id = get_post_meta($post->ID, self::$meta_primary, true);

        if($term_id && $term = get_term($term_id)) {
            $options['primary'] = $term->name;
        }

        wp_localize_script('knife-primary-tagbox', 'knife_primary_tagbox', $options);
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

        // Save meta
        if(isset($_REQUEST[self::$meta_primary])) {
            $term = get_term_by('name', $_REQUEST[self::$meta_primary], 'post_tag');

            if($term && $term->term_id > 0) {
                update_post_meta($post_id, self::$meta_primary, $term->term_id);
            }
        }
    }


    /**
     * Move primary tag to first position
     */
    public static function sort_tags($items) {
        global $post;

        if(empty($post->ID) || !is_array($items)) {
            return $items;
        }

        $primary = get_post_meta($post->ID, self::$meta_primary, true);

        if(empty($primary)) {
            return $items;
        }

        $sorted = [];

        foreach($items as $item) {
            if($item->term_id === (int) $primary) {
                array_unshift($sorted, $item);
            } else {
                $sorted[] = $item;
            }
        }

        return $sorted;
    }
}


/**
 * Load current module environment
 */
Knife_Primary_Tag::load_module();
