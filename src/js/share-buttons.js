(function() {
	var parent = '.share';

	if(document.querySelector(parent) === null)
		return false;

    var counters = {facebook: false, vkontakte: false};

    var make_url = function(network) {
		var link = document.querySelector('link[rel="canonical"]');

		if(link && link.href)
 			link = encodeURIComponent(link.href);
		else
			link = encodeURIComponent(window.location.href.replace(window.location.hash, ''));

		if(network === 'vkontakte')
			return 'https://vk.com/share.php?act=count&index=0&url=' + link;

 		if(network === 'facebook')
			return 'https://graph.facebook.com/?callback=FB.Share&id=' + link;
	}

	var open_popup = function(url, params) {
		var left = Math.round(screen.width / 2 - params.width / 2);
		var top = 0;

		if (screen.height > params.height)
			top = Math.round(screen.height / 3 - params.height / 2);

		var win = window.open(url, params.id, 'left=' + left + ',top=' + top + ',' +
			'width=' + params.width + ',height=' + params.height + ',personalbar=0,toolbar=0,scrollbars=1,resizable=1');
	}

	var get_shares = function(network) {
		var script = document.createElement('script');

		script.type = 'text/javascript';
		script.src = make_url(network);
		script.id = 'share-' + network;

		document.getElementsByTagName('head')[0].appendChild(script);

		return true;
	}

    var links = document.querySelectorAll('.share .share__link');

	if(links === null)
		return false;

	for(var i = 0; i < links.length; i++) {
		var network = links[i].dataset.label;

		links[i].addEventListener('click', function(e) {
			e.preventDefault();

			return open_popup(this.href, {width: 600, height: 400, id: this.dataset.label})
		});

		if(network in counters && counters[network] === false)
			counters[network] = get_shares(network);
	}

	window.VK = {
		Share: {
			count: function (id, count) {
				document.getElementById('share-vkontakte').outerHTML = '';

				if(typeof count === 'undefined' || !count)
					return;

				var links = document.querySelectorAll('.share .share__link--vkontakte');

                for(var i = 0; i < links.length; i++) {
					var child = document.createElement("span");
					child.className = 'share__count';
					child.innerHTML = count;

					links[i].appendChild(child);
				}
			}
		}
	}

	window.FB = {
		Share: function (data) {
			document.getElementById('share-facebook').outerHTML = '';

			if(typeof data.share === 'undefined' || !data.share.share_count)
				return;

			var links = document.querySelectorAll('.share .share__link--facebook');

			for(var i = 0; i < links.length; i++) {
				var child = document.createElement("span");
				child.className = 'share__count';
				child.innerHTML = data.share.share_count;

				links[i].appendChild(child);
			}
		}
	}
})();
