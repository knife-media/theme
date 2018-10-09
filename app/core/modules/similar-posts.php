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
        if(is_singular('post') && !in_category('news')) {
            $similar = self::get_similar(get_queried_object_id());

            if($similar > 0) {
                wp_localize_script('knife-theme', 'knife_similar_posts', $similar);
            }
        }
    }


    /**
     * Get similar posts related on common tags
     *
     * Using get_the_tags function to retrieve terms with primary tag first
     */
    private static function get_similar($post_id) {
        $similar = false;// wp_cache_get($post_id, self::$cache);

        if($similar !== false) {
            return $similar;
        }

        $similar = [];

        // Get given post tags
        if($post_terms = get_the_tags($post_id)) {
            $the_terms = wp_list_pluck($post_terms, 'term_id');

            // Get posts with primary tag
            $the_posts = get_posts([
                'posts_per_page' => -1,
                'tag_id' => $the_terms[0],
                'post__not_in' => [$post_id],
                'post_status' => 'publish',
                'ignore_sticky_posts' => true
            ]);

            $related = [];

            foreach(wp_list_pluck($the_posts, 'ID') as $id) {
                $related[$id] = 0;

                foreach(get_the_terms($id, 'post_tag') as $tag) {
                    if(in_array($tag->term_id, $the_terms)) {
                        $reated[$id] = $related[$id] + 1;
                    }
                }
            }

            // Sort by tags count
            arsort($related);

            // Get first 5 elements
            $related = array_slice($related, 0, 5, true);

            foreach($related as $id => $count) {
                $relate_items = [
                    'title' => get_the_title($id),
                    'link' => get_permalink($id)
                ];

                $relate_terms = get_the_tags($id);

                if(is_array($relate_terms)) {
                    $relate_term = end($relate_terms);

                    if($relate_emoji = get_term_meta($relate_term->term_id, '_knife-term-emoji', true)) {
                        $relate_items['emoji'] = $relate_emoji;
                    }
                }

                $similar[] = $relate_items;
            }
        }

        wp_cache_set($post_id, $similar, self::$cache);

        return $similar;
    }
}


/**
 * Load current module environment
 */
Knife_Similar_Posts::load_module();
