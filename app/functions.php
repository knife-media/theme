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

	add_image_size('medium-thumb', 480, 99999, false);
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

function share_post($id){

			if (!$title){$title=get_the_title($id);}
			if (!$link){$link = urlencode(get_permalink($id));}


	?>
		<div class="social-sharing article__social-sharing">
          <span class="article__social-sharing-item">
			<a href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo $link; ?>&p[title]=<?php echo $title; ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,height=600,width=600');return false;" >
            <i class="fa fa-2x fa-facebook-official" aria-hidden="true"></i>
            <span>Пошерить</span>
			</a>
          </span>
          <span class="article__social-sharing-item vk_icon">
			<a href="http://vk.com/share.php?url=<?php echo $link; ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,height=600,width=600');return false;">
				<i class="fa fa-2x fa-vk" aria-hidden="true"></i>
				<span>Поделиться</span>
			</a>
          </span>
          <span class="article__social-sharing-item tel_icon">
			<a href="https://telegram.me/share/url?url=<?php echo $link; ?>&text=<?php echo $title; ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,height=600,width=600');return false;">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path class="f_w" d="M6,11.960784l-1,-3l11,-8l-15.378,5.914c0,0 -0.672,0.23 -0.619,0.655c0.053,0.425 0.602,0.619 0.602,0.619l3.575,1.203l1.62,5.154l2.742,-2.411l-0.007,-0.005l3.607,2.766c0.973,0.425 1.327,-0.46 1.327,-0.46l2.531,-13.435l-10,11zz"></path></svg>
				<span> </span>
			</a>
          </span>
          <span class="article__social-sharing-item tw_icon">
			<a href="https://twitter.com/intent/tweet?text=<?php echo $title;?>&url=<?php echo $link; ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,height=600,width=600');return false;">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path class="f_w" d="M15.96 3.42c-.04.153-.144.31-.237.414l-.118.058v.118l-.59.532-.237.295c-.05.036-.398.21-.413.237V6.49h-.06v.473h-.058v.294h-.058v.296h-.06v.235h-.06v.237h-.058c-.1.355-.197.71-.295 1.064h-.06v.116h-.06c-.02.1-.04.197-.058.296h-.06c-.04.118-.08.237-.118.355h-.06c-.038.118-.078.236-.117.353l-.118.06-.06.235-.117.06v.116l-.118.06v.12h-.06c-.02.057-.038.117-.058.175l-.118.06v.117c-.06.04-.118.08-.177.118v.118l-.237.177v.118l-.59.53-.532.592h-.117c-.06.078-.118.156-.177.236l-.177.06-.06.117h-.118l-.06.118-.176.06v.058h-.118l-.06.118-.353.12-.06.117c-.078.02-.156.04-.235.058v.06c-.118.038-.236.078-.354.118v.058H8.76v.06h-.12v.06h-.176v.058h-.118v.06H8.17v.058H7.99v.06l-.413.058v.06h-.237c-.667.22-1.455.293-2.36.293h-.886v-.058h-.53v-.06H3.27v-.06h-.295v-.06H2.68v-.057h-.177v-.06h-.236v-.058H2.09v-.06h-.177v-.058h-.177v-.06H1.56v-.058h-.12v-.06l-.294-.06v-.057c-.118-.04-.236-.08-.355-.118v-.06H.674v-.058H.555v-.06H.437v-.058H.32l-.06-.12H.142v-.058c-.13-.08-.083.026-.177-.118H1.56v-.06c.294-.04.59-.077.884-.117v-.06h.177v-.058h.237v-.06h.118v-.06h.177v-.057h.118v-.06h.177v-.058l.236-.06v-.058l.236-.06c.02-.038.04-.078.058-.117l.237-.06c.02-.04.04-.077.058-.117h.118l.06-.118h.118c.036-.025.047-.078.118-.118V12.1c-1.02-.08-1.84-.54-2.303-1.183-.08-.058-.157-.118-.236-.176v-.117l-.118-.06v-.117c-.115-.202-.268-.355-.296-.65.453.004.987.008 1.354-.06v-.06c-.254-.008-.47-.08-.65-.175v-.058H2.32v-.06c-.08-.02-.157-.04-.236-.058l-.06-.118h-.117l-.118-.178h-.12c-.077-.098-.156-.196-.235-.294l-.118-.06v-.117l-.177-.12c-.35-.502-.6-1.15-.59-2.006h.06c.204.234.948.377 1.357.415v-.06c-.257-.118-.676-.54-.827-.768V5.9l-.118-.06c-.04-.117-.08-.236-.118-.354h-.06v-.118H.787c-.04-.196-.08-.394-.118-.59-.06-.19-.206-.697-.118-1.005h.06V3.36h.058v-.177h.06v-.177h.057V2.83h.06c.04-.118.078-.236.117-.355h.118v.06c.12.097.237.196.355.295v.118l.118.058c.08.098.157.197.236.295l.176.06.354.413h.118l.177.236h.118l.06.117h.117c.04.06.08.118.118.177h.118l.06.118.235.06.06.117.356.12.06.117.53.176v.06h.118v.058l.236.06v.06c.118.02.236.04.355.058v.06h.177v.058h.177v.06h.176v.058h.236v.06l.472.057v.06l1.417.18v-.237c-.1-.112-.058-.442-.057-.65 0-.573.15-.99.354-1.358v-.117l.118-.06.06-.235.176-.118v-.118c.14-.118.276-.236.414-.355l.06-.117h.117l.12-.177.235-.06.06-.117h.117v-.058H9.7v-.058h.177v-.06h.177v-.058h.177v-.06h.296v-.058h1.063v.058h.294v.06h.177v.058h.178v.06h.177v.058h.118v.06h.118l.06.117c.08.018.158.038.236.058.04.06.08.118.118.177h.118l.06.117c.142.133.193.163.472.178.136-.12.283-.05.472-.118v-.06h.177v-.058h.177v-.06l.236-.058v-.06h.177l.59-.352v.176h-.058l-.06.295h-.058v.117h-.06v.118l-.117.06v.118l-.177.118v.117l-.118.06-.354.412h-.117l-.177.236h.06c.13-.112.402-.053.59-.117l1.063-.353z"></path></svg>
				<span> </span>
			</a>
          </span>
        </div>
<?php }

function escapeJsonString($value) {
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}
*/
