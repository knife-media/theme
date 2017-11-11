<?php
/**
 * Important functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @package knife-theme
 * @since 1.1
 */


// Insert required js files
add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('knife-scripts', get_bloginfo('template_url') . '/assets/scripts.min.js', [], '0.1', true);
});


// Insert styles
add_action('wp_print_styles', function() {
   	wp_enqueue_style('knife-styles', get_bloginfo('template_url') . '/assets/styles.min.css', [], '0.1');
});


// Insert fonts
add_action('wp_enqueue_scripts', function() {

});


// Change mail from fields
add_filter('wp_mail_from_name', function($name) {
	return __('knife.media webmaster', 'knife-theme');
});

add_filter('wp_mail_from', function($email) {
	$hostname = parse_url(site_url("/"), PHP_URL_HOST);

	return "no-reply@{$hostname}";
});


// Remove useless widgets from wp-admin section
add_action('admin_init', function() {
	remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
	remove_meta_box('dashboard_primary', 'dashboard', 'normal');
});


// Custom image sizes
add_action('after_setup_theme', function(){
	add_theme_support('post-thumbnails');

	add_image_size('medium-thumbnail', 480, 99999, false);
	add_image_size('related-thumbnail', 360, 180, true);


	add_image_size( 'cb-100-65', 100, 65, true );
	add_image_size( 'cb-260-170', 260, 170, true );
	add_image_size( 'cb-360-490', 360, 490, true );
	add_image_size( 'cb-360-240', 360, 240, true );
	add_image_size( 'cb-378-300', 378, 300, true );
	add_image_size( 'cb-759-300', 759, 300, true );
	add_image_size( 'cb-759-500', 759, 500, true );
	add_image_size( 'cb-759-600', 759, 600, true );
	add_image_size( 'cb-1400-600', 1400, 600, true );
});


// Add new post formats
add_action('after_setup_theme', function() {
	add_theme_support('post-formats', array('aside'));
});


// Add theme menus
add_action('init', function() {
	register_nav_menus([
		'main_menu' => 'Верхнее меню',
		'footer_menu' => 'Нижнее меню'
	]);
});


// Remove fcking emojis and wordpress meta for security reasons
add_action('init', function() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link' );
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0 );
	remove_action('wp_head', 'rest_output_link_wp_head', 10 );
	remove_action('wp_head', 'wp_oembed_add_discovery_links', 10 );
});


// Admin bar
add_action('init', function() {
	add_filter('show_admin_bar', '__return_false');

	add_action('admin_bar_menu', function($wp_admin_bar) {
		$wp_admin_bar->remove_menu('customize');
	}, 999);
});


// Disable embeds
add_action('wp_enqueue_scripts', function() {
	wp_deregister_script('wp-embed');
});


// Disable jquery
add_action('wp_enqueue_scripts', function() {
	if(!is_admin()) {
		wp_deregister_script('jquery');
	}
});


// Rewrite urls after switch theme just in case
add_action('after_switch_theme', function() {
     flush_rewrite_rules();
});


// We don't want to use default gallery styles anymore
add_filter('use_default_gallery_style', '__return_false');


// Change default header menu classes
add_filter('nav_menu_css_class', function($classes, $item, $args) {
	if($args->theme_location !== 'main_menu')
		return $classes;

	return ['topline__menu-item'];
}, 10, 3);


// Add class to menu item link
add_filter('nav_menu_link_attributes', function($atts, $item, $args) {
	if($args->theme_location !== 'main_menu')
		return $atts;

	$atts['class'] = 'topline__menu-link';

	return $atts;
}, 10, 3);


// Remove menu ids
add_filter('nav_menu_item_id', '__return_empty_string');


// Rename aside post format
add_filter('gettext_with_context', function($translation, $text, $context, $domain) {
	$names = [
		'Aside'  => __('Без сайдбара', 'knife-theme'),
		'Standard' => __('Стандартный', 'knife-theme')
	];

	if($context !== 'Post format')
		return $translation;

	return str_replace(array_keys($names), array_values($names), $text);
}, 10, 4);


