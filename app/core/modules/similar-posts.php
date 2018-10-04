<?php
/**
* Similar posts
*
* Show similar posts grouped by common tags inside single template
*
* @package knife-theme
* @since 1.5
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Similar_Posts {
    /**
     * Cache group to store similar posts
     *
     * @access  private
     * @var     string
     */
    private static $cache = 'knife-similar-posts';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include similar posts data
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);
    }


    /**
     * Inject similar posts data
     */
    public static function inject_object() {
        if(is_singular('post')) {
            $similar_posts = self::get_similar(get_queried_object_id());

            if($similar_posts > 0) {
                wp_localize_script('knife-theme', 'knife_similar_posts', $similar_posts);
            }
        }
    }


    /**
     * Get similar posts related on common tags
     *
     * Using get_the_tags function to retrieve terms with primary tag first
     */
    private static function get_similar($post_id) {
        $similar = wp_cache_get($post_id, self::$cache);

        if($similar !== false) {
            return $similar;
        }

        $similar = [];

        // Get given post tags
        $the_terms = wp_list_pluck(get_the_tags($post_id), 'term_id');

        // Get posts with primary tag
        $the_posts = get_posts([
            'posts_per_page' => -1,
            'tag_id' => $the_terms[0],
            'post__not_in' => [$post_id],
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        ]);

        foreach(wp_list_pluck($the_posts, 'ID') as $id) {
            $similar[$id] = 0;

            foreach(get_the_terms($id, 'post_tag') as $tag) {
                if(in_array($tag->term_id, $the_terms)) {
                    $similar[$id] = $similar[$id] + 1;
                }
            }
        }

        // Sort by tags count
        arsort($similar);

        $i = 0;

        $data = [];
        foreach($similar as $p => $c) {
            $data[] = [
                'title' => get_the_title($p),
                'emoji' => get_term_meta($the_terms[0], '_knife-term-emoji', true),
                'link' => get_permalink($p)
            ];
            if($i++ > 5)
                break;
        }

        print_r($data);
        die;
    }
}


/**
 * Load current module environment
 */
Knife_Similar_Posts::load_module();
