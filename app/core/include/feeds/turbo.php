<?php
/**
 * Yandex.Turbo feed
 *
 * @since 1.7
 * @version 1.17
 */

header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>';
?>

<rss version="2.0"
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru">
    <channel>
        <title><?php bloginfo_rss( 'name' ); ?></title>
        <link><?php bloginfo_rss( 'url' ); ?></link>
        <description><?php bloginfo_rss( 'description' ); ?></description>
        <language><?php bloginfo_rss( 'language' ); ?></language>
        <?php do_action( 'rss2_head' ); ?>

        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <item turbo="true">
                <link><?php the_permalink_rss(); ?></link>
                <title><?php the_title_rss(); ?></title>
                <author><?php the_author(); ?></author>
                <?php
                    // Print publish date
                    printf(
                        '<pubDate>%s</pubDate>',
                        esc_html( mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ) )
                    );

                    // Custom header for turbo content
                    $header = sprintf(
                        '<header><h1>%s</h1></header>',
                        get_the_title_rss()
                    );

                    $content = self::clean_content( get_the_content_feed() );

                    printf(
                        '<turbo:content><![CDATA[%s]]></turbo:content>',
                        $header . self::prepare_turbo_content( $content ) // phpcs:ignore
                    );
                ?>
            </item>
        <?php endwhile; ?>
    </channel>
</rss>
