<?php
/**
 * Quiz section
 *
 * Custom post type for quiz
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Quiz_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $post_type = 'quiz';


    /**
     * Quiz share query var
     *
     * @access  private
     * @var     string
     */
    private static $query_var = 'share';


    /**
     * Unique nonce string
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-quiz-nonce';


   /**
    * Poster ajax action
    *
    * @access  private
    * @var     string
    */
    private static $ajax_action = 'knife-quiz-poster';


    /**
     * Unique meta to store quiz options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-quiz-options';


    /**
     * Unique meta to store quiz items
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-quiz-items';


    /**
     * Unique meta to store quiz results
     *
     * @access  private
     * @var     string
     */
    private static $meta_results = '_knife-quiz-results';


    /**
     * Directory to save results images
     *
     * @access  private
     * @var     string
     */
    private static $upload_folder = '/quiz/';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register quiz post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add rewrite url for sharing
        add_action('init', [__CLASS__, 'add_share_rule']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_share_var']);

        // Redirect to custom generated template if share query var exists
        add_action('template_redirect', [__CLASS__, 'redirect_share']);

        // Change single post type template path
        add_action('single_template', [__CLASS__, 'include_single']);

        // Add quiz metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 12);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Create result poster
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'create_poster']);

        // Include quiz options
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Add quiz post type to archives
        add_action('pre_get_posts', [__CLASS__, 'update_archives'], 12);
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$post_type, [
            'labels'                    => [
                'name'                  => __('Тесты', 'knife-theme'),
                'singular_name'         => __('Тест', 'knife-theme'),
                'add_new'               => __('Добавить тест', 'knife-theme'),
                'menu_name'             => __('Тесты', 'knife-theme'),
                'all_items'             => __('Все тесты', 'knife-theme'),
                'add_new_item'          => __('Добавить новый тест', 'knife-theme'),
                'new_item'              => __('Новый тест', 'knife-theme'),
                'edit_item'             => __('Редактировать тест', 'knife-theme'),
                'update_item'           => __('Обновить тест', 'knife-theme'),
                'view_item'             => __('Просмотреть тест', 'knife-theme'),
                'view_items'            => __('Просмотреть тесты', 'knife-theme'),
                'search_items'          => __('Искать тест', 'knife-theme'),
                'insert_into_item'      => __('Добавить в тест', 'knife-theme')
            ],
            'label'                 => __('Тесты', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'excerpt', 'comments', 'author'],
            'taxonomies'            => ['post_tag', 'category'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 4,
            'menu_icon'             => 'dashicons-list-view',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Add rewrite sharing parameter to post type url
     *
     * Mask: /%post-type%/%post-name%/%item-number%/
     */
    public static function add_share_rule() {
        add_rewrite_rule(
            sprintf('%s/([^/]+)/([^/]+)/?$',
                self::$post_type
            ),

            sprintf(
                'index.php?post_type=%1$s&%1$s=$matches[1]&%2$s=$matches[2]',
                self::$post_type,
                self::$query_var
            ),

            'top'
        );
    }


    /**
     * Append share query tag to availible query vars
     */
    public static function append_share_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include quiz meta options, items and results
     */
    public static function inject_object() {
        if(is_singular(self::$post_type)) {
            $post_id = get_the_ID();

            $options = get_post_meta($post_id, self::$meta_options, true);
            $options = wp_parse_args($options, [
                'achievment' => 0,
                'details' => 'none',
                'format' => 'binary'
            ]);

            // Add quiz items
            wp_localize_script('knife-theme', 'knife_quiz_items',
                (array) self::retrieve_items($post_id, $options)
            );

            // Add quiz results
            wp_localize_script('knife-theme', 'knife_quiz_results',
                (object) self::retrieve_results($post_id, $options)
            );

            if(method_exists('Knife_Share_Buttons', 'get_settings')) {
                $options = array_merge($options, [
                    'permalink' => get_permalink($post_id),
                    'share_links' => Knife_Share_Buttons::get_settings($post_id)
                ]);
            }

            // Add quiz options object
            wp_localize_script('knife-theme', 'knife_quiz_options', $options);
        }
    }


    /**
     * Redirect to custom generated template if share query var exists
     */
    public static function redirect_share() {
        $share = get_query_var('share');

        if(is_singular(self::$post_type) && strlen($share) > 0) {
            $post_id = get_the_ID();

            $options = get_post_meta($post_id, self::$meta_options, true);
            $options = wp_parse_args($options, [
                'achievment' => 0,
                'details' => 'none',
                'format' => 'binary'
            ]);

            $results = self::retrieve_results($post_id, $options);

            if(isset($results[$share])) {
                $blanks = array_fill_keys(['heading', 'description', 'poster'], '');

                $result = wp_parse_args(
                    array_intersect_key($results[$share], $blanks), $blanks
                );

                extract($result);

                $include = get_template_directory() . '/core/include';
                include_once($include . '/templates/quiz-share.php');

                exit;
            }
        }
    }


    /**
     * Include single quiz template
     */
    public static function include_single($template) {
        if(is_singular(self::$post_type)) {
            $new_template = locate_template(['templates/single-quiz.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Append quiz posts to archives
     */
    public static function update_archives($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        // Is in archive
        foreach(['tag', 'category', 'author', 'date', 'home'] as $archive) {
            $method = 'is_' . $archive;

            if($query->$method()) {
                $types = $query->get('post_type');

                if(!is_array($types)) {
                    $types = ['post'];
                }

                $types[] = self::$post_type;
                $query->set('post_type', $types);

                return false;
            }
        }
    }


    /**
     * Create result posters using ajax options
     */
    public static function create_poster() {
        check_admin_referer(self::$metabox_nonce, 'nonce');

        if(!method_exists('Knife_Poster_Templates', 'create_posters')) {
            wp_send_json_error(__('Модуль генерации не найден', 'knife-theme'));
        }

        $options = wp_parse_args($_REQUEST, [
            'template' => 'strong_head',
            'post_id' => 0,
            'attachment' => 0,
            'achievment' => ''
        ]);

        $posters = Knife_Poster_Templates::create_posters($options, self::$upload_folder);

        if(is_wp_error($posters)) {
            return wp_send_json_error($posters->get_error_message());
        }

        return wp_send_json_success((object) $posters);
    }


    /**
     * Add quiz metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-quiz-metabox', __('Настройки теста'), [__CLASS__, 'display_metabox'], self::$post_type, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== self::$post_type) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert scripts for dynaimc wp_editor
        wp_enqueue_editor();

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-quiz-metabox', $include . '/styles/quiz-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-quiz-metabox', $include . '/scripts/quiz-metabox.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'post_id' => absint($post_id),
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$metabox_nonce),
            'meta_items' => esc_attr(self::$meta_items),
            'meta_results' => esc_attr(self::$meta_results),
            'editor' => wp_default_editor(),
            'choose' => __('Выберите изображение', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-quiz-metabox', 'knife_quiz_metabox', $options);
    }


    /**
     * Display quiz metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/quiz-metabox.php');
    }


    /**
     * Save quiz options
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$post_type) {
            return;
        }

        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }


        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            update_post_meta($post_id, self::$meta_options, $_REQUEST[self::$meta_options]);
        }

        // Update items meta
        self::update_items(self::$meta_items, $post_id);

        // Update results meta
        self::update_results(self::$meta_results, $post_id);
    }


    /**
     * Update quiz items meta from post-metabox
     */
    private static function update_items($query, $post_id) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete quiz post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            // Filter answer array
            if(isset($item['answers']) && is_array($item['answers'])) {
                foreach($item['answers'] as $i => &$answer) {
                    // Remove answer if empty
                    if((bool) array_filter($answer) === false) {
                        unset($item['answers'][$i]);
                    }
                }
            }

            // Add post meta if not empty
            if(array_filter($item)) {
                add_post_meta($post_id, $query, wp_kses_post_deep($item));
            }
        }
    }


    /**
     * Update quiz results meta from post-metabox
     */
    private static function update_results($query, $post_id) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete quiz post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $result) {
            // We should unset empty posters array
            if(isset($result['posters']) && !array_filter($result['posters'])) {
                unset($result['posters']);
            }

            if(array_filter($result)) {
                add_post_meta($post_id, $query, wp_kses_post_deep($result));
            }
        }
    }


    /**
     * Retrieve items within meta to show as object
     */
    private static function retrieve_items($post_id, $options, $items = []) {
        // Loop through items
        foreach(get_post_meta($post_id, self::$meta_items) as $meta) {

            // Skip item if required field is empty
            foreach(['question', 'answers'] as $field) {
                if(empty($meta[$field])) {
                    continue 2;
                }
            }

            $answers = [];

            foreach($meta['answers'] as $i => $fields) {
                if($answer = self::get_answer($fields, $options)) {
                    $answers[] = $answer;
                }
            }

            if(count($answers) > 0) {
                $items[] = [
                    'question' => apply_filters('the_content', $meta['question']),
                    'answers' => $answers
                ];
            }

        }

        return $items;
    }


    /**
     * Retrieve results within meta to show as object
     */
    private static function retrieve_results($post_id, $options, $results = []) {
        // Loop through results
        foreach(get_post_meta($post_id, self::$meta_results) as $item) {
            $blanks = ['from' => 0, 'to' => 0];

            $points = wp_parse_args(
                array_intersect_key($item, $blanks), $blanks
            );

            $points = array_map('intval', $points);

            for($i = $points['from']; $points['to'] >= $i; $i++) {
                if(!empty($item['posters'][$i])) {
                    $result['poster'] = esc_url($item['posters'][$i]);
                }

                if(!empty($item['description'])) {
                    $result['description'] = wp_specialchars_decode($item['description']);
                }

                if($options['details'] === 'result' && !empty($item['details'])) {
                    $result['details'] = apply_filters('the_content', $item['details']);
                }

                if($options['details'] === 'remark' && !empty($options['remark'])) {
                    $result['details'] = apply_filters('the_content', $options['remark']);
                }


                $heading = [];

                if($options['achievment'] && !empty($item['achievment'])) {
                    $heading[] = str_replace('%', $i, $item['achievment']);
                }

                if(!empty($item['heading'])) {
                    $heading[] = wp_specialchars_decode($item['heading']);
                }

                if(count($heading) > 0) {
                    $result['heading'] = implode(' ', $heading);
                }


                if(array_filter($result)) {
                    if($options['format'] === 'category' && !empty($item['category'])) {
                        $category = mb_substr($item['category'], 0, 1);
                        $results[$category] = $result;

                        continue;
                    }

                    $results[$i] = $result;
                }
            }
        }

        return $results;
    }


    /**
     * Helper to get answer using options
     */
    private static function get_answer($fields, $options, $answer = []) {
        // Set choice
        if(empty($options['attachment'])) {
            if(empty($fields['choice'])) {
                return false;
            }

            $answer['choice'] = wp_kses_post($fields['choice']);
        }

        // Set attachment
        if(!empty($options['attachment'])) {
            if(empty($fields['attachment'])) {
                return false;
            }

            $attachment = wp_get_attachment_image_url($fields['attachment'], 'short');

            if($attachment === false) {
                return false;
            }

            $answer['attachment'] = esc_url($attachment);
        }

        // Set message if required
        if(!empty($options['message'])) {
            if(empty($fields['message'])) {
                return false;
            }

            $answer['message'] = wpautop(wp_kses_post($fields['message']));
        }

        switch($options['format']) {
            case 'category':
                if(!isset($fields['category'])) {
                    return false;
                }

                $answer['category'] = mb_substr($fields['category'], 0, 1);
            break;

            case 'points':
                if(!isset($fields['points'])) {
                    return false;
                }

                $answer['points'] = (int) $fields['points'];
            break;

            case 'binary':
                if(isset($fields['binary'])) {
                    $answer['binary'] = 1;
                }

            break;
        }


        return $answer;
    }
}


/**
 * Load current module environment
 */
Knife_Quiz_Section::load_module();
