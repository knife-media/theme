<?php
/**
 * Yandex.Zen feed
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
?>

<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php bloginfo_rss('name'); ?></title>
        <link><?php bloginfo_rss('url'); ?></link>
        <description><?php bloginfo_rss('description'); ?></description>
        <language><?php bloginfo_rss('language'); ?></language>
        <?php do_action('rss2_head'); ?>

        <?php
            global $post, $wpdb;

            $paged = intval(get_query_var('paged', 1));

            if($paged < 1) {
                $paged = 1;
            }

            $limit = 50;
            $offset = $limit * ($paged - 1);

            $query = "SELECT SQL_CALC_FOUND_ROWS p.*, IFNULL(m2.meta_value, p.post_date_gmt) as zen_date
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} m1 ON (p.ID = m1.post_id AND m1.meta_key = '" . self::$zen_exclude . "')
                LEFT JOIN {$wpdb->postmeta} m2 ON (p.ID = m2.post_id AND m2.meta_key = '" . self::$zen_publish . "')
                WHERE p.post_type = 'post' AND p.post_status = 'publish' AND m1.post_id IS NULL
                GROUP BY p.ID ORDER BY zen_date DESC LIMIT {$offset}, {$limit}";

            $posts = $wpdb->get_results($query, OBJECT);
        ?>

        <?php foreach($posts as $post) : setup_postdata($post); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <guid><?php the_guid(); ?></guid>
                <author><?php the_author(); ?></author>
                <?php
                    // Print publish date
                    printf(
                        '<pubDate>%s</pubDate>',
                        mysql2date('D, d M Y H:i:s +0000', $post->zen_date, false)
                    );

                    // Print description
                    printf(
                        '<description><![CDATA[%s]]></description>',
                        apply_filters('the_excerpt_rss', get_the_excerpt())
                    );

                    $content = get_the_content_feed();

                    // Remove break lines and spaces
                    $content = self::clean_content($content);


                    // Store images for enclosure
                    $enclosure = self::get_images($content, get_the_ID());

                    // Remove unwanted tags
                    $content = self::remove_tags($content);

                    // Print yandex:full-text
                    printf(
                        '<content:encoded><![CDATA[%s]]></content:encoded>',
                        $content
                    );

                    // Insert category
                    foreach(get_the_category() as $category) {
                        printf('<category>%s</category>', esc_html($category->cat_name));
                    }

                    // Insert enclosure
                    foreach($enclosure as $image) {
                        printf('<enclosure url="%s" type="%s" />', esc_url($image), wp_check_filetype($image)['type']);
                    }
                ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
