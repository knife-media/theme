<div id="knife-authors-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Get post authors meta
        $authors = get_post_meta($post_id, self::$post_meta);
    ?>

    <div class="authors">
        <?php
            printf(
                '<input class="authors-input" type="text" placeholder="%s">',
                __('Поиск автора', 'knife-theme')
            );
        ?>

        <span class="spinner"></span>
    </div>

    <div class="authors">
        <?php
            foreach($authors as $author) {
                $user = get_userdata($author);

                printf(
                    '<div class="authors-item">%s</div>',
                    esc_html($user->display_name)
                );
            }
        ?>
    </div>
</div>
