<?php
/**
 * Similar posts
 *
 * Show similar posts grouped by common tags inside single template
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.10
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
    private static $cache_group = 'knife-similar-posts';


    /**
     * Default post type with similar aside
     *
     * @since   1.8
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include similar posts data
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Update links returned via tinymce
        add_filter('wp_link_query', [__CLASS__, 'update_link_query'], 12);
    }


    /**
     * Update links returned via tinymce
     *
     * @since 1.9
     */
    public static function update_link_query($results) {
        foreach($results as &$result) {
            $result['title'] = html_entity_decode($result['title']);
        }

        return $results;
    }


    /**
     * Inject similar posts data
     */
    public static function inject_object() {
        if(is_singular(self::$post_type)) {
            $post_id = get_the_ID();

            // Check if promo post
            $is_promo = get_post_meta($post_id, '_knife-promo', true);

            if(in_category('news') || $is_promo) {
                return;
            }

            $options = [
                'title' => __('Читайте также', 'knife-theme'),
                'action' => __('Similar click', 'knife-theme')
            ];

            // Get similar if not cards
            if(!has_post_format('chat', $post_id)) {
                $similar = self::get_similar($post_id);

                // Append promo
                $similar = self::append_promo($similar);

                if($similar > 0) {
                    $options['similar'] = $similar;
                }
            }

            wp_localize_script('knife-theme', 'knife_similar_posts', $options);
        }
    }


    /**
     * Get similar posts related on common tags
     *
     * Using get_the_tags function to retrieve terms with primary tag first
     */
    private static function get_similar($post_id) {
        $similar = wp_cache_get($post_id, self::$cache_group);

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
                'ignore_sticky_posts' => true,
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => ['news'],
                        'operator' => 'NOT IN'
                    ]
                ]
            ]);

            $related = [];

            foreach(wp_list_pluck($the_posts, 'ID') as $id) {
                $related[$id] = 0;

                foreach(get_the_terms($id, 'post_tag') as $tag) {
                    if(in_array($tag->term_id, $the_terms)) {
                        $related[$id] = $related[$id] + 1;
                    }
                }
            }

            // Sort by tags count
            arsort($related);

            // Get first 9 elements
            $related = array_slice($related, 0, 9, true);

            foreach($related as $id => $count) {
                $similar[] = [
                    'title' => get_the_title($id),
                    'link' => get_permalink($id),
                ];
            }

            // Update similar posts cache by post id
            wp_cache_set($post_id, $similar, self::$cache_group);
        }

        return $similar;
    }


    /**
     * Append similar promo links from query var if exists
     */
    private static function append_promo($similar) {
        $similar_promo = get_query_var('similar_promo', []);

        if(array_filter($similar_promo)) {
            $similar = array_merge($similar_promo, $similar);
        }

        // Query var is no use any more
        set_query_var('similar_promo', false);

        return $similar;
    }
}


/**
 * Load current module environment
 */
Knife_Similar_Posts::load_module();
