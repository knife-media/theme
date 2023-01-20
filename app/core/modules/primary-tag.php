<?php
/**
 * Primary post tag
 *
 * Update current post tag metabox to add opportunity select primary tag
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.16
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
    public static $post_type = ['post','quiz'];


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
        add_filter('get_the_terms', [__CLASS__, 'sort_tags'], 10, 3);
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
            'meta' => self::$meta_primary,
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
        if(isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

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
    public static function sort_tags($terms, $post_id, $taxonomy) {
        if(!is_array($terms) || $taxonomy !== 'post_tag') {
            return $terms;
        }

        $primary = get_post_meta($post_id, self::$meta_primary, true);

        if(empty($primary)) {
            return $terms;
        }

        $sorted = [];

        foreach($terms as $term) {
            if($term->term_id === (int) $primary) {
                array_unshift($sorted, $term);
            } else {
                $sorted[] = $term;
            }
        }

        return $sorted;
    }
}


/**
 * Load current module environment
 */
Knife_Primary_Tag::load_module();
