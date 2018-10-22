/**
 * Replace embed links with iframes
 *
 * @since 1.5
 */

(function() {
  var youtube = document.querySelectorAll( ".embed-youtube" );

  for (var i = 0; i < youtube.length; i++) {
      youtube[i].addEventListener( "click", function(e) {
        e.preventDefault();

        var iframe = document.createElement( "iframe" );

        iframe.setAttribute( "frameborder", "0" );
        iframe.setAttribute( "allowfullscreen", "" );
        iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );

        while (this.firstChild) {
          this.removeChild(this.firstChild);
        }

        this.appendChild(iframe);
    });
  };

})();
