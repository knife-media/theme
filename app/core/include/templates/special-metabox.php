<div id="taxonomy-<?php echo self::$slug; ?>" class="categorydiv">
    <?php
        printf('<input type="hidden" name="tax_input[%s][]" value="0" />',
            esc_attr(self::$slug)
        );

        $selected = wp_get_object_terms($post->ID, self::$slug);
        $tax_name = 'tax_input[' . self::$slug . ']';

        $dropdown = [
            'taxonomy' => self::$slug,
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
