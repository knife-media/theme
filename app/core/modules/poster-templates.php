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
            'large_title' => __('Только крупный заголовок', 'knife-theme'),
            'single_head' => __('Однострочный заголовок и описание', 'knife-theme'),
            'strong_head' => __('Многострочный заголовок и описание', 'knife-theme')
        ];

        return $templates;
    }


    /**
     * Get posters
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
            return new WP_Error('method', __('Не найден метод генератора', 'knife-theme'));
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
     * Generate result multiline poster
     */
    private static function _strong_head($i, $image, $options, $filename) {
        $poster = new PHPImage();
        $poster->setDimensionsFromImage($image)->draw($image);
        $poster->resize(1200, 630, true);

        $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

        // Draw site name
        $poster->text('knife.media', [
            'x' => 48, 'y' => 40, 'fontSize' => 16
        ]);


        // Draw description
        if(!empty($options['description'])) {
            $poster->textBox($options['description'], [
                'x' => 48, 'y' => 440, 'width' => 950, 'height' => 120, 'fontSize' => 24
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
                'x' => 48, 'y' => 280, 'width' => 950, 'height' => 160, 'fontSize' => 52
            ]);
        }

        $poster->snapshot($options['basedir'] . $filename);

        return $options['baseurl'] . $filename;
    }
}
