(function() {
	var parent = '.search';
	var holder = 'search-gcse';
	var detect = 'gsc-results';

	if(document.querySelector(parent) === null)
		return false;

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


    var init = function() {
		// Create fake div for google results
		var fake = document.createElement("div");

		fake.id = holder;
		fake.style.display = 'none';

		// Append fake div to base selector
		document.querySelector(parent).appendChild(fake);

		// Prepare gcse callback
		window.__gcse = {
			parsetags: 'explicit',
			callback: push
		};

		// Load gsce to page
		var cx = '009571386059139339715:mqsuxgtrwyo';
		var gcse = document.createElement('script');
		gcse.type = 'text/javascript';
		gcse.async = true;
		gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(gcse, s);
	}


	var make = function() {
		var result = document.getElementById('search-results');
		var source = document.getElementById(holder).querySelectorAll('.gs-result');

		var html = '';

		if(document.getElementById('search-input').value.length > 0) {

			for(var i = 0; i < source.length; i++) {
				if(!source[i].querySelector('a.gs-title') || !source[i].querySelector('.gs-snippet'))
					return false;

				// TODO: Rework this behaviour
				html += '<div class="search__results-item"><a class="search__results-link" href="' + source[i].querySelector('a.gs-title').href + '">';
				html += '<p class="search__results-head">' + source[i].querySelector('a.gs-title').textContent + '</p>';
				html += '<p class="search__results-text">' + source[i].querySelector('.gs-snippet').textContent + '</p>';
				html += '</a></div>';
			}
		}

		// Clear nodes. Faster than innerHTML
		while (result.firstChild) {
			result.removeChild(result.firstChild);
		}

		result.innerHTML = html;
	}



	// Init google cse on search layer open
	document.querySelector('.topline .toggle--search').addEventListener('click', function(e) {
		e.preventDefault();

		document.getElementById('search-input').focus();

		if(this.classList.contains('toggle--expand'))
			return false;

		if(typeof window.__gcse === 'undefined')
			return init();
	});

})();
