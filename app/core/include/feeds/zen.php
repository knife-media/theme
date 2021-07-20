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
            $query = new WP_Query([
                'post__in' => self::get_zen_query(),
                'posts_per_page' => -1,
                'orderby' => 'post__in'
            ]);
        ?>

        <?php while($query->have_posts()) : $query->the_post(); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <guid><?php the_guid(); ?></guid>
                <author><?php the_author(); ?></author>
                <?php
                    // Print publish date
                    printf(
                        '<pubDate>%s</pubDate>',
                        self::get_zen_date(get_the_ID(), get_post_time('Y-m-d H:i:s', true))
                    );

                    // Print description
                    printf(
                        '<description><![CDATA[%s]]></description>',
                        apply_filters('the_excerpt_rss', get_the_excerpt())
                    );

                    $content = self::get_filtered_content();

                    // Store images for enclosure
                    $enclosure = self::get_images($content, get_the_ID());

                    // Get zen special categories
                    $categories = self::get_zen_categories(get_the_ID());

                    // Remove unwanted tags
                    $content = self::remove_tags($content);

                    // Print yandex:full-text
                    printf(
                        '<content:encoded><![CDATA[%s]]></content:encoded>',
                        self::clean_content($content)
                    );

                    // Insert category
                    foreach($categories as $category) {
                        printf('<category>%s</category>', esc_html($category));
                    }

                    // Insert enclosure
                    foreach($enclosure as $image) {
                        printf('<enclosure url="%s" type="%s" />', esc_url($image), wp_check_filetype($image)['type']);
                    }
                ?>
            </item>
        <?php endwhile; wp_reset_postdata(); ?>
    </channel>
</rss>
