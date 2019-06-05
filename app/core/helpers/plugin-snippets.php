<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */


/**
 * Add custom meta class to couathors posts link
 *
 * @link https://github.com/Automattic/Co-Authors-Plus/blob/master/template-tags.php#L272
 */
add_filter('coauthors_posts_link', function($args) {
    $args['class'] = 'meta__item';

    return $args;
});


/**
 * Fix co-authors author archive bug
 *
 * @link https://github.com/Automattic/Co-Authors-Plus/issues/573
 * @link https://github.com/Automattic/Co-Authors-Plus/issues/595
 */
add_action('init', function() {
    global $coauthors_plus;

    remove_filter('posts_where', [$coauthors_plus, 'posts_where_filter'], 10, 2);
});

add_filter('posts_where', function($where, $query) {
    global $coauthors_plus, $wpdb;

    if ( $query->is_author() ) {

        if ( ! empty( $query->query_vars['post_type'] ) && ! is_object_in_taxonomy( $query->query_vars['post_type'], $coauthors_plus->coauthor_taxonomy ) ) {
            return $where;
        }

        if ( $query->get( 'author_name' ) ) {
            $author_name = sanitize_title( $query->get( 'author_name' ) );
        } else {
            $author_data = get_userdata( $query->get( $coauthors_plus->coauthor_taxonomy ) );
            if ( is_object( $author_data ) ) {
                $author_name = $author_data->user_nicename;
            } else {
                return $where;
            }
        }

        $terms = array();
        $coauthor = $coauthors_plus->get_coauthor_by( 'user_nicename', $author_name );
        if ( $author_term = $coauthors_plus->get_author_term( $coauthor ) ) {
            $terms[] = $author_term;
        }
        // If this co-author has a linked account, we also need to get posts with those terms
        if ( ! empty( $coauthor->linked_account ) ) {
            $linked_account = get_user_by( 'login', $coauthor->linked_account );
            if ( $guest_author_term = $coauthors_plus->get_author_term( $linked_account ) ) {
                $terms[] = $guest_author_term;
            }
        }

        // Whether or not to include the original 'post_author' value in the query
        // Don't include it if we're forcing guest authors, or it's obvious our query is for a guest author's posts
        if ( $coauthors_plus->force_guest_authors || stripos( $where, '.post_author = 0)' ) ) {
            $maybe_both = false;
        } else {
            $maybe_both = apply_filters( 'coauthors_plus_should_query_post_author', true );
        }

        $maybe_both_query = $maybe_both ? '$1 OR' : '';

        if ( ! empty( $terms ) ) {
            $terms_implode = '';
            $coauthors_plus->having_terms = '';
            foreach ( $terms as $term ) {
                $terms_implode .= '(' . $wpdb->term_taxonomy . '.taxonomy = \''. $coauthors_plus->coauthor_taxonomy.'\' AND '. $wpdb->term_taxonomy .'.term_id = \''. $term->term_id .'\') OR ';
                $coauthors_plus->having_terms .= ' ' . $wpdb->term_taxonomy .'.term_id = \''. $term->term_id .'\' OR ';
            }
            $terms_implode = rtrim( $terms_implode, ' OR' );

            $id = is_author() ? get_queried_object_id() : '\d+';

            // When WordPress generates query as 'post_author IN (id)'.
            if ( false !== strpos( $where, "{$wpdb->posts}.post_author IN " ) ) {

                $maybe_both_query = $maybe_both ? '$0 OR' : '';

                $where = preg_replace( '/\s\b(?:' . $wpdb->posts . '\.)?post_author\s*IN\s*(.*' . $id . '.)/', ' (' . $maybe_both_query . ' ' . $terms_implode . ')', $where, -1 ); #' . $wpdb->postmeta . '.meta_id IS NOT NULL AND

            } else {
                $where = preg_replace( '/(\b(?:' . $wpdb->posts . '\.)?post_author\s*=\s*(' . $id . '))/', '(' . $maybe_both_query . ' ' . $terms_implode . ')', $where, -1 ); #' . $wpdb->postmeta . '.meta_id IS NOT NULL AND
            }

            // the block targets the private posts clause (if it exists)
            if (
                is_user_logged_in() &&
                is_author() &&
                get_queried_object_id() != get_current_user_id()
            ) {
                $current_coauthor      = $coauthors_plus->get_coauthor_by( 'user_nicename', wp_get_current_user()->user_nicename );
                $current_coauthor_term = $coauthors_plus->get_author_term( $current_coauthor );

                $current_user_query  = $wpdb->term_taxonomy . '.taxonomy = \''. $coauthors_plus->coauthor_taxonomy.'\' AND '. $wpdb->term_taxonomy .'.term_id = \''. $current_coauthor_term->term_id .'\'';
                $coauthors_plus->having_terms .= ' ' . $wpdb->term_taxonomy .'.term_id = \''. $current_coauthor_term->term_id .'\' OR ';

                $currentUser = get_current_user_id();
                    if (isset($_GET['author_name']) && $_GET['author_name']) {
                        $user = get_user_by('login', $_GET['author_name']);

                        if ($user && $user->ID) {
                            $currentUser = $user->ID;
                        }
                    } else if (is_author()) {
                        $currentUser = 'user_' . get_the_author_meta('ID');
                    }

                  $where = preg_replace( '/(\b(?:' . $wpdb->posts . '\.)?post_author\s*=\s*(' . $currentUser . '))/', $current_user_query, $where, -1 );
            }

            $coauthors_plus->having_terms = rtrim( $coauthors_plus->having_terms, ' OR' );

        }
    }
    return $where;
}, 10, 2);


/**
 * Add custom text and font for social image generator
 *
 * @link https://github.com/antonlukin/social-image/blob/master/src/classes/Generation.php
 */
add_filter('social_image_texts', function($texts, $params) {
    $fonts = get_template_directory() . '/assets/fonts/';

    $texts = [
        [
            "text" => 'KNIFE.MEDIA',
            "posx" => 65,
            "posy" => 60,
            "file" => $fonts . 'formular/formular-black.ttf',
            "size" => 22,
            "color" => '#ffe634'
        ],

        [
            "text" => wordwrap($params['text'], 1024 / 20),
            "posx" => 65,
            "posy" => 150,
            "file" => $fonts . 'formular/formular-medium.ttf',
            "size" => 46,
            "color" => '#ffe634'
        ]
    ];

    return $texts;
}, 10, 2);
