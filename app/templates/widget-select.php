<?php
/**
 * Televisor widget template
 *
 * @package knife-theme
 * @since 1.6
 */
?>

<div class="select">
    <?php
        printf('<a class="select__head head" href="%1$s">%2$s</a>',
            esc_url(get_post_type_archive_link('select')),
            esc_html($instance['title'])
        );

        $this->show_links($query);
    ?>
</div>
