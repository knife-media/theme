<?php
/**
 * Yandex.Turbo feed
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
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <turbo:content>
                    <![CDATA[
                        <header>
                            <?php
                                if(has_post_thumbnail()) {
                                    printf('<figure><img src="%s"></figure>',
                                        get_the_post_thumbnail_url(get_the_ID(), 'outer')
                                    );
                                }
                            ?>

                            <h1><?php the_title_rss(); ?></h1>
                        </header>
                        <?php the_content_feed(); ?>
                    ]]>
                </turbo:content>

                <?php do_action('rss2_item'); ?>
            </item>
        <?php endwhile; ?>
    </channel>
</rss>
