<?php
/**
 * Yandex.News feed
 *
 * @since 1.7
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
?>

<rss version="2.0"
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru">
    <channel>
        <title><?php bloginfo_rss('name'); ?></title>
        <link><?php bloginfo_rss('url'); ?></link>
        <description><?php bloginfo_rss('description'); ?></description>
        <language><?php bloginfo_rss('language'); ?></language>
        <?php do_action('rss2_head'); ?>

        <?php while(have_posts()) : the_post(); ?>
            <item turbo="true">
                <link><?php the_permalink_rss(); ?></link>
                <title><?php the_title_rss(); ?></title>
                <author><?php the_author(); ?></author>
                <?php
                    // Print publish date
                    printf(
                        '<pubDate>%s</pubDate>',
                        mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false)
                    );

                    // Print description
                    printf(
                        '<description><![CDATA[%s]]></description>',
                        apply_filters('the_excerpt_rss', get_the_excerpt())
                    );

                    $turbo = get_the_content_feed();

                    // Custom header for turbo content
                    if(has_post_thumbnail()) {
                        $header = sprintf(
                            '<header><figure><img src="%s"></figure><h1>%s</h1></header>',
                            get_the_post_thumbnail_url(get_the_ID(), 'outer'),
                            get_the_title_rss()
                        );
                    } else {
                        $header = sprintf(
                            '<header><h1>%s</h1></header>',
                            get_the_title_rss()
                        );
                    }

                    $content = self::get_filtered_content();

                    // Store images for enclosure
                    $enclosure = self::get_images($content, get_the_ID());

                    // Remove unwanted tags
                    $content = self::remove_tags($content);


                    // Print turbo:content
                    printf(
                        '<turbo:content><![CDATA[%s]]></turbo:content>',
                        $header . self::clean_content($turbo)
                    );

                    // Print yandex:full-text
                    printf(
                        '<yandex:full-text><![CDATA[%s]]></yandex:full-text>',
                        self::clean_content($content)
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
        <?php endwhile; ?>
    </channel>
</rss>
