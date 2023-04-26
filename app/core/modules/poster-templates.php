<?php
/**
 * Poster Templates
 *
 * Return generated posters using custom templates
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Poster_Templates {
    /**
     * Get poster templates
     */
    public static function get_templates( $target = '' ) {
        $include = get_template_directory() . '/core/include/posters';

        // Define empty templates
        $templates = array();

        // Loop over all posters and parse headers
        foreach ( glob( $include . '/*.php' ) as $poster ) {
            $data = get_file_data(
                $poster,
                array(
                    'target' => 'Target',
                    'title'  => 'Poster name',
                )
            );

            // Get filename
            $path = pathinfo( $poster );
            $name = $path['filename'];

            if ( empty( $target ) || $target === $data['target'] ) {
                $templates[ $name ] = $data['title'];
            }
        }

        return $templates;
    }

    /**
     * Print templates select
     */
    public static function print_select( $args = array() ) {
        $args = wp_parse_args(
            $args,
            array(
                'attributes' => array(),
                'selected'   => 0,
                'target'     => '',
                'echo'       => true,
            )
        );

        $options = array();

        // Get availible templates
        $templates = self::get_templates( $args['target'] );

        foreach ( $templates as $template => $label ) {
            $options[] = sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                esc_attr( $template ),
                esc_html( $label ),
                selected( $args['selected'], $template, false )
            );
        }

        // Add selected attribute if need
        if ( ! array_key_exists( $args['selected'], $templates ) ) {
            array_unshift(
                $options,
                sprintf(
                    '<option disabled selected value="">%s</option>',
                    esc_html__( 'Выберите шаблон генератора', 'knife-theme' )
                )
            );
        }

        $attributes = array();

        // Generate attributes
        foreach ( $args['attributes'] as $key => $value ) {
            $attributes[] = $key . '="' . $value . '"';
        }

        // Create select using attributes and options
        $select = sprintf(
            '<select %1$s>%2$s</select>',
            implode( ' ', $attributes ),
            implode( "\n", $options )
        );

        if ( $args['echo'] !== true ) {
            return $select;
        }

        echo $select; // phpcs:ignore
    }

    /**
     * Create poster using options
     *
     * @since 1.9
     */
    public static function create_poster( $options, $folder ) {
        $templates = self::get_templates();

        // Check if template defined
        if ( ! array_key_exists( $options['template'], $templates ) ) {
            return new WP_Error( 'prepare', esc_html__( 'Шаблон генерации не задан', 'knife-theme' ) );
        }

        $image = get_attached_file( $options['attachment'] );

        // Check image url by attachment id
        if ( $image === false ) {
            return new WP_Error( 'prepare', esc_html__( 'Не удалось найти вложение', 'knife-theme' ) );
        }

        // Append required ImageText class
        if ( ! class_exists( 'ImageText' ) ) {
            require_once get_template_directory() . '/core/classes/imagetext.class.php';
        }

        $upload = wp_upload_dir();

        // Define upload dir and url
        $basedir = $upload['basedir'] . $folder;
        $baseurl = $upload['baseurl'] . $folder;

        // Check upload folder
        if ( ! wp_is_writable( $basedir ) && ! wp_mkdir_p( $basedir ) ) {
            return new WP_Error( 'prepare', esc_html__( 'Проверьте права на запись', 'knife-theme' ) );
        }

        // Check post id existance
        if ( absint( $options['post_id'] ) === 0 ) {
            return new WP_Error( 'prepare', esc_html__( 'Пустое значение post ID', 'knife-theme' ) );
        }

        $textbox = array();

        // Fill textbox array
        if ( isset( $options['textbox'] ) ) {
            $textbox = array_map( 'wp_specialchars_decode', $options['textbox'] );
        }

        // Create poster file name
        $filename = $options['post_id'] . uniqid( '-' ) . '.jpg';

        try {
            $include = '/core/include/posters/' . $options['template'] . '.php';

            // Include posters template
            include get_template_directory() . $include;

        } catch ( Exception $error ) {
            return new WP_Error( 'generate', esc_html__( 'Ошибка генерации: ', 'knife-theme' ) . $error->getMessage() );
        }

        return $baseurl . $filename;
    }
}
