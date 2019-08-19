<?php
/**
 * Hidden widgets
 *
 * Hide or show widgets conditionally
 * Fork of jetpack-widget-visibility plugin
 *
 * @link https://wordpress.org/plugins/jetpack-widget-visibility/
 * @package knife-theme
 * @since 1.5
 * @version 1.8
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Hidden_Widgets {
    /**
     * Store passed template-redirect option
     *
     * @access  private
     * @var     bool
     */
    private static $passed_template_redirect = false;


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Set admin-side actions
        add_action('admin_init', [__CLASS__, 'init_admin']);

        // Set frontend actions
        if(!is_admin() && !in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'])) {
            add_action('init', [__CLASS__, 'init']);
        }
    }


    /**
     * Set frontend actions on init
     */
    public static function init() {
        add_filter('widget_display_callback', [__CLASS__, 'filter_widget']);
        add_filter('sidebars_widgets', [__CLASS__, 'sidebars_widgets']);
        add_action('template_redirect', [__CLASS__, 'template_redirect']);
    }


    /**
     * Set admin-side actions
     */
    public static function init_admin() {
        add_action('sidebar_admin_setup', [__CLASS__, 'include_assets']);
        add_action('in_widget_form', [__CLASS__, 'widget_conditions_admin'], 10, 3);
        add_action('wp_ajax_widget_conditions_options', [__CLASS__, 'widget_conditions_options']);
        add_action('wp_ajax_widget_conditions_has_children', [__CLASS__, 'widget_conditions_has_children']);

        add_filter('widget_update_callback', [__CLASS__, 'widget_update'], 10, 3);
    }


    /**
     * Include assets to sidebars pages
     */
    public static function include_assets() {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_style('knife-hidden-widgets', $include . '/styles/hidden-widgets.css');
        wp_enqueue_script('knife-hidden-widgets', $include . '/scripts/hidden-widgets.js', ['jquery', 'jquery-ui-core'], $version, true);
    }


    /**
     * Provided a second level of granularity for widget conditions.
     */
    public static function widget_conditions_options_echo($major = '', $minor = '') {
        if(in_array($major,  array('category', 'tag')) && is_numeric($minor)) {
            $minor = self::maybe_get_split_term($minor, $major);
        }

        switch($major){
            case 'category':
                printf('<option value="">%s</option>',
                    __('Все страницы категории', 'knife-theme')
                );

                $categories = get_categories([
                    'number' => 1000,
                    'orderby' => 'count',
                    'order' => 'DESC'
                ]);

                usort($categories, [__CLASS__, 'strcasecmp_name']);

                foreach($categories as $category){
                    printf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        esc_attr($category->term_id),
                        selected($category->term_id, $minor, false),
                        esc_html($category->name)
                    );
                }
            break;

            case 'tag':
                printf('<option value="">%s</option>',
                    __('Все страницы тегов', 'knife-theme')
                );

                $tags = get_tags([
                    'number' => 1000,
                    'hide_empty' => false,
                    'orderby' => 'count',
                    'order' => 'DESC'
                ]);

                usort($tags, array( __CLASS__, 'strcasecmp_name'));

                foreach($tags as $tag){
                    printf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        esc_attr($tag->term_id),
                        selected($tag->term_id, $minor, false),
                        esc_html($tag->name)
                    );
                }
            break;

            case 'meta':
                printf(
                    '<option value="promo" %1$s>%2$s</option>',
                    selected('promo', $minor, false),
                    __('Партнерский материал', 'knife-theme')
                );
            break;

            case 'page':
                if('post' == $minor) {
                    $minor = 'post_type-post';
                }

                if(!$minor) {
                    $minor = 'post_type-page';
                }

                printf(
                    '<option value="front" %1$s>%2$s</option>',
                    selected('front', $minor, false),
                    __('Главная страница', 'knife-theme')
                );

                printf(
                    '<option value="posts" %1$s>%2$s</option>',
                    selected('posts', $minor, false),
                    __('Страница постов', 'knife-theme')
                );

                printf(
                    '<option value="archive" %1$s>%2$s</option>',
                    selected('archive', $minor, false),
                    __('Архивная страница', 'knife-theme')
                );

                printf(
                    '<option value="404" %1$s>%2$s</option>',
                    selected('404', $minor, false),
                    __('Ошибка 404', 'knife-theme')
                );

                printf(
                    '<option value="search" %1$s>%2$s</option>',
                    selected('search', $minor, false),
                    __('Страница поиска', 'knife-theme')
                );


                printf('<optgroup label="%s">',
                    esc_attr('Тип поста:', 'knife-theme')
                );

                $post_types = get_post_types(['public' => true], 'objects');

                foreach($post_types as $post_type) {
                    printf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        esc_attr('post_type-' . $post_type->name),
                        selected('post_type-' . $post_type->name, $minor, false),
                        esc_html($post_type->labels->singular_name)
                    );
                }

                printf('</optgroup>');

                printf('<optgroup label="%s">',
                    esc_attr('Статичная страница:', 'knife-theme')
                );

                echo str_replace(' value="' . esc_attr($minor). '"',
                    ' value="' . esc_attr($minor). '" selected="selected"',
                    preg_replace('/<\/?select[^>]*?>/i', '', wp_dropdown_pages( array('echo' => false)))
                );

                echo '</optgroup>';
            break;

            case 'taxonomy':
                printf('<option value="">%s</option>',
                    __('Все страницы таксономии', 'knife-theme')
                );

                $taxonomies = get_taxonomies(['_builtin' => false], 'objects');

                usort($taxonomies, [__CLASS__, 'strcasecmp_name']);

                $parts = explode('_tax_', $minor);

                if(2 === count($parts)) {
                    $minor_id = self::maybe_get_split_term($parts[1], $parts[0]);
                    $minor = $parts[0] . '_tax_' . $minor_id;
                }

                foreach($taxonomies as $taxonomy) {
                    // Skip co-authors taxonomy
                    // TODO: Remove condition on co-authors removing
                    if($taxonomy->name === 'author') {
                        continue;
                    }

                    printf(
                        '<optgroup label="%s">',
                        esc_attr($taxonomy->labels->name . ':', 'knife-theme')
                    );

                    printf(
                        '<option value="%1$s" %2$s>%3$s</option>',
                        esc_attr($taxonomy->name),
                        selected($taxonomy->name, $minor, false),
                        __('Все страницы', 'knife-theme')
                    );

                    $terms = get_terms(
                        [$taxonomy->name],
                        ['number' => 250, 'hide_empty' => false]
                    );

                    foreach($terms as $term) {
                        printf(
                            '<option value="%1$s" %2$s>%3$s</option>',
                            esc_attr($taxonomy->name . '_tax_' . $term->term_id),
                            selected($taxonomy->name . '_tax_' . $term->term_id, $minor, false),
                            esc_html($term->name)
                        );
                    }

                    echo '</optgroup>';
                }
            break;
        }
    }


    /**
     * This is the AJAX endpoint for the second level of conditions.
     */
    public static function widget_conditions_options() {
        self::widget_conditions_options_echo($_REQUEST['major'],
            isset($_REQUEST['minor']) ? $_REQUEST['minor'] : ''
        );

        die;
    }


    /**
     * Provide an option to include children of pages.
     */
    public static function widget_conditions_has_children_echo($major = '', $minor = '', $has_children = false){
        if(!$major || 'page' !== $major || !$minor){
            return null;
        }

        if('front' == $minor){
            $minor = get_option('page_on_front');
        }

        if(!is_numeric($minor)) {
            return null;
        }

        $page_children = get_pages([
            'child_of' => (int) $minor
        ]);

        if($page_children) {
            printf(
                '<label class="condition-children"><input type="checkbox" id="include_children" name="conditions[page_children][]" value="has" %1$s />%2$s</label>',
                checked($has_children, true, false),
                __('Включить вложенные', 'knife-theme')
            );
        }
    }


    /**
     * This is the AJAX endpoint for the has_children input.
     */
    public static function widget_conditions_has_children() {
        self::widget_conditions_has_children_echo($_REQUEST['major'],
            isset($_REQUEST['minor']) ? $_REQUEST['minor'] : '',
            isset($_REQUEST['has_children']) ? $_REQUEST['has_children'] : false
        );

        die;
    }


    /**
     * Add the widget conditions to each widget in the admin.
     */
    public static function widget_conditions_admin($widget, $return, $instance){
        $conditions = [];

        if(isset($instance['conditions'])) {
            $conditions = $instance['conditions'];
        }

        if(!isset($conditions['action'])) {
            $conditions['action'] = 'show';
        }

        if(empty($conditions['rules'])) {
            $conditions['rules'][] = ['major' => '', 'minor' => '', 'has_children' => ''];
        }

        $conditions_class = 'widget-conditional';

        if(empty($_POST['widget-conditions-visible']) || $_POST['widget-conditions-visible'] == '0')  {
            $conditions_class = $conditions_class . ' widget-conditional-hide';
        }

        ?>
        <div class="<?php echo $conditions_class ?>">
            <?php
                printf(
                    '<input type="hidden" name="widget-conditions-visible" value="%s" />',
                    isset($_POST['widget-conditions-visible']) ? esc_attr($_POST['widget-conditions-visible']) : '0'
                );

                if(!isset($_POST['widget-conditions-visible'])) {
                    printf('<a href="#" class="button display-options">%s</a>',
                        '<span class="dashicons dashicons-admin-tools"></span>'
                    );
                }
            ?>

            <div class="widget-conditional-inner">
                <div class="condition-top">
                    <?php
                        echo '<select name="conditions[action]">';

                        printf('<option value="show" %1$s>%2$s</option>',
                            selected($conditions['action'], 'show', false),
                            esc_html('Показать', 'knife-theme')
                        );

                        printf('<option value="hide" %1$s>%2$s</option>',
                            selected($conditions['action'], 'hide', false),
                            esc_html('Скрыть', 'knife-theme')
                        );

                        echo '</select>';
                    ?>
                </div>

                <div class="conditions">
                    <?php foreach($conditions['rules'] as $rule) : ?>

                    <?php
                        $rule = wp_parse_args($rule, [
                            'major' => '',
                            'minor' => '',
                            'has_children' => ''
                        ]);
                    ?>
                        <div class="condition">
                            <div class="selection">
                                <select class="conditions-rule-major" name="conditions[rules_major][]">
                                    <?php
                                        printf(
                                            '<option value="" %1$s>%2$s</option>',
                                            selected("", $rule['major'], false),
                                            __('Выбрать условие', 'knife-theme')
                                        );

                                        printf(
                                            '<option value="tag" %1$s>%2$s</option>',
                                            selected("tag", $rule['major'], false),
                                            __('Метка', 'knife-theme')
                                        );

                                        printf(
                                            '<option value="page" %1$s>%2$s</option>',
                                            selected("page", $rule['major'], false),
                                            __('Страница', 'knife-theme')
                                        );

                                        printf(
                                            '<option value="category" %1$s>%2$s</option>',
                                            selected("category", $rule['major'], false),
                                            __('Категория', 'knife-theme')
                                        );

                                        printf(
                                            '<option value="meta" %1$s>%2$s</option>',
                                            selected("meta", $rule['major'], false),
                                            __('Свойство записи', 'knife-theme')
                                        );

                                        if(get_taxonomies(['_builtin' => false])) {
                                            printf(
                                                '<option value="taxonomy" %1$s>%2$s</option>',
                                                selected("taxonomy", $rule['major'], false),
                                                __('Таксономия', 'knife-theme')
                                            );
                                        }
                                    ?>
                                </select>

                                <?php
                                    $conditions_attr = sprintf('data-loading-text="%s"',
                                        __('Загрузка&hellip;', 'knife-theme')
                                    );

                                    if(!$rule['major']) {
                                        $conditions_attr = $conditions_attr . ' disabled="disabled"';
                                    }
                                ?>

                                <select class="conditions-rule-minor" name="conditions[rules_minor][]" <?php echo $conditions_attr; ?>>
                                    <?php self::widget_conditions_options_echo($rule['major'], $rule['minor']); ?>
                                </select>

                                <span class="conditions-rule-has-children">
                                    <?php self::widget_conditions_has_children_echo($rule['major'], $rule['minor'], $rule['has_children']); ?>
                                </span>
                            </div>

                            <div class="condition-control">
                                <span class="condition-conjunction"><?php _e('или', 'knife-theme'); ?></span>

                                <div class="actions">
                                    <a href="#" class="add-condition">
                                        <span class="dashicons dashicons-plus-alt"></span>
                                    </a>
                                    <a href="#" class="delete-condition">
                                        <span class="dashicons dashicons-dismiss"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }


    /**
     * On an AJAX update of the widget settings, process the display conditions.
     */
    public static function widget_update($instance, $new_instance, $old_instance){
        if(empty($_POST['conditions'])) {
            return $instance;
        }

        $conditions = [];
        $conditions['action'] = $_POST['conditions']['action'];
        $conditions['rules'] = [];

        foreach($_POST['conditions']['rules_major'] as $index => $major_rule){
            if(!$major_rule) {
                continue;
            }

            $conditions['rules'][] = [
                'major' => $major_rule,
                'minor' => isset($_POST['conditions']['rules_minor'][$index]) ? $_POST['conditions']['rules_minor'][$index] : '',
                'has_children' => isset($_POST['conditions']['page_children'][$index]) ? true : false,
           ];
        }

        if(!empty($conditions['rules'])) {
            $instance['conditions'] = $conditions;
        } else {
            unset($instance['conditions']);
        }

        return $instance;
    }


    /**
     * Filter the list of widgets for a sidebar so that active sidebars work as expected.
     */
    public static function sidebars_widgets($widget_areas){
        $settings = [];

        foreach($widget_areas as $widget_area => $widgets){
            if(empty($widgets)) {
                continue;
            }

            if(!is_array($widgets)) {
                continue;
            }

            if('wp_inactive_widgets' == $widget_area) {
                continue;
            }

            foreach($widgets as $position => $widget_id){
                // Find the conditions for this widget.
                if(preg_match('/^(.+?)-(\d+)$/', $widget_id, $matches)) {
                    $id_base = $matches[1];
                    $widget_number = intval($matches[2]);
                }
                else {
                    $id_base = $widget_id;
                    $widget_number = null;
                }

                if(!isset($settings[$id_base])) {
                    $settings[$id_base] = get_option('widget_' . $id_base);
                }

                // New multi widget (WP_Widget)
                if(!is_null($widget_number)) {
                    if(isset($settings[$id_base][$widget_number]) && false === self::filter_widget($settings[$id_base][$widget_number])) {
                        unset($widget_areas[$widget_area][$position]);
                    }
                }

                // Old single widget
                else if(!empty($settings[ $id_base ]) && false === self::filter_widget($settings[$id_base])) {
                    unset($widget_areas[$widget_area][$position]);
                }
            }
        }

        return $widget_areas;
    }


    /**
     * Set template redirect option
     */
    public static function template_redirect() {
        self::$passed_template_redirect = true;
    }


    /**
     * Generates a condition key based on the rule array
     */
    private static function generate_condition_key($rule){
        if(isset($rule['has_children'])) {
            return $rule['major'] . ":" . $rule['minor'] . ":" . $rule['has_children'];
        }

        return $rule['major'] . ":" . $rule['minor'];
    }


    /**
     * Determine whether the widget should be displayed based on conditions set by the user.
     */
    public static function filter_widget($instance){
        global $wp_query;

        if(empty($instance['conditions']) || empty($instance['conditions']['rules'])) {
            return $instance;
        }

        // Store the results of all in-page condition lookups so that multiple widgets with
        // the same visibility conditions don't result in duplicate DB queries.
        static $condition_result_cache = [];

        $condition_result = false;

        foreach($instance['conditions']['rules'] as $rule){
            $condition_key = self::generate_condition_key($rule);

            if(isset($condition_result_cache[ $condition_key ])) {
                $condition_result = $condition_result_cache[ $condition_key ];
            } else {
                switch($rule['major']) {
                     case 'page':
                        // Previously hardcoded post type options.
                        if ('post' == $rule['minor']) {
                            $rule['minor'] = 'post_type-post';
                        }
                        else if(!$rule['minor']) {
                            $rule['minor'] = 'post_type-page';
                        }

                        switch($rule['minor']){
                            case '404':
                                $condition_result = is_404();
                            break;

                            case 'search':
                                $condition_result = is_search();
                            break;

                            case 'archive':
                                $condition_result = is_archive();
                            break;

                            case 'posts':
                                $condition_result = $wp_query->is_posts_page;
                            break;

                            case 'home':
                                $condition_result = is_home();
                            break;

                            case 'front':
                                $condition_result = is_front_page() && !is_paged();
                            break;

                            default:
                                if(substr($rule['minor'], 0, 10) == 'post_type-') {
                                    $condition_result = is_singular(substr($rule['minor'], 10)) && !is_front_page();
                                } elseif($rule['minor'] == get_option('page_for_posts')) {
                                    // If $rule['minor'] is a page ID which is also the posts page
                                    $condition_result = $wp_query->is_posts_page;
                                } else {
                                    // $rule['minor'] is a page ID
                                    $condition_result = is_page() && ($rule['minor'] == get_the_ID());

                                    // Check if $rule['minor'] is parent of page ID
                                    if(!$condition_result && isset($rule['has_children']) && $rule['has_children']) {
                                        $condition_result = wp_get_post_parent_id(get_the_ID()) == $rule['minor'];
                                    }
                                }
                            break;
                        }
                    break;

                    case 'tag':
                        if(!$rule['minor'] && is_tag()) {
                            $condition_result = true;
                        } else {
                            $rule['minor'] = self::maybe_get_split_term($rule['minor'], $rule['major']);

                            if(is_singular() && $rule['minor'] && has_tag($rule['minor'])) {
                                $condition_result = true;
                            } else {
                                $tag = get_tag($rule['minor']);

                                if($tag && !is_wp_error($tag) && is_tag($tag->slug)) {
                                    $condition_result = true;
                                }
                            }
                        }
                    break;

                    case 'category':
                        if(!$rule['minor'] && is_category()) {
                            $condition_result = true;
                        } else {
                            $rule['minor'] = self::maybe_get_split_term($rule['minor'], $rule['major']);

                            if(is_category($rule['minor'])) {
                                $condition_result = true;
                            } else if(is_singular() && $rule['minor'] && in_array('category', get_post_taxonomies()) && has_category($rule['minor'])) {
                                $condition_result = true;
                            }
                        }
                    break;

                    case 'meta':
                        if($rule['minor'] === 'promo' && is_singular()) {
                            if(get_post_meta(get_the_ID(), '_knife-promo', true)) {
                                $condition_result = true;
                            }
                        }
                    break;

                    case 'taxonomy':
                        $term = explode('_tax_', $rule['minor']); // $term[0] = taxonomy name; $term[1] = term id

                        if(isset($term[0])&& isset($term[1])) {
                            $term[1] = self::maybe_get_split_term($term[1], $term[0]);
                        }

                        if(isset($term[1]) && is_tax($term[0], $term[1])) {
                            $condition_result = true;
                        } else if(isset($term[1]) && is_singular() && $term[1] && has_term($term[1], $term[0])) {
                            $condition_result = true;
                        } else if(is_singular() && $post_id = get_the_ID()) {
                            $terms = get_the_terms($post_id, $rule['minor']); // Does post have terms in taxonomy?

                            if($terms && !is_wp_error($terms)) {
                                $condition_result = true;
                            }
                        }
                    break;
                }

                if($condition_result || self::$passed_template_redirect){
                    // Some of the conditions will return false when checked before the template_redirect
                    // action has been called, like is_page(). Only store positive lookup results, which
                    // won't be false positives, before template_redirect, and everything after.
                    $condition_result_cache[ $condition_key ] = $condition_result;
                }
            }

            if($condition_result) {
                break;
            }
        }

        if(('show' == $instance['conditions']['action'] && !$condition_result) || ('hide' == $instance['conditions']['action'] && $condition_result)) {
            return false;
        }

        return $instance;
    }


    /**
     * Map strcasecmp function
     */
    public static function strcasecmp_name($a, $b){
        return strcasecmp($a->name, $b->name);
    }


    /**
     * Split terms
     */
    public static function maybe_get_split_term($old_term_id = '', $taxonomy = '') {
        $term_id = $old_term_id;

        if('tag' == $taxonomy){
            $taxonomy = 'post_tag';
        }

        if(function_exists('wp_get_split_term') && $new_term_id = wp_get_split_term($old_term_id, $taxonomy)) {
            $term_id = $new_term_id;
        }

        return $term_id;
    }
}


/**
 * Load current module environment
 */
Knife_Hidden_Widgets::load_module();
