<?php
/**
 * Poster Templates
 *
 * Return generated posters using custom templates
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.9
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Poster_Templates {
    /**
     * Get poster templates
     */
    public static function get_templates() {
        $templates = [
            'generic' => __('Универсальный шаблон', 'knife-theme')
        ];

        return $templates;
    }


    /**
     * Print templates select
     */
    public static function print_select($args = []) {
        $args = wp_parse_args($args, [
            'attributes' => [],
            'selected' => 0,
            'echo' => true
        ]);

        $templates = self::get_templates();

        $options = [];
        foreach($templates as $template => $label) {
            $options[] = sprintf('<option value="%1$s"%3$s>%2$s</option>',
                esc_attr($template), esc_html($label),
                selected($args['selected'], $template, false)
            );
        }

        if(!array_key_exists($args['selected'], $templates)) {
            array_unshift($options, sprintf('<option disabled selected value="">%s</option>',
                __('Выберите шаблон генератора', 'knife-theme')
            ));
        }

        $attributes = [];
        foreach($args['attributes'] as $key => $value) {
            $attributes[] = $key . '="' . $value . '"';
        }

        // Create select using attributes and options
        $select = sprintf('<select %1$s>%2$s</select>',
            implode(' ', $attributes), implode("\n", $options)
        );

        if($args['echo'] !== true) {
            return $select;
        }

        echo $select;
    }
}
