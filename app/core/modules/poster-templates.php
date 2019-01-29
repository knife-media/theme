<?php
/**
* Poster Templates
*
* Return generated posters using custom templates
*
* @package knife-theme
* @since 1.7
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
            'single_head' => __('Однострочный заголовок и описание', 'knife-theme'),
            'strong_head' => __('Многострочный заголовок и описание', 'knife-theme'),
            'large_title' => __('Только крупный заголовок', 'knife-theme')
        ];

        return $templates;
    }


    /**
     * Get templates select
     */
    public static function get_select($args = []) {
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


    /**
     * Create posters
     */
    public static function create_posters($options, $folder, $posters = []) {
        $templates = self::get_templates();

        // Check if generation template is defined
        if(!array_key_exists($options['template'], $templates)) {
            return new WP_Error('template', __('Шаблон генерации не задан', 'knife-theme'));
        }

        // Find generator method by template
        $method = substr_replace($options['template'], '_', 0, 0);
        if(!method_exists(__CLASS__, $method)) {
            return new WP_Error('method', __('Не найден шаблон генератора', 'knife-theme'));
        }

        // Get image url by attachment id
        $image = get_attached_file($options['attachment']);
        if($image === false) {
            return new WP_Error('attachment', __('Не удалось найти вложение', 'knife-theme'));
        }

        // Append required PHPImage class
        if(!class_exists('PHPImage')) {
            require(get_template_directory() . '/core/classes/phpimage.class.php');
        }

        // Check upload folder
        $upload = wp_upload_dir();
        if(!wp_is_writable($upload['basedir']) && !mkdir($upload['basedir'])) {
            return new WP_Error('upload', __('Проверьте права на запись', 'knife-theme'));
        }

        $options = array_merge($options, [
            'baseurl' => $upload['baseurl'] . $folder,
            'basedir' => $upload['basedir'] . $folder
        ]);

        // Check post id existance
        if(absint($options['post_id']) === 0) {
            return new WP_Error('post_id', __('Пустое значение post ID', 'knife-theme'));
        }

        // Intersect result points with blank values
        $blanks = ['from' => 0, 'to' => 0];

        $points = wp_parse_args(
            array_intersect_key($options, $blanks), $blanks
        );

        $points = array_map('intval', $points);

        // Loop through result points
        for($i = $points['from']; $points['to'] >= $i; $i++) {
            try {
                if($i === $points['from'] || strlen($options['achievment']) > 0) {
                    $filename = $options['post_id'] . uniqid('-') . '.jpg';
                    $posters[$i] = self::$method($i, $image, $options, $filename);

                    continue;
                }

                $posters[$i] = $posters[$i - 1];

            } catch(Exception $error) {
                return new WP_Error('generate', __('Ошибка генерации: ' . $error->getMessage(), 'knife-theme'));
            }
        }

        return (object) $posters;
    }


    /**
     * Generate strong head poster
     */
    private static function _strong_head($i, $image, $options, $filename) {
        $poster = new PHPImage();
        $poster->setDimensionsFromImage($image)->draw($image);
        $poster->resize(1200, 630, true);

        // Change brightness and contrast
        $filter = $poster->getResource();
        imagefilter($filter, IMG_FILTER_CONTRAST, 35);
        imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
        $poster->setResource($filter);

        $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

        // Draw site name
        $poster->text('knife.media', [
            'x' => 48, 'y' => 40, 'fontSize' => 16
        ]);


        // Draw description
        if(!empty($options['description'])) {
            $poster->textBox($options['description'], [
                'x' => 48, 'y' => 445, 'width' => 950, 'height' => 120, 'fontSize' => 24
            ]);
        }


        $titles = [];

        if(!empty($options['title'])) {
            $titles[] = wp_specialchars_decode($options['title']);
        }

        if(!empty($options['tagline'])) {
            $titles[] = wp_specialchars_decode($options['tagline']);
        }

        // Draw quiz title
        if(count($titles) > 0) {
            $poster->rectangle(50, 230, 950, 1, [255, 255, 255]);

            $poster->textBox(implode(' ', $titles), [
                'x' => 48, 'y' => 160, 'width' => 950, 'height' => 200, 'fontSize' => 24
            ]);
        }


        $heading = [];

        if(strlen($options['achievment']) > 0) {
            $heading[] = str_replace('%', $i, $options['achievment']);
        }

        if(!empty($options['heading'])) {
            $heading[] = wp_specialchars_decode($options['heading']);
        }

        // Draw heading
        if(count($heading) > 0) {
            $poster->setLineHeight(1.125);
            $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

            $poster->textBox(implode(' ', $heading), [
                'x' => 48, 'y' => 280, 'width' => 950, 'height' => 160, 'fontSize' => 52
            ]);
        }

        $poster->snapshot($options['basedir'] . $filename);

        return $options['baseurl'] . $filename;
    }


    /**
     * Generate single head poster
     */
    private static function _single_head($i, $image, $options, $filename) {
        $poster = new PHPImage();
        $poster->setDimensionsFromImage($image)->draw($image);
        $poster->resize(1200, 630, true);

        // Change brightness and contrast
        $filter = $poster->getResource();
        imagefilter($filter, IMG_FILTER_CONTRAST, 35);
        imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
        $poster->setResource($filter);

        $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

        // Draw site name
        $poster->text('knife.media', [
            'x' => 48, 'y' => 40, 'fontSize' => 16
        ]);


        // Draw description
        if(!empty($options['description'])) {
            $poster->textBox($options['description'], [
                'x' => 48, 'y' => 380, 'width' => 950, 'height' => 120, 'fontSize' => 24
            ]);
        }


        $titles = [];

        if(!empty($options['title'])) {
            $titles[] = wp_specialchars_decode($options['title']);
        }

        if(!empty($options['tagline'])) {
            $titles[] = wp_specialchars_decode($options['tagline']);
        }

        // Draw quiz title
        if(count($titles) > 0) {
            $poster->rectangle(50, 230, 950, 1, [255, 255, 255]);

            $poster->textBox(implode(' ', $titles), [
                'x' => 48, 'y' => 160, 'width' => 950, 'height' => 200, 'fontSize' => 24
            ]);
        }


        $heading = [];

        if(strlen($options['achievment']) > 0) {
            $heading[] = str_replace('%', $i, $options['achievment']);
        }

        if(!empty($options['heading'])) {
            $heading[] = wp_specialchars_decode($options['heading']);
        }

        // Draw heading
        if(count($heading) > 0) {
            $poster->setLineHeight(1);
            $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

            $poster->textBox(implode(' ', $heading), [
                'x' => 48, 'y' => 280, 'width' => 950, 'height' => 80, 'fontSize' => 52
            ]);
        }

        $poster->snapshot($options['basedir'] . $filename);

        return $options['baseurl'] . $filename;
    }


    /**
     * Generate large title poster
     */
    private static function _large_title($i, $image, $options, $filename) {
        $poster = new PHPImage();
        $poster->setDimensionsFromImage($image)->draw($image);
        $poster->resize(1200, 630, true);

        $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

        // Draw site name
        $poster->text('knife.media', [
            'x' => 48, 'y' => 60, 'fontSize' => 20
        ]);


        $titles = [];

        if(!empty($options['title'])) {
            $titles[] = wp_specialchars_decode($options['title']);
        }

        if(!empty($options['tagline'])) {
            $titles[] = wp_specialchars_decode($options['tagline']);
        }

        // Draw quiz title
        if(count($titles) > 0) {
            $poster->textBox(implode(' ', $titles), [
                'x' => 48, 'y' => 105, 'width' => 950, 'height' => 200, 'fontSize' => 20
            ]);
        }


        // Draw heading
        if(!empty($options['heading'])) {
            $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');

            if(function_exists('mb_strtoupper')) {
                $options['heading'] = mb_strtoupper($options['heading']);
            }

            $poster->textBox($options['heading'], [
                'x' => 48, 'y' => 290, 'width' => 950, 'fontSize' => 64
            ]);
        }

        $poster->snapshot($options['basedir'] . $filename);

        return $options['baseurl'] . $filename;
    }

}
