jQuery(document).ready(function($) {
	if (typeof wp.media === 'undefined') return;

	var frame;
	var box = $("#knife-sticker-box");

	var wait = function() {
		box.find('.button').toggleClass('disabled');
		box.find('.spinner').toggleClass('is-active');

		box.find('.notice').remove();
	}

	var notice = function(data) {
		var status = $('<div />', {
			"class": "notice notice-error",
			"html": "<p>" + data + "</p>"
		});

		return status.prependTo(box);
	}

	box.on('click', '#knife-sticker-upload', function(e) {
		e.preventDefault();

		if(frame)
			return frame.open();

		frame = wp.media({
			title: knife_post_sticker.choose,
			multiple: false
		});

		frame.on('select', function() {
			var attachment = frame.state().get('selection').first().toJSON();

			var data = {
				action: 'knife_sticker_upload',
				post: box.data('post'),
				sticker: attachment.id
			}

			var xhr = $.ajax({method: 'POST', url: box.data('ajaxurl'), data: data}, 'json');

			xhr.done(function(answer) {
				wait();

				if(answer.success === false)
					return notice(answer.data);

				if(box.find('#knife-sticker-image').length > 0)
					return box.find('#knife-sticker-image').attr('src', answer.data);

				var img = $('<img />', {id: 'knife-sticker-image', src: answer.data});

				return img.prependTo(box);
			});

			return wait();
		});

		return frame.open();
	});

	box.on('click' , '#knife-sticker-delete', function(e) {
		e.preventDefault();

		var data = {
			action: 'knife_sticker_delete',
			post: box.data('post')
		}

		var xhr = $.ajax({method: 'POST', url: box.data('ajaxurl'), data: data}, 'json');

		xhr.done(function(answer) {
			wait();

			if(answer.success === false)
				return notice(answer.data);

			return box.find('#knife-sticker-image').remove();
		});

		return wait();
	});
});
