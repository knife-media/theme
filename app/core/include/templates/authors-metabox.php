<div id="knife-authors-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Get post authors meta
        $authors = get_post_meta($post_id, self::$post_meta);

        printf(
            '<input class="authors-input" type="text" placeholder="%s">',
            __('Поиск автора', 'knife-theme')
        );

        foreach($authors as $author) {
            $user = get_userdata($author);

            printf(
                '<p class="authors-item">%s<span class="authors-delete"></span>%s</p>',
                sprintf(
                    '<input type="hidden" name="%s[]" value="%s">',
                    esc_attr(self::$post_meta),
                    esc_attr($user->ID)
                ),
                esc_html($user->display_name)
            );
        }
    ?>
</div>