// Remove useless image attributes
add_filter('post_thumbnail_html', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('image_send_to_editor', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);


/*

 class Clean_Walker_Nav extends Walker_Nav_Menu {

	function filter_builtin_classes( $var ) {
	    return ( FALSE === strpos( $var, 'item' ) ) ? $var : '';
	}
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='submenu'>\n";
	}
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = '';
		$value = '';
		$unfiltered_classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = array_filter( $unfiltered_classes, array( $this, 'filter_builtin_classes' ) );
		if ( preg_grep("/^current/", $unfiltered_classes) ) {
			$classes[] = 'active';
		}
		if ( preg_grep("/^current_page_item/", $unfiltered_classes) ) {
			$classes[] = 'curr_active';
		}

		if($item->menu_item_parent == 0 ){
			$menu_class="menu__item";
			$link_class="menu__link";
		}else{
			$menu_class="submenu__item";
			$link_class="submenu__link";
		}

		$classes[] = $menu_class;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $value . $class_names .'>';
		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class']   = $link_class;
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}



/*


function knife_theme_setup() {


//	require( get_template_directory() . '/widgets/widgets.php' );
	include_once('shortcodes.php');
	include_once('a-lex/menuW.php');
	include_once('a-lex/post.php');
	include_once('a-lex/user_info.php');



}


function _widgets_on_init() {
	register_sidebar( array(
		'name' => 'Главная страница',
		'id' => 'homepage_widget_area',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );


}
add_action( 'widgets_init', '_widgets_on_init' );


function _check_if_public_query_vars(&$v, $k) {
	$public_query_vars = array('m', 'p', 'posts', 'w', 'cat', 'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence', 'debug', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'tag', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error', 'comments_popup', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'robots', 'taxonomy', 'term', 'cpage', 'post_type');
	if (!in_array($k, $public_query_vars))
		unset($v);
}

function apostol_more_posts_action_callback() {
	global $wp_query;
	if (!empty($_POST['offset'])) {
		$p_offset = (int) $_POST['offset'];
	}
	$query_vars = wp_parse_args($_POST['query_string']);
	array_walk($query_vars, '_check_if_public_query_vars');

	if (!empty($_POST['offset'])) {
		$query_vars['offset'] = $p_offset;
	}

	$available_templates = apply_filters('apostol_ajax_available_templates', array('category', 'related', 'search'));
	if (in_array($_POST['template'], $available_templates)) {
		$template = $_POST['template'];
		if($template =='search'){

			$s = $_POST['text'];
			$query_vars = array(
				'posts_per_page' => 10,
				's' => $s,
				'post_type' => array('post', 'video', 'page'),
			);
			$wp_query = new WP_Query($query_vars);

			if (have_posts()) {
				while ( have_posts() ) : the_post();
					get_template_part('loop/loop', 'search');
					$news_count++;
					echo '<span class="hr search__separator"></span>';
				endwhile;

			}else{
				echo 'noresult';
			}


		}else{

			$wp_query = new WP_Query($query_vars);

			$i = 0;
			if (have_posts()) {
				while ( have_posts() ) : the_post();
					get_template_part('loop', $template);
				endwhile;
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				if ($wp_query->max_num_pages > $p_offset/get_option('posts_per_page')+1) {  ?>
					<div class="loader clearfix">
						<a class="more-btn load" href="#" data-container-id="content" data-template="<?php echo $template;?>" data-current-page="<?php echo ($paged - 1) * get_option('posts_per_page') ; ?>" data-query-string="<?php echo $_POST['query_string']; ?>">Load More</a>
					</div>
				<?php }
			}
		}
	}
	die();
}

add_action('wp_ajax_more_posts', 'apostol_more_posts_action_callback');
add_action('wp_ajax_nopriv_more_posts', 'apostol_more_posts_action_callback');

function the_content_text( $charlength ){
	$content = strip_tags(do_shortcode(get_the_excerpt()));
	$charlength++;

	if ( mb_strlen( $content) > $charlength ) {
		$the_content_text = "";
		$subex = mb_substr( $content, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$the_content_text .= mb_substr( $subex, 0, $excut );
		} else {
			$the_content_text .= $subex;
		}
		$the_content_text .= '...';
	} else {
		$the_content_text .= $content;
	}
	return $pre.$the_content_text;
}
function crop_the_text( $text, $charlength ){
	$content = strip_tags($text);
	$charlength++;

	if ( mb_strlen( $content) > $charlength ) {
		$the_content_text = "";
		$subex = mb_substr( $content, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$the_content_text .= mb_substr( $subex, 0, $excut );
		} else {
			$the_content_text .= $subex;
		}
		$the_content_text .= '...';
	} else {
		$the_content_text .= $content;
	}
	return $pre.$the_content_text;
}
function crop_the_text_hard( $text, $charlength ){
	$content = strip_tags($text);
	if ( mb_strlen( $content) > $charlength ) {
		$the_content_text = mb_substr($content, 0, $charlength );
		$the_content_text .= '...';
	} else {
		$the_content_text .= $content;
	}
	return $the_content_text;
}

function escapeJsonString($value) {
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}
*/


