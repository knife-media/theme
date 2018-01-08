<?php
/**
* Common widgets handler
*
* Use for cross-widget functions
*
* @package knife-theme
* @since 1.1
*/

new Knife_Widgets_Handler;

class Knife_Widgets_Handler {
 	/**
	* Unique nonce for widget ajax requests
	*
	* @since	1.1
	* @access	private
	* @var		string
	*/
	private $nonce = 'knife-widget-nonce';

	public function __construct() {
		add_action('widget_update_callback', [$this, 'clear_widget_cache']);
		add_action('in_admin_footer', [$this, 'widget_script']);
		add_action('wp_ajax_knife-widget-terms', [$this, 'widget_terms']);
	}

	/**
	 * Remove widgets cache on save or delete post
	 */
	public function clear_widget_cache($instance) {
		$sidebars = get_option('sidebars_widgets');

		foreach($sidebars as $sidebar) {
			if(!is_array($sidebar))
				continue;

			foreach($sidebar as $widget)
				delete_transient($widget);
		}

		return $instance;
	}


	/**
	 * Custom terms form by taxonomy name
	 */
	public function widget_terms() {
		check_ajax_referer($this->nonce, 'nonce');

		wp_terms_checklist(0, [
			'taxonomy' => esc_attr($_POST['filter'])
		]);

		wp_die();
	}


	/**
	 * Ajax handler for terms load
	 */
	public function widget_script() {
?>
		<script type="text/javascript">
			(function() {
				jQuery(document).on('change', '.knife-widget-taxonomy', function() {
					var list = jQuery(this).closest('.widget-content').find('.knife-widget-termlist');

					var data = {
						action: 'knife-widget-terms',
						filter: jQuery(this).val(),
						nonce: '<?php echo wp_create_nonce($this->nonce); ?>'
					}

					jQuery.post(ajaxurl, data, function(response) {
						list.html(response);

						return list.show();
					});

					return list.hide();
				});
			})();
		</script>
<?php
	}
}
