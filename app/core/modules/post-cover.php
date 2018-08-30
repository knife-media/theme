<?php
/**
* Cover post meta
*
* Indicates how to show post in lists
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Post_Cover {
   /**
    * Post meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta = '_knife-cover';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Add checkbox above thumnail widget
        add_filter('admin_post_thumbnail_html', [__CLASS__, 'print_checkbox'], 10, 3);
    }


    /**
     * Prints checkbox in post thumbnail editor metabox
     */
    public static function print_checkbox($content, $post_id, $thumbnail_id = '')  {
        if(get_post_type($post_id) !== 'post' || empty($thumbnail_id)) {
            return $content;
        }

        $cover = get_post_meta($post_id, self::$meta, true);

        $checkbox = sprintf(
            '<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
            esc_attr(self::$meta),
            __('Использовать подложку в списках', 'knife-theme'),
            checked($cover, 1, false)
        );

        return $checkbox . $content;
    }


    /**
     * Save cover meta
     */
    public static function save_meta($post_id) {
        if(get_post_type($post_id) !== 'post') {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta])) {
            return delete_post_meta($post_id, self::$meta);
        }

        return update_post_meta($post_id, self::$meta, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Post_Cover::load_module();
