<div id="taxonomy-<?php echo self::$taxonomy; ?>" class="categorydiv">
    <?php
        printf('<input type="hidden" name="tax_input[%s][]" value="0" />',
            esc_attr(self::$taxonomy)
        );
    ?>

    <ul id="<?php echo self::$taxonomy; ?>checklist" data-wp-lists="list:<?php echo self::$taxonomy; ?>" class="categorychecklist form-no-clear">
        <?php
            wp_terms_checklist(
                $post->ID, ['taxonomy' => self::$taxonomy]
            );
        ?>
    </ul>
</div>
