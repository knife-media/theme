(function(){
	return;

 window.__gcse = {
		parsetags: 'explicit',
		callback: gcseCallback
	};
		var cx = '009571386059139339715:mqsuxgtrwyo';
		var gcse = document.createElement('script');
		gcse.type = 'text/javascript';
		gcse.async = true;
		gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(gcse, s);


function gcseCallback() {
	var result = document.createElement("div");

google.search.cse.element.render(
        {
gname:'gsearch',
          div: "results",
          tag: 'searchresults-only',
attributes: {
enableHistory: false,
gaQueryParameter: 's',
queryParameterName: 's'
}
         });

};

	var updateResults = function() {
		var res = document.getElementById('results');
		var items = res.querySelectorAll('.gs-result');

		document.getElementById('xx').innerHTML = '';

		for(var i = 0; i < items.length; i++) {
   			var it = document.createElement('div');
			it.innerHTML = items[i].querySelector('a.gs-title').textContent;

			document.getElementById('xx').appendChild(it);
		}

	}

	var input = document.getElementById('search-input');

	input.oninput = function() {
		var element = google.search.cse.element.getElement('gsearch');
		element.execute(input.value);
	}

	var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
	new MutationObserver(function(mutations) {
		for (var i = 0; i < mutations.length; ++i) {
			if (mutations[i].target.classList.contains('gsc-results')) {
				updateResults();
				break;
			}
		}
	}).observe(document.getElementById('results'), {
		subtree:       true,
		attributes:    false,
		childList:     true,
		characterData: false
	});

})();

