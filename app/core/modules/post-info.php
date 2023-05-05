<?php
/**
 * Post info
 *
 * Return post info data
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Post_Info {
    /**
     * Post info entry point
     *
     * @since 1.8
     */
    public static function get_info( $options = array(), $output = '' ) {
        // Get club about info
        if ( in_array( 'club', $options, true ) ) {
            $output = self::get_club( $output );
        }

        // Get post meta
        $output = self::get_meta( $options, $output );

        // Get head info
        if ( in_array( 'head', $options, true ) ) {
            $output = self::get_head( $output );
        }

        // Get promo pixel image
        if ( in_array( 'pixel', $options, true ) ) {
            $output = self::get_pixel( $output );
        }

        // Get promo ORD image
        if ( in_array( 'ord', $options, true ) ) {
            $output = self::get_ord( $output );
        }

        // Get label info
        if ( in_array( 'best', $options, true ) ) {
            $output = self::get_best( $output );
        }

        return $output;
    }

    /**
     * Get entry caption
     *
     * @since 1.10
     */
    public static function get_tagline( $output = '' ) {
        $post_id = get_the_ID();

        if ( property_exists( 'Knife_Promo_Manager', 'meta_promo' ) ) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if ( get_post_meta( $post_id, $meta_promo, true ) ) {
                return self::compose_promo_tagline( $post_id, $output );
            }
        }

        // Check special projects class and return template
        if ( property_exists( 'Knife_Special_Projects', 'taxonomy' ) ) {
            $taxonomy = Knife_Special_Projects::$taxonomy;

            if ( has_term( '', $taxonomy, $post_id ) ) {
                return self::compose_special_tagline( $post_id, $output );
            }
        }

        return $output;
    }

    /**
     * Get club about meta
     *
     * @since 1.8
     */
    private static function get_club( $output = '' ) {
        if ( property_exists( 'Knife_Club_Section', 'post_type' ) ) {
            $post_id = get_the_ID();

            // Get post type info
            $post_type = get_post_type( $post_id );

            if ( $post_type === Knife_Club_Section::$post_type ) {
                $club = sprintf(
                    '<div class="meta meta--club"><a class="meta__item" href="%s">%s</a></div>',
                    esc_url( get_post_type_archive_link( $post_type ) ),
                    esc_html( get_post_type_object( $post_type )->labels->name )
                );

                $output = $output . $club;
            }
        }

        return $output;
    }

    /**
     * Get label info meta
     *
     * @since 1.8
     */
    private static function get_best( $output = '' ) {
        if ( property_exists( 'Knife_Best_Posts', 'meta_best' ) ) {
            $post_id = get_the_ID();

            // Set meta best
            $meta_best = Knife_Best_Posts::$meta_best;

            // Check if best post
            if ( get_post_meta( $post_id, $meta_best, true ) ) {
                $best = sprintf(
                    '<div class="meta meta--best"><a class="meta__item" href="%s">%s</a></div>',
                    trailingslashit( home_url( Knife_Best_Posts::$query_var ) ),
                    _x( '⭐', 'best post emoji', 'knife-theme' )
                );

                $output = $output . $best;
            }
        }

        return $output;
    }

    /**
     * Get head info meta
     *
     * @since 1.8
     */
    private static function get_head( $output = '' ) {
        if ( property_exists( 'Knife_Promo_Manager', 'meta_promo' ) ) {
            $post_id = get_the_ID();

            // Set meta promo
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            // Check if promo first
            if ( get_post_meta( $post_id, $meta_promo, true ) ) {
                $promo = sprintf(
                    '<a class="head" href="%s">%s</a>',
                    trailingslashit( home_url( Knife_Promo_Manager::$query_var ) ),
                    esc_html__( 'Партнерский материал', 'knife-theme' )
                );

                return $output . $promo;
            }
        }

        if ( property_exists( 'Knife_Special_Projects', 'taxonomy' ) ) {
            $terms = get_the_terms( get_the_ID(), Knife_Special_Projects::$taxonomy );

            // Check if post has terms
            if ( isset( $terms[0] ) && property_exists( 'Knife_Special_Projects', 'term_meta' ) ) {
                $options = get_term_meta( $terms[0]->term_id, Knife_Special_Projects::$term_meta, true );

                if ( empty( $options['hidden'] ) ) {
                    $special = sprintf(
                        '<a class="head" href="%2$s">%1$s</a>',
                        esc_html( $terms[0]->name ),
                        esc_url( get_term_link( $terms[0]->term_id ) )
                    );

                    return $output . $special;
                }
            }
        }

        $tags = get_the_tags();

        // Check for tags finally
        if ( isset( $tags[0] ) ) {
            $output = $output . sprintf(
                '<a class="head" href="%2$s">%1$s</a>',
                esc_html( $tags[0]->name ),
                esc_url( get_tag_link( $tags[0]->term_id ) )
            );
        }

        return $output;
    }

    /**
     * Show ORD pixel
     *
     * @since 1.15
     */
    private static function get_ord( $output = '' ) {
        if ( property_exists( 'Knife_Promo_Manager', 'meta_ord' ) ) {
            $post_id = get_the_ID();

            // Set meta ord
            $meta_ord = get_post_meta( $post_id, Knife_Promo_Manager::$meta_ord, true );

            // Check if promo ord first
            if ( $meta_ord ) {
                $ord = sprintf(
                    '<img src="%s" alt="">',
                    esc_url( $meta_ord )
                );

                $output = $output . $ord;
            }
        }

        return $output;
    }

    /**
     * Show promo pixel
     *
     * @since 1.15
     */
    private static function get_pixel( $output = '' ) {
        if ( property_exists( 'Knife_Promo_Manager', 'meta_pixel' ) ) {
            $post_id = get_the_ID();

            // Set meta pixel
            $meta_pixel = get_post_meta( $post_id, Knife_Promo_Manager::$meta_pixel, true );

            // Check if promo pixel first
            if ( $meta_pixel ) {
                $pixel = sprintf(
                    '<img src="%s" alt="">',
                    esc_url( $meta_pixel )
                );

                $output = $output . $pixel;
            }
        }

        return $output;
    }



    /**
     * Common method to output posts info meta
     *
     * @since 1.8
     */
    private static function get_meta( $options = array(), $output = '' ) {
        // Get allowed meta options
        $options = array_intersect(
            $options,
            array(
                'author',
                'date',
                'tag',
                'tags',
                'time',
            )
        );

        $meta = array();

        foreach ( $options as $option ) {
            $method = 'meta_' . $option;

            if ( method_exists( __CLASS__, $method ) ) {
                $meta[] = self::$method();
            }
        }

        if ( array_filter( $meta ) ) {
            $output = $output . sprintf(
                '<div class="meta">%s</div>',
                implode( '', $meta )
            );
        }

        return $output;
    }

    /**
     * Get post author info
     */
    private static function meta_author( $output = '' ) {
        $post_id = get_the_ID();

        if ( property_exists( 'Knife_Promo_Manager', 'meta_promo' ) ) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            // Check if promo first
            if ( get_post_meta( $post_id, $meta_promo, true ) ) {
                $promo = sprintf(
                    '<a class="meta__item" href="%s" rel="author">%s</a>',
                    trailingslashit( home_url( Knife_Promo_Manager::$query_var ) ),
                    esc_html__( 'Редакция спецпроектов', 'knife-theme' )
                );

                return $output . $promo;
            }
        }

        if ( method_exists( 'Knife_Authors_Manager', 'get_post_authors' ) ) {
            $authors = (array) Knife_Authors_Manager::get_post_authors( $post_id );

            foreach ( $authors as $author ) {
                $user = get_userdata( $author );

                if ( $user === false ) {
                    continue;
                }

                $output = $output . sprintf(
                    '<a class="meta__item" href="%s" rel="author">%s</a>',
                    esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
                    esc_html( $user->display_name )
                );
            }
        }

        return $output;
    }

    /**
     * Get post date info
     */
    private static function meta_date( $output = '' ) {
        if ( property_exists( 'Knife_Best_Posts', 'query_var' ) ) {
            // Ok, let's die if it in /best/ archive page
            if ( get_query_var( Knife_Best_Posts::$query_var ) ) {
                return $output;
            }
        }

        $output = sprintf(
            '<span class="meta__item"><time datetime="%1$s">%2$s</time></span>',
            get_the_time( 'c' ),
            get_the_date( 'Y' ) === gmdate( 'Y' ) ? get_the_time( 'j F' ) : get_the_time( 'j F Y' )
        );

        return $output;
    }

    /**
     * Get post time info
     */
    private static function meta_time() {
        $output = sprintf(
            '<span class="meta__item">%s</span>',
            get_the_time( 'H:i' )
        );

        return $output;
    }

    /**
     * Get primary post tag info
     */
    private static function meta_tag( $output = '' ) {
        $tags = get_the_tags();

        if ( isset( $tags[0] ) ) {
            $output = sprintf(
                '<a class="meta__item" href="%2$s">%1$s</a>',
                esc_html( $tags[0]->name ),
                esc_url( get_tag_link( $tags[0]->term_id ) )
            );
        }

        return $output;
    }

    /**
     * Get post tags info
     */
    private static function meta_tags( $output = '' ) {
        $tags = get_the_tags();

        if ( is_array( $tags ) ) {
            foreach ( $tags as $i => $tag ) {
                if ( $i <= 3 ) {
                    $output = $output . sprintf(
                        '<a class="meta__item" href="%2$s">%1$s</a>',
                        esc_html( $tag->name ),
                        esc_url( get_tag_link( $tag->term_id ) )
                    );
                }
            }
        }

        return $output;
    }

    /**
     * Compose promo tagline
     *
     * @since 1.10
     */
    private static function compose_promo_tagline( $post_id, $output = '', $partner = '' ) {
        $classes = 'tagline';

        // Get promo options
        $options = (array) get_post_meta( $post_id, Knife_Promo_Manager::$meta_options, true );

        // Set default promo panel color
        if ( empty( $options['color'] ) ) {
            $options['color'] = '#fff';
        }

        // Add logo if exists
        if ( ! empty( $options['logo'] ) ) {
            $partner = $partner . sprintf(
                '<img class="tagline__partner-logo" src="%s" alt="">',
                esc_url( $options['logo'] )
            );

            $classes = $classes . ' tagline--logo';
        }

        // Add title if exists
        if ( ! empty( $options['title'] ) ) {
            $partner = $partner . sprintf(
                '<span class="tagline__partner-title">%s</span>',
                sanitize_text_field( $options['title'] )
            );

            $classes = $classes . ' tagline--title';
        }

        // Add required title
        if ( empty( $options['text'] ) ) {
            $options['text'] = esc_html__( 'Партнерский материал', 'knife-theme' );
        }

        $promo = sprintf(
            '<span class="tagline__text">%s</span>',
            sanitize_text_field( $options['text'] )
        );

        // Wrap logo and title
        if ( ! empty( $partner ) ) {
            $promo = $promo . sprintf(
                '<div class="tagline__partner">%s</div>',
                $partner
            );
        }

        $styles = array(
            'background-color:' . $options['color'],
            'color:' . self::get_text_color( $options['color'] ),
        );

        $styles = implode( '; ', $styles );

        // Return if link not defined
        if ( empty( $options['link'] ) ) {
            $output = sprintf(
                '<div class="%2$s" style="%3$s">%1$s</div>',
                $promo,
                $classes,
                esc_attr( $styles )
            );

            return $output;
        }

        $output = sprintf(
            '<a href="%2$s" class="%3$s" target="_blank" rel="noopener" style="%4$s">%1$s</a>',
            $promo,
            esc_url( $options['link'] ),
            $classes,
            esc_attr( $styles )
        );

        return $output;
    }

    /**
     * Compose special tagline
     *
     * @since 1.10
     */
    private static function compose_special_tagline( $post_id, $output = '' ) {
        $taxonomy = Knife_Special_Projects::$taxonomy;

        // Loop over all tax terms
        foreach ( get_the_terms( $post_id, $taxonomy ) as $term ) {
            $ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );

            // Get parent if exists
            if ( ! empty( $ancestors ) ) {
                $term = get_term( $ancestors[0], $taxonomy );
            }

            // Don't show hidden special
            if ( property_exists( 'Knife_Special_Projects', 'term_meta' ) ) {
                $options = get_term_meta( $term->term_id, Knife_Special_Projects::$term_meta, true );

                if ( ! empty( $options['hidden'] ) ) {
                    break;
                }
            }

            if ( empty( $options['single'] ) ) {
                $output = sprintf(
                    '<a class="tagline" href="%2$s"><span class="tagline__name">%1$s</span></a>',
                    esc_html( $term->name ),
                    esc_url( get_term_link( $term->term_id ) )
                );

                break;
            }

            $styles = array(
                'background:' . $options['single'],
                'color:' . self::get_text_color( $options['single'] ),
            );

            $output = sprintf(
                '<a class="tagline" href="%2$s" style="%3$s"><span class="tagline__name">%1$s</span></a>',
                esc_html( $term->name ),
                esc_url( get_term_link( $term->term_id ) ),
                esc_attr( implode( '; ', $styles ) )
            );

            break;
        }

        return $output;
    }

    /**
     * Get text color using relative luminance
     *
     * @link https://en.wikipedia.org/wiki/Relative_luminance
     * @since 1.10
     */
    private static function get_text_color( $color ) {
        $color = trim( $color, '#' );

        if ( strlen( $color ) === 3 ) {
            $r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
            $g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
            $b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
        } elseif ( strlen( $color ) === 6 ) {
            $r = hexdec( substr( $color, 0, 2 ) );
            $g = hexdec( substr( $color, 2, 2 ) );
            $b = hexdec( substr( $color, 4, 2 ) );
        }

        // Get relative luminance
        $y = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return $y > 128 ? '#000' : '#fff';
    }
}
