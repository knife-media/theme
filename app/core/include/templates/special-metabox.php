<div id="taxonomy-<?php echo self::$taxonomy; ?>" class="categorydiv">
    <?php
        printf('<input type="hidden" name="tax_input[%s][]" value="0" />',
            esc_attr(self::$taxonomy)
        );

        $selected = wp_get_object_terms($post->ID, self::$taxonomy);
        $tax_name = 'tax_input[' . self::$taxonomy . ']';

        $dropdown = [
            'taxonomy' => self::$taxonomy,
            'hide_empty' => false,
            'name' => $tax_name,
            'orderby' => 'name',
            'hierarchical' => false,
            'show_option_none' => '&mdash;',
            'class' => 'widefat'
        ];

        if(isset($selected[0]->term_id)) {
            $dropdown['selected'] = $selected[0]->term_id;
        }

        wp_dropdown_categories($dropdown);
    ?>
</div>
