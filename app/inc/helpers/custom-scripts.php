<?php
/**
* Custom scripts
*
* Add external scripts in a proper way
* We have to remake it later
*
* @package knife-theme
* @since 1.1
*/

new Knife_Custom_Scripts;

class Knife_Custom_Scripts {

	public function __construct() {
		add_action('wp_head', [$this, 'print_tgm_script'], 12);
		add_action('wp_head', [$this, 'print_io_script'], 12);

		add_action('wp_footer', [$this, 'print_tgm_iframe']);
	}

	public function print_tgm_script() {
?>
<script>
	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KZ7MHM');
</script>
<?php
	}

	public function print_tgm_iframe() {
?>
<noscript>
	<iframe src="//www.googletagmanager.com/ns.html?id=GTM-KZ7MHM" height="0" width="0" style="display:none;"></iframe>
</noscript>
<?php
	}

	public function print_io_script() {
?>
<script>
	window._io_config = window._io_config || {};
	window._io_config['0.2.0'] = window._io_config['0.2.0'] || [];
	window._io_config['0.2.0'].push({
		page_url: '<?php echo wp_get_canonical_url(); ?>'
		, page_type: '<?php echo (is_single()) ? 'article' : 'default'; ?>'
		, page_title: '<?php wp_title(' - ',TRUE,'right'); bloginfo('name'); ?>'
		, page_language: 'ru'
<?php if(is_single()) : ?>
		, article_authors: [<?php @coauthors("', '", "', '", "'", "'"); ?>]
		, article_categories: ['<?php echo @get_the_category()[0]->cat_name; ?>']
<?php endif; ?>
	});

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id; js.async = true;
		js.src = 'https://cdn.onthe.io/io.js/IypUTPnGjxFH';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'io-cdnjs'));
</script>
<?php
	}
}
