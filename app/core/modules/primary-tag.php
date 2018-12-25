<?php
/**
* Primary post tag
*
* Update current post tag metabox to add opportunity select primary tag
*
* @package knife-theme
* @since 1.3
* @version 1.7
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Primary_Tag {
   /**
    * Post meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta = 'primary-tag';


   /**
    * Post types primary tag availible
    *
    * @access  private
    * @var     array
    */
    private static $type = ['post', 'generator'];


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
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$type)) {
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
        $term_id = get_post_meta($post_id, self::$meta, true);

        if($term_id && $term = get_term($term_id)) {
            $options['primary'] = $term->name;
        }

        wp_localize_script('knife-primary-tagbox', 'knife_primary_tagbox', $options);
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

        // Save meta
        if(isset($_REQUEST[self::$meta])) {
            $term = get_term_by('name', $_REQUEST[self::$meta], 'post_tag');

            if($term && $term->term_id > 0) {
                update_post_meta($post_id, self::$meta, $term->term_id);
            }
        }
    }


    /**
     * Move primary tag to first position
     */
    public static function sort_tags($items) {
        global $post;

        if(!is_array($items)) {
            return $items;
        }

        if($primary = get_post_meta($post->ID, self::$meta, true)) {
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


/**
 * Load current module environment
 */
Knife_Primary_Tag::load_module();
