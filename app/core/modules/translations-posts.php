<?php
/**
 * Translations posts
 *
 * Custom post meta to mark translations posts
 *
 * @package knife-theme
 * @since 1.15
 * @version 1.16
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Translations_Posts {
    /**
     * Post meta to pass translations option
     *
     * @access  public
     * @var     string
     */
    public static $meta_translations = '_knife-translations';


    /**
     * Default post type with posts labels
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post'];


    /**
     * Archive query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'translations';


    /**
     * Users ID to force translations meta on publish
     *
     * @access public
     * @var    array
     */
    public static $translators = [132];

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Create custom translations archive url
        add_action('init', [__CLASS__, 'add_translations_rule']);

        // Update query_vars for translations archive
        add_action('parse_query', [__CLASS__, 'update_query_vars']);

        // Add query tag
        add_action('query_vars', [__CLASS__, 'append_translations_var']);

        // Include archive template for translations posts
        add_filter('template_include', [__CLASS__, 'include_archive']);

        // Show posts only with translations post meta
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Update translations archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_document_title'], 10);

        // Schedule appending translation meta on publish
        add_action('transition_post_status', [__CLASS__, 'schedule_translation'], 10, 3);

        // Set translation on publish for certain author
        add_action('knife_schedule_translation', [__CLASS__, 'set_translation']);

        // Add setting to set translations post meta
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox']);

        // Update translations post meta
        add_action('save_post', [__CLASS__, 'save_meta']);
    }


    /**
     * Create custom translations archive url
     */
    public static function add_translations_rule() {
        add_rewrite_rule(
            sprintf('^%s/?$', self::$query_var),
            sprintf('index.php?%s=1', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/page/([0-9]+)/?$', self::$query_var),
            sprintf('index.php?%s=1&paged=$matches[1]', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/[^/]+/(?:feed/)?', self::$query_var),
            sprintf('index.php', self::$query_var),
            'top'
        );
    }


    /**
     * Update query_vars for translations archive
     */
    public static function update_query_vars() {
        global $wp_query;

        if(array_key_exists(self::$query_var, $wp_query->query_vars)) {
            $wp_query->is_archive = true;
            $wp_query->is_home = false;
        }
    }


    /**
     * Append translations query tag to availible query vars
     */
    public static function append_translations_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include archive.php template for translations posts
     */
    public static function include_archive($template) {
        if(get_query_var(self::$query_var)) {
            $new_template = locate_template(['archive.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Show only translations posts in custom archive template
     */
    public static function update_query($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        if(get_query_var(self::$query_var)) {
            $query->set('post_type', self::$post_type);

            $query->set('meta_key', self::$meta_translations);
            $query->set('meta_value', 1);
        }
    }


    /**
     * Update post archive caption title
     */
    public static function update_archive_title($title) {
        if(get_query_var(self::$query_var)) {
            $title = sprintf('<h1 class="caption__title">%s</h1>',
                __('Переводы статей', 'knife-theme')
            );
        }

        return $title;
    }


    /**
     * Update translations archive document title
     */
    public static function update_document_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Переводы статей', 'knife-theme');
        }

        return $title;
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(!in_array($post->post_type, self::$post_type)) {
            return;
        }

        $translations = get_post_meta($post->ID, self::$meta_translations, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_translations),
            __('Статья-перевод', 'knife-theme'),
            checked($translations, 1, false)
        );
    }


    /**
     * Save translations term for post
     */
    public static function save_meta($post_id) {
        if(wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_translations])) {
            return delete_post_meta($post_id, self::$meta_translations);
        }

        return update_post_meta($post_id, self::$meta_translations, 1);
    }

    /**
     * Set translation meta on publish for certain users
     */
    public static function schedule_translation($new_status, $old_status, $post) {
        if($new_status !== 'publish') {
            return;
        }

        if(!property_exists('Knife_Authors_Manager', 'meta_authors')) {
            return;
        }

        $authors = get_post_meta($post->ID, Knife_Authors_Manager::$meta_authors);

        if(count(array_intersect(self::$translators, $authors)) > 0) {
            wp_schedule_single_event(time(), 'knife_schedule_translation', [$post->ID]);
        }
    }

    /**
     * Set translation on publish for certain author
     */
    public static function set_translation($post_id) {
        update_post_meta($post_id, self::$meta_translations, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Translations_Posts::load_module();
