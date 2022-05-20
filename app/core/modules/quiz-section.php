<?php
/**
 * Quiz section
 *
 * Custom post type for quiz
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.14
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Quiz_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  public
     * @var     string
     */
    public static $post_type = 'quiz';


    /**
     * Quiz share query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'share';


    /**
     * Unique nonce string
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-quiz-nonce';


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

        // Generate result posters
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'generate_posters']);

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
            'supports'              => ['title', 'thumbnail', 'excerpt', 'comments', 'author', 'custom-fields'],
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
            'has_archive'           => true,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Add rewrite sharing parameter to post type url
     * Mask: /%post-type%/%post-name%/%item-number%/
     *
     * @version 1.14
     */
    public static function add_share_rule() {
        add_rewrite_rule(
            sprintf('%s/([^/]+)/(\d+)/?$',
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
        $post_id = get_queried_object_id();

        if(is_singular(self::$post_type)) {
            $options = (array) get_post_meta($post_id, self::$meta_options, true);

            $options = wp_parse_args($options, [
                'achievment' => 0,
                'details' => 'none',
                'format' => 'binary'
            ]);

            // Add quiz items
            wp_localize_script('knife-theme', 'knife_quiz_items',
                self::retrieve_items($post_id, $options)
            );

            // Add quiz results
            wp_localize_script('knife-theme', 'knife_quiz_results',
                self::retrieve_results($post_id, $options)
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
        $object = get_queried_object();

        if($object === null) {
            return;
        }

        // Get share var
        $share = get_query_var(self::$query_var);

        if(empty($share) || !is_singular(self::$post_type)) {
            return;
        }

        $post_id = $object->ID;

        // Get quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        $options = wp_parse_args($options, [
            'achievment' => 0,
            'details' => 'none',
            'format' => 'binary'
        ]);

        // Get quiz results
        $results = self::retrieve_results($post_id, $options);

        // Split share query var
        $param = explode('/', $share);

        if(array_key_exists($param[0], $results)) {
            $index = $param[0];
            $blank = array_fill_keys(['heading', 'description', 'poster'], '');

            $result = wp_parse_args(
                array_intersect_key($results[$index], $blank), $blank
            );

            extract($result);

            // Update poster with dynamic portion
            if(isset($param[1]) && preg_match('~^[a-z0-9]+$~', $param[1])) {
                $poster = str_replace('*', $param[1], $poster);
            }

            $include = get_template_directory() . '/core/include';
            include_once($include . '/templates/quiz-share.php');

            exit;
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
        foreach(['tag', 'category', 'author', 'date', 'home', 'tax'] as $archive) {
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
     * Add quiz metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-quiz-metabox', __('Настройки теста', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        if(get_post_type($post->ID) !== self::$post_type) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert scripts for dynamic wp_editor
        wp_enqueue_editor();

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-quiz-metabox', $include . '/styles/quiz-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-quiz-metabox', $include . '/scripts/quiz-metabox.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'post_id' => absint($post->ID),
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$ajax_nonce),
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
     * Create result posters using ajax options
     */
    public static function generate_posters() {
        check_admin_referer(self::$ajax_nonce, 'nonce');

        if(!method_exists('Knife_Poster_Templates', 'create_poster')) {
            wp_send_json_error(__('Модуль генерации не найден', 'knife-theme'));
        }

        $options = wp_parse_args($_REQUEST, [
            'template' => '',
            'post_id' => 0,
            'attachment' => 0,
            'achievment' => ''
        ]);

        $points = ['from' => 0, 'to' => 0];

        if(isset($options['from'])) {
            $points['from'] = intval($options['from']);
        }

        if(isset($options['to'])) {
            $points['to'] = intval($options['to']);
        }

        if(isset($options['textbox']['heading'])) {
            $heading = $options['textbox']['heading'];
        }

        $posters = [];

        // Loop through result points
        for($i = $points['from']; $points['to'] >= $i; $i++) {
            // If not first poster and achievment not replaced
            if(count($posters) > 0 && strpos($options['achievment'], '%') === false) {
                $posters[$i] = array_pop(array_reverse($posters));

                continue;
            }

            $options['textbox']['heading'] = str_replace('%', $i, $options['achievment']);

            if(isset($heading)) {
                $options['textbox']['heading'] = $options['textbox']['heading'] . ' ' . $heading;
            }

            // Create poster
            $poster = Knife_Poster_Templates::create_poster($options, self::$upload_folder);

            if(is_wp_error($poster)) {
                wp_send_json_error($poster->get_error_message());
            }

            $posters[$i] = $poster;
        }

        return wp_send_json_success((object) $posters);
    }


    /**
     * Save quiz options
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$post_type) {
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
            $options = wp_kses_post_deep($_REQUEST[self::$meta_options]);

            // Remove slashes
            $options = stripslashes_deep($options);

            if(isset($options['remark'])) {
                $options['remark'] = wp_targeted_link_rel(links_add_target($options['remark']));
            }

            update_post_meta($post_id, self::$meta_options, $options);
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

        // Remove slashes
        $items = stripslashes_deep($_REQUEST[$query]);

        // Define allowed img attribites
        $allowed = [
            'src' => [],
            'style' => [],
            'class' => [],
            'alt' => []
        ];

        foreach($items as $item) {
            // Filter answer array
            if(isset($item['answers']) && is_array($item['answers'])) {
                foreach($item['answers'] as $i => &$answer) {
                    // Remove answer if empty
                    if((bool) array_filter($answer) === false) {
                        unset($item['answers'][$i]);
                    }

                    // Add blank and rel attributes to answer
                    if(!empty($answer['message'])) {
                        $answer['message'] = apply_filters('content_save_pre', $answer['message']);
                    }

                    // Remove all tags except img from choice
                    if(!empty($answer['choice'])) {
                        $answer['choice'] = wp_kses($answer['choice'], ['img' => $allowed]);
                    }
                }
            }

            // Add blank and rel attributes to question
            if(isset($item['question'])) {
                $item['question'] = apply_filters('content_save_pre', $item['question']);
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

        // Remove slashes
        $results = stripslashes_deep($_REQUEST[$query]);

        foreach($results as $result) {
            // We should unset empty posters array
            if(isset($result['posters']) && !array_filter($result['posters'])) {
                unset($result['posters']);
            }

            // Add blank and rel attributes to details
            if(isset($result['details'])) {
                $result['details'] = wp_targeted_link_rel(links_add_target($result['details']));
            }

            if(array_filter($result)) {
                add_post_meta($post_id, $query, wp_kses_post_deep($result));
            }
        }
    }


    /**
     * Retrieve items within meta to show as object
     */
    private static function retrieve_items($post_id, $options) {
        $items = [];
        $count = 0;

        // Loop through items
        foreach(get_post_meta($post_id, self::$meta_items) as $meta) {

            // Skip item if required field is empty
            foreach(['question', 'answers'] as $field) {
                if(empty($meta[$field])) {
                    continue 2;
                }
            }

            $answers = [];

            foreach($meta['answers'] as $fields) {
                if($answer = self::get_answer($fields, $options, $count++)) {
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
    private static function retrieve_results($post_id, $options) {
        $results = [];

        // Loop through results
        foreach(get_post_meta($post_id, self::$meta_results) as $meta) {
            $blanks = ['from' => 0, 'to' => 0];

            $points = wp_parse_args(
                array_intersect_key($meta, $blanks), $blanks
            );

            $points = array_map('intval', $points);

            // Set blank points if format does not use them
            if(in_array($options['format'],  ['category', 'dynamic'])) {
                $points = $blanks;
            }

            for($i = $points['from']; $points['to'] >= $i; $i++) {
                $result = [];

                if(!empty($meta['posters'][$i])) {
                    $result['poster'] = esc_url($meta['posters'][$i]);
                }

                if(!empty($meta['description'])) {
                    $result['description'] = wp_specialchars_decode($meta['description']);
                }

                if($options['details'] === 'result' && !empty($meta['details'])) {
                    $result['details'] = apply_filters('the_content', $meta['details']);
                }

                if($options['details'] === 'remark' && !empty($options['remark'])) {
                    $result['details'] = apply_filters('the_content', $options['remark']);
                }


                $heading = [];

                if($options['achievment'] && !empty($meta['achievment'])) {
                    $heading[] = str_replace('%', $i, $meta['achievment']);
                }

                if(!empty($meta['heading'])) {
                    $heading[] = wp_specialchars_decode($meta['heading']);
                }

                if(count($heading) > 0) {
                    $result['heading'] = implode(' ', $heading);
                }

                if(!array_filter($result)) {
                    continue;
                }

                $result['advance'] = $i;

                // Generate results only for category format
                if($options['format'] === 'category') {
                    // Check if category exists
                    if(!empty($meta['category'])) {
                        $result['advance'] = mb_substr($meta['category'], 0, 1);
                    }
                }

                // Generate results only for dynamic format
                if($options['format'] === 'dynamic') {
                    // Check if dynamic exists
                    if(!empty($meta['dynamic'])) {
                        $result['poster'] = esc_url($meta['dynamic']);
                        unset($result['advance']);
                    }
                }

                $results[] = $result;
            }
        }

        return $results;
    }


    /**
     * Helper to get answer using options
     */
    private static function get_answer($fields, $options, $id) {
        $answer = [];

        // Generate alphabet for dynamic format
        $alphabet = array_merge(range('0', '9'), range('a', 'z'));

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
            case 'dynamic':
                $answer['dynamic'] = $alphabet[$id];
            break;

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
