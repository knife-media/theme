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
            'generic' => __('Универсальный шаблон', 'knife-theme'),
            'snippet' => __('Сниппет для соцсетей', 'knife-theme')
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

        $options = [];

        // Get availible templates
        $templates = self::get_templates();

        foreach($templates as $template => $label) {
            $options[] = sprintf('<option value="%1$s"%3$s>%2$s</option>',
                esc_attr($template), esc_html($label),
                selected($args['selected'], $template, false)
            );
        }

        // Add selected attribute if need
        if(!array_key_exists($args['selected'], $templates)) {
            array_unshift($options, sprintf('<option disabled selected value="">%s</option>',
                __('Выберите шаблон генератора', 'knife-theme')
            ));
        }

        $attributes = [];

        // Generate attributes
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


    /**
     * Create poster using options
     *
     * @since 1.9
     */
    public static function create_poster($options, $folder) {
        $templates = self::get_templates();

        // Check if template defined
        if(!array_key_exists($options['template'], $templates)) {
            return new WP_Error('prepare', __('Шаблон генерации не задан', 'knife-theme'));
        }

        // Check if poster template file exists
        if(!file_exists(get_template_directory() . $options['include'])) {
            return new WP_Error('prepare', __('Не удалось найти файл шаблона', 'knife-theme'));
        }

        $image = get_attached_file($options['attachment']);

        // Check image url by attachment id
        if($image === false) {
            return new WP_Error('prepare', __('Не удалось найти вложение', 'knife-theme'));
        }

        // Append required PHPImage class
        if(!class_exists('PHPImage')) {
            require(get_template_directory() . '/core/classes/phpimage.class.php');
        }

        $upload = wp_upload_dir();

        // Define upload dir and url
        $basedir = $upload['basedir'] . $folder;
        $baseurl = $upload['baseurl'] . $folder;

        // Check upload folder
        if(!wp_is_writable($basedir) && !wp_mkdir_p($basedir)) {
            return new WP_Error('prepare', __('Проверьте права на запись', 'knife-theme'));
        }

        // Check post id existance
        if(absint($options['post_id']) === 0) {
            return new WP_Error('prepare', __('Пустое значение post ID', 'knife-theme'));
        }

        $textbox = [];

        // Fill textbox array
        if(isset($options['textbox'])) {
            $textbox = array_map('wp_specialchars_decode', $options['textbox']);
        }

        // Create poster file name
        $filename = $options['post_id'] . uniqid('-') . '.jpg';

        try {
            $include = '/core/include/posters/' . $options['template'] . '.php';

            // Include posters template
            include(get_template_directory() . $include);

        } catch(Exception $error) {
            return new WP_Error('generate', __('Ошибка генерации: ', 'knife-theme') . $error->getMessage());
        }

        return $baseurl . $filename;
    }
}
