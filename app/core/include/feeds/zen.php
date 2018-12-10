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

        <?php foreach($posts as $post) : setup_postdata($post); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <guid><?php the_guid(); ?></guid>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $post->zen_date, false); ?></pubDate>
                <author><?php the_author(); ?></author>
                <description>
                    <![CDATA[<?php the_excerpt_rss(); ?>]]>
                </description>
                <content:encoded>
                    <![CDATA[<?php the_content_feed(); ?>]]>
                </content:encoded>

                <?php do_action('rss2_item'); ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
