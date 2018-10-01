<div id="taxonomy-<?php echo self::$slug; ?>" class="categorydiv">
    <?php
        printf('<input type="hidden" name="tax_input[%s][]" value="0" />',
            esc_attr(self::$slug)
        );
    ?>

    <ul id="<?php echo self::$slug; ?>checklist" data-wp-lists="list:<?php echo self::$slug; ?>" class="categorychecklist form-no-clear">
        <?php
            wp_terms_checklist(
                $post->ID, ['taxonomy' => self::$slug]
            );
        ?>
    </ul>
</div>
