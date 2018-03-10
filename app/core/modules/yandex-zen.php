<?php
/**
* Yandex.Zen feed template
*
* Add custom Yandex.Zen feed template
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
	die;
}

new Knife_Yandex_Zen;

class Knife_Yandex_Zen {

  /**
   * Exclude post item meta
   */
  private $meta = '_knife-feed';

  /**
   * Feed slug and template name
   */
	private $slug = 'zen';

  /**
   * Content allowed tags
   */
  private $tags = ['<br>','<p>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<ol>','<li>','<img>','<figcaption>','<figure>','<b>','<strong>','<i>','<em>'];

  /**
   * Current post enclosure array
   */
  private $enclosure = null;


  public function __construct() {
    add_action('init', [$this, 'init_feeds']);
    add_action('wp', [$this, 'add_hooks']);

    // alter feed main query loop
    add_action('pre_get_posts', [$this, 'update_query']);
  }


  /**
   * Update custom feed template
   */
  public function add_hooks() {
    if(!is_feed($this->slug))
      return;

    // remove post content unwanted tags
    add_filter('the_content_feed', [$this, 'clear_content']);

    // add post category and enclosure after content
    add_action('rss2_item', [$this, 'insert_category']);
    add_action('rss2_item', [$this, 'insert_enclosure']);

    // update rss2 head with atom link
    add_action('rss2_head', [$this, 'update_head']);
  }


  /**
   * Register new feed endpoint
   */
  public function init_feeds() {
    add_feed($this->slug, [$this, 'add_feed']);
  }


   /**
   * Update posts query
   */
  public function update_query($query) {
    if($query->is_main_query() && $query->is_feed() && $this->slug === $query->get('feed')) {
      $query->set('posts_per_rss', 50);
      $query->set('post_type', 'post');
      $query->set('post_status', 'publish');

      $query->set('meta_query', [[
        'key' => $this->meta,
        'compare' => 'NOT EXISTS'
      ]]);
    }
  }


   /**
   * Init template feed
   */
  public function add_feed() {
    require get_template_directory() . '/core/include/feeds/' . $this->slug . '.php';
  }


  /**
   * Add atom self link
   */
  public function update_head() {
    printf('<atom:link href="%s" rel="self" type="application/rss+xml" />', get_feed_link($this->slug));
  }


   /**
   * Upgrade content tag
   */
  public function clear_content($content) {
    $content = $this->add_images($content);

    $content = $this->strip_tags($content, $this->tags);
    $content = $this->clear_xml($content);


    return $content;
  }


   /**
   * Add post content images
   */
  public function add_images($content) {
    preg_match_all('~<img\s+?src="(.+?)"~is', $content, $images, PREG_PATTERN_ORDER);

    $enclosure = $this->add_thumbnail([]);

    foreach($images[1] as $link) {
      if(empty($link))
        continue;

      $mime = wp_check_filetype($link);

      array_push($enclosure, [
        'type' => $mime['type'],
        'link' => $link
      ]);
    }

    $this->enclosure = $enclosure;

    return $content;
  }


   /**
   * Add post thumbnail to enclosure list
   */
  public function add_thumbnail($enclosure) {
    global $post;

    if(!has_post_thumbnail($post->ID))
      return $enclosure;

    array_push($enclosure, [
      'type' => get_post_mime_type(get_post_thumbnail_id($post->ID)),
      'link' => get_the_post_thumbnail_url($post->ID, 'outer')
    ]);

    return $enclosure;
  }


   /**
   * Insert post categories
   */
  public function insert_category() {
    foreach(get_the_category() as $category) {
      printf('<category>%s</category>', esc_html($category->cat_name));
    }
  }


  /**
   * Insert enclosure to content
   *
   * @link https://yandex.ru/support/zen/publishers/rss-modify.html#publication
   */
  public function insert_enclosure() {
    foreach($this->enclosure as $image) {
      printf('<enclosure url="%s" type="%s" />', esc_url($image['link']), $image['type']);
    }
  }


   /**
   * Strip tags, scripts and styles
   */
  public function strip_tags($string, $allowable_tags = null) {
    $string = preg_replace('#<(script|style)[^>]*?>.*?</\\1>#si', '', $string);
    $string = strip_tags($string, implode(',', $allowable_tags));

    return $string;
  }


   /**
   * Remove unwanted content
   */
  public function clear_xml($string) {
    $string = preg_replace('/[\r\n]+/', "\n", $string);
    $string = preg_replace('/[ \t]+/', ' ', $string);

    $string = preg_replace('~<figure\s+?class="figure\s+?figure--embed">.+?</figure>~si', '', $string);

    $string = preg_replace('/ style="[^"]+"/', '', $string);
    $string = preg_replace('/ srcset="[^"]+"/', '', $string);
    $string = preg_replace('/ sizes="[^"]+"/', '', $string);
    $string = preg_replace('/ class="[^"]+"/', '', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    $string = preg_replace('/<[^\/>]*><\/[^>]*>/', '', $string);

    $string = str_replace('&hellip;', '...', $string);
    $string = str_replace('&nbsp;', ' ', $string);
    $string = str_replace('> <', '><', $string);
    $string = str_replace(PHP_EOL, '', $string);

    $string = force_balance_tags($string);

    return trim($string);
  }
}
