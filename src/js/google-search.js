(function() {
	var parent = document.querySelector('.search');

	// Check if search id and template defined
	if(parent === null || typeof knife_search_id === 'undefined')
		return document.getElementById('toggle-search').classList.add('toggle--hidden');

	var holder = 'search-gcse';
	var detect = 'gsc-results';

	var view = null;

	var push = function() {
		if(document.readyState !== 'complete')
    		return google.setOnLoadCallback(push, true);

		// Render results to holder element
		google.search.cse.element.render({
			gname: holder,
			div: holder,
			tag: 'searchresults-only',
			attributes: {
				enableHistory: false,
				gaQueryParameter: 's',
				queryParameterName: 's'
			}
		});

		// Update fake results on search input
		document.getElementById('search-input').oninput = function() {
			var element = google.search.cse.element.getElement(holder);

			element.execute(this.value);
		}

		// Create observer to detect new results in gsce fake block
		var observer = window.MutationObserver || window.WebKitMutationObserver;

		view = new observer(function(mutations) {
			for (var i = 0; i < mutations.length; ++i) {
				if (!mutations[i].target.classList.contains(detect))
					continue;

				return make();
			}
		});

		view.observe(document.getElementById(holder), {subtree: true, attributes: false, childList: true, characterData: false});
	}


    var init = function(gcse_id) {
		// Create fake div for google results
		var fake = document.createElement("div");

		fake.id = holder;
		fake.style.display = 'none';

		// Append fake div to base selector
		parent.appendChild(fake);

		// Prepare gcse callback
		window.__gcse = {
			parsetags: 'explicit',
			callback: push
		};

		// Load gsce to page
		var gcse = document.createElement('script');
		gcse.type = 'text/javascript';
		gcse.async = true;
		gcse.src = 'https://cse.google.com/cse.js?cx=' + gcse_id;
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(gcse, s);
	}


	var make = function() {
		var result = document.getElementById('search-results');
		var source = document.getElementById(holder).querySelectorAll('.gs-result');

		if(document.getElementById('search-input').value.length > 0) {
			// Clear nodes. Faster than innerHTML
			while (result.firstChild) {
				result.removeChild(result.firstChild);
			}

			var append = function(source) {
				var data = {
					link: source.querySelector('a.gs-title').href,
					head: source.querySelector('a.gs-title').textContent,
					text: source.querySelector('.gs-snippet').textContent
				}

 				var head = document.createElement('p');
				head.className = 'search__results-head';
				head.appendChild(document.createTextNode(data.head));

 				var text = document.createElement('p');
				text.className = 'search__results-text';
				text.appendChild(document.createTextNode(data.text));

    			var link = document.createElement('a');
				link.className = 'search__results-link';
				link.href = data.link;
				link.appendChild(head);
 				link.appendChild(text);

				return result.appendChild(link);
			}

			for(var i = 0; i < source.length; i++) {
				if(!source[i].querySelector('a.gs-title') || !source[i].querySelector('.gs-snippet'))
					return false;

				append(source[i]);
			}
		}
	}


	// Init google cse on search layer open
	document.getElementById('toggle-search').addEventListener('click', function(e) {
		e.preventDefault();

		document.getElementById('search-input').focus();

		if(this.classList.contains('toggle--expand'))
			return false;

		if(typeof window.__gcse === 'undefined')
			return init(knife_search_id);
	});

})();
