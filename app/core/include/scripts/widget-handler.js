(function() {
	jQuery(document).on('change', '.knife-widget-taxonomy', function() {
		var list = jQuery(this).closest('.widget-content').find('.knife-widget-termlist');

		var data = {
			action: 'knife_widget_terms',
			filter: jQuery(this).val(),
			nonce: knife_widget_handler.nonce
		}

		jQuery.post(ajaxurl, data, function(response) {
			list.html(response);

			return list.show();
		});

		return list.hide();
	});
})();
