jQuery(document).ready(function($) {
	if (typeof wp.media === 'undefined') return;

	var frame;
	var block = $("#knife-term-background");

	var toggle = function() {
        var image = block.find('.knife-input').val();

		block.find('.knife-image').remove();
		block.find('.knife-delete').hide();
		block.find('.knife-size').hide();

		if(image.length > 0) {
			$('<img />', {class: 'knife-image', src: image}).prependTo(block);

			block.find('.knife-size').show();
			block.find('.knife-delete').show();
		}
	}

	block.on('click', '.knife-select', function(e) {
		e.preventDefault();

		if(frame)
			return frame.open();

		frame = wp.media({
			title: knife_custom_background.choose,
			multiple: false
		});

		frame.on('select', function() {
			var attachment = frame.state().get('selection').first().toJSON();

			block.find('.knife-input').val(attachment.url);

			return toggle();
		});

		return frame.open();
	});

	block.on('click', '.knife-delete', function(e) {
		e.preventDefault();

		block.find('.knife-input').val('');

		return toggle();
	});

	return toggle();
});
