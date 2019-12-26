jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-quiz-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_quiz_metabox.error === 'undefined') {
    return false;
  }


  /**
   * Use this variable to store for current editor id
   */
  box.data('editor', 1);


  /**
   * Add class for short time
   */
  function blinkClass(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  /**
   * Show generate loader
   */
  function toggleLoader(result) {
    var spinner = result.find('.result__image-spinner');

    spinner.toggleClass('is-active');

    result.find('.result__image-generate').prop('disabled',
      result.hasClass('is-active')
    );
  }


  /**
   * Update poster fields
   */
  function updatePosters(result, points, posters) {
    if(typeof posters === 'undefined') {
      posters = {};

      result.find('input[data-poster]').each(function() {
        var score = $(this).data('poster');

        posters[score] = $(this).val();
      });
    }

    // Clone first field
    var clone = result.find('.poster:first').clone();

    // Find fields
    var fields = result.find('.result__share-posters');

    // Clear posters to recreate them below
    fields.empty();

    for(var i = points.from; i <= points.to; i++) {
      var poster = clone.clone();

      // Set points in title
      poster.find('.poster__title > span').text(i);

      // Set input attributes
      poster.find('.poster__field').attr({
        'value': '', 'data-poster': i
      });

      if(typeof posters[i] !== 'undefined') {
        poster.find('.poster__field').val(posters[i]);
      }

      fields.append(poster);
    }

    if(fields.children().length === 0) {
      fields.append(clone);
    }

    sortPosters(result);
  }


  /**
   * Generate poster using ajax
   */
  function createPosters(result, points) {
    var data = {
      'action': knife_quiz_metabox.action,
      'nonce': knife_quiz_metabox.nonce,
      'post_id': knife_quiz_metabox.post_id,
      'textbox': {}
    }

    // Add required poster fields
    $.each(['attachment', 'template'], function(i, v) {
      data[v] = result.find('[data-result="' + v + '"]').val();
    });

    // Add achievment if need
    if(result.hasClass('result--achievment')) {
      data.achievment = result.find('[data-result="achievment"]').val();
    }

    // Add points to data object
    $.extend(data, points);

    data.textbox.title = $('#title').val() || '';

    if($('#knife-tagline-input').val()) {
      // If title not emnpty
      if(data.textbox.title) {
        data.textbox.title = data.textbox.title + ' ';
      }

      data.textbox.title = data.textbox.title + $('#knife-tagline-input').val();
    }

    // Add required poster fields
    $.each(['heading', 'description'], function(i, v) {
      data.textbox[v] = result.find('[data-result="' + v + '"]').val();
    });


    // Clear warning before request
    var warning = result.find('.result__image-warning');
    warning.html('').hide();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      toggleLoader(result);

      if(typeof answer.data === 'undefined') {
        return warning.html(knife_quiz_metabox.error).show();
      }

      answer.success = answer.success || false;

      if(answer.success === true && typeof answer.data === 'object') {
        var poster = result.find('.result__image-poster');

        $.each(answer.data, function(i, image) {
          // Create image if not exists
          if(poster.find('img').length === 0) {
            $('<img />').prependTo(poster);
          }

          poster.find('img').attr('src', image);

          return false;
        });

        return updatePosters(result, points, answer.data);
      }

      return warning.html(answer.data).show();
    });

    xhr.error(function() {
      toggleLoader(result);

      return warning.html(knife_quiz_metabox.error).show();
    });

    return toggleLoader(result);
  }


  /**
   * Update wp.editor using editorId
   */
  function updateEditor(el) {
    if(typeof knife_quiz_metabox.editor === 'undefined') {
      knife_quiz_metabox.editor = 'html';
    }

    $.each(el.find('.wp-editor-area'), function(i, editor) {
      var textarea = $(editor);
      var editorId = textarea.attr('id');

      if(typeof editorId === 'undefined') {
        editorId = 'knife-quiz-editor-' + box.data('editor');
        textarea.attr('id', editorId);

        box.data().editor++;
      } else {
        wp.editor.remove(editorId);
      }

      wp.editor.initialize(editorId, {
        tinymce: {
          toolbar1: 'link',
          invalid_styles: 'color font-weight font-size',
          init_instance_callback: function() {
            if(window.tinymce && window.switchEditors) {
              window.switchEditors.go(editorId, knife_quiz_metabox.editor);
            }
          }
        },
        quicktags: {
          buttons: 'link'
        },
        mediaButtons: true
      });
    });
  }


  /**
   * Update questions order
   */
  function sortQuestions(callback) {
    if(typeof knife_quiz_metabox.meta_items === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_items = knife_quiz_metabox.meta_items;

    box.find('.item:not(:first)').each(function(i) {
      var item = $(this);

      // Update question title
      item.find('.item__title > span').text(i + 1);

      // Change fields name
      item.find('[data-item]').each(function() {
        var data = $(this).data('item');

        // Create name attribute
        var name = meta_items + '[' + i + ']';

        $(this).attr('name', name + '[' + data + ']');
      });

      item.data('number', i);
    });

    if(typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Update answers order
   */
  function sortAnswers(item) {
    if(typeof knife_quiz_metabox.meta_items === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_items = knife_quiz_metabox.meta_items;

    // Get item number
    var number = item.data('number');

    $.each(item.find('.answer:not(:first)'), function(i) {
      var answer = $(this);

      // Loop through answers fields
      answer.find('[data-answer]').each(function() {
        var data = $(this).data('answer');

        // Create name attribute
        var name = meta_items;

        $.each([number, 'answers', i, data], function(j, v) {
          name = name + '[' + v + ']';
        });

        $(this).attr('name', name);
      });
    });
  }


  /**
   * Update results order
   */
  function sortResults(callback) {
    if(typeof knife_quiz_metabox.meta_results === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_results = knife_quiz_metabox.meta_results;

    box.find('.result:not(:first)').each(function(i) {
      var result = $(this);

      // Update question title
      result.find('.result__title > span').text(i + 1);

      // Change fields name
      result.find('[data-result]').each(function() {
        var data = $(this).data('result');

        // Create name attribute
        var name = meta_results + '[' + i + ']';

        $(this).attr('name', name + '[' + data + ']');
      });

      result.data('number', i);
    });

    if(typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Update posters order
   */
  function sortPosters(result) {
    if(typeof knife_quiz_metabox.meta_results === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    // Hide scores warning
    result.find('.result__scores-warning').hide();

    var meta_results = knife_quiz_metabox.meta_results;

    // Get item number
    var number = result.data('number');

    // Loop through answers fields
    result.find('[data-poster]').each(function() {
      var data = $(this).data('poster');

      // Create name attribute
      var name = meta_results;

      $.each([number, 'posters', data], function(i, v) {
        name = name + '[' + v + ']';
      });

      $(this).attr('name', name);

      if($(this).val().length === 0) {
        result.find('.result__scores-warning').show();
        return false;
      }
    });
  }


  /**
   * Image append
   */
  function appendImage(poster, attachment, thumbnail, callback) {
    if(typeof knife_quiz_metabox.choose === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    // Create custom state
    var state = wp.media.controller.Library.extend({
      defaults: _.defaults({
        filterable: 'all',
      }, wp.media.controller.Library.prototype.defaults)
    });

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_quiz_metabox.choose,
      multiple: false,
      states: [
        new state()
      ]
    });

    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden inputs values
      poster.find(attachment).val(selection.id);

      // Set thumbnail as selection if exists
      if(thumbnail && typeof selection.sizes[thumbnail] !== 'undefined') {
        selection = selection.sizes[thumbnail];
      }

      // Create image if not exists
      if(poster.find('img').length === 0) {
        $('<img />').prependTo(poster);
      }

      poster.find('img').attr('src', selection.url);

      if(typeof callback === 'function') {
        return callback(selection.url);
      }
    });

    frame.open();
  }


  /**
   * Toggle manage options
   */
  function toggleManage(option) {
    // Get new answer class
    var toggle = 'answer--' + option.data('manage');

    box.find('.answer').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Toggle format options
   */
  function toggleFormat(option) {
    $.each(['binary', 'points', 'category'], function(i, v) {
      // Remove answers modificators
      box.find('.answer').removeClass(
          'answer--' + v
      );

      // Remove result modificators
      box.find('.result').removeClass(
          'result--' + v
      );
    });

    // Set new format modificators
    box.find('.answer').addClass(
      'answer--' + option.data('format')
    );

    box.find('.result').addClass(
      'result--' + option.data('format')
    );

    // Toggle achievment
    toggleAchievment(option.data('format'));
  }


  /**
   * Toggle summary options
   */
  function toggleSummary(option) {
    var toggle = 'result--' + option.data('summary');

    box.find('.result').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Toggle achievment option
   */
  function toggleAchievment(format) {
    var toggle = box.find('.summary [data-summary="achievment"]');

    toggle.prop('disabled', false);

    if(format === 'category') {
      toggle.prop('checked', false);
      toggle.prop('disabled', true);
    }

    return toggle.change();
  }


  /**
   * Toggle details options
   */
  function toggleDetails(option) {
    box.find('.result').toggleClass('result--details',
      option.data('details') === 'result' && option.is(':checked')
    );

    box.find('.remark').toggleClass('remark--details',
      option.data('details') === 'remark' && option.is(':checked')
    );
  }


  /**
   * Manage checkbox answer trigger
   */
  box.on('change', '.manage input[data-manage]', function() {
    var option = $(this);

    return toggleManage(option);
  });


  /**
   * Format radio trigger
   */
  box.on('change', '.manage input[data-format]', function() {
    var option = $(this);

    return toggleFormat(option);
  });


  /**
   * Summary checkbox trigger
   */
  box.on('change', '.summary input[data-summary]', function() {
    var option = $(this);

    return toggleSummary(option);
  });


  /**
   * Details radio trigger
   */
  box.on('change', '.summary input[data-details]', function() {
    var option = $(this);

    return toggleDetails(option);
  });


  /**
   * Update results category list on answer change
   */
  box.on('change', '.answer input[data-answer]', function() {
    var option = $(this);

    if(option.data('answer') === 'category') {
      var answer = option.val();

      box.find('.result .result__category-field').each(function(i, select) {
        if($(select).find('option[value=' + answer + ']').length === 0) {
          $(select).append('<option value="' + answer + '">' + answer + '</option>');
        }
      });
    }
  });


  /**
   * Add new question
   */
  box.on('click', '.action--item', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    // Insert after last item
    box.find('.item:last').after(item);

    sortQuestions(function() {
      updateEditor(item);
    });
  });


  /**
   * Swap questions
   */
  box.on('click', '.item__change', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Don't swap first element
    if(item.index() === 1) {
      return false;
    }

    var prev = item.prev().insertAfter(item);

    sortQuestions(function() {
      // Sort answers
      box.find('.item:not(:first)').each(function(i) {
        sortAnswers($(this));
      });

      // Update item editor
      updateEditor(item);
    });
  });


  /**
   * Remove question
   */
  box.on('click', '.item__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    sortQuestions(function() {
      // Sort answers
      box.find('.item:not(:first)').each(function(i) {
        sortAnswers($(this));
      });

      // Update item editor
      updateEditor(item);
    });

    item.remove();
  });


  /**
   * Add answer to question
   */
  box.on('click', '.item__manage-add', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Clone fake answer
    var answer = item.find('.answer:first').clone();

    // Insert after last answer
    item.find('.answer:last').after(answer);

    // Show answers button
    item.find('.item__manage-toggle').attr('disabled', false);

    if(!item.find('.item__answers').hasClass('item__answers--expand')) {
      item.find('.item__manage-toggle').trigger('click');
    }

    sortAnswers(item);
  });


  /**
   * Remove answer
   */
  box.on('click', '.answer__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Remove answer
    $(this).closest('.answer').remove();

    // Toggle answers button
    item.find('.item__manage-toggle').attr('disabled',
      item.find('.answer').length <= 1
    );

    sortAnswers(item);
  });


  /**
   * Toggle answers
   */
  box.on('click', '.item__manage-toggle', function(e) {
    e.preventDefault();

    var button = $(this);

    // Swap title with data attr
    var title = button.text();
    button.text(button.data('toggle'));
    button.data('toggle', title);

    // Find closest answers
    var answers = button.closest('.item').find('.item__answers');
    answers.toggleClass('item__answers--expand');

    // Animate scrolling to item
    $('html, body').animate({
      scrollTop: button.closest('.item').offset().top
    }, 200);
  });


  /**
   * Choose answer image
   */
  box.on('click', '.answer__poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    appendImage(poster, '.answer__poster-attachment', 'short');
  });


  /**
   * Add new result
   */
  box.on('click', '.action--result', function(e) {
    e.preventDefault();

    var result = box.find('.result:first').clone();

    // Insert after last item
    box.find('.result:last').after(result);

    sortResults(function() {
      // Sort posters
      box.find('.result:not(:first)').each(function(i) {
        sortPosters($(this));
      });

      // Update item editor
      updateEditor(result);
    });
  });


  /**
   * Remove result
   */
  box.on('click', '.result__delete', function(e) {
    e.preventDefault();

    var result = $(this).closest('.result');

    sortResults(function() {
      // Sort posters
      box.find('.result:not(:first)').each(function(i) {
        sortPosters($(this));
      });

      // Update item editor
      updateEditor(result);
    });

    result.remove();
  });


  /**
   * Choose result poster
   */
  box.on('click', '.result__image-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    appendImage(poster, '.result__image-attachment', false, function(url) {
      var result = poster.closest('.result');

      result.find('input[data-poster]').each(function() {
        $(this).attr('value', url);
      });
    });
  });


  /**
   * Generate button click
   */
  box.on('click', '.result__image-generate', function(e) {
    e.preventDefault();

    var points = {};

    // Select closest result
    var result = $(this).closest('.result');

    $.each(['from', 'to'], function(i, v) {
      points[v] = parseInt(result.find('[data-result="' + v + '"]').val());
    });

    // Get scores fields
    var fields = result.find('.result__scores-field');

    if(points.from > points.to) {
      return blinkClass(fields, 'result__scores-field--error')
    }

    var caption = result.find('.result__image-caption');

    // Get attachment value
    var attachment = result.find('.result__image-attachment').val();

    if(attachment.length === 0) {
      return blinkClass(caption, 'result__image-caption--error');
    }

    createPosters(result, points);
  });


  /**
   * Show posters fields
   */
  box.on('click', '.result__image-manual', function(e) {
    e.preventDefault();

    var points = {};

    // Select closest result
    var result = $(this).closest('.result');

    $.each(['from', 'to'], function(i, v) {
      points[v] = parseInt(result.find('[data-result="' + v + '"]').val());
    });

    // Get scores fields
    var fields = result.find('.result__scores-field');

    if(points.from > points.to) {
      return blinkClass(fields, 'result__scores-field--error')
    }

    // Toggle posters class
    result.toggleClass('result--posters');

    if(result.hasClass('result--posters')) {
      updatePosters(result, points);
    }
  });


  /**
   * Show posters fields
   */
  box.on('change', '.result__scores input', function(e) {
    if($(this).val().length === 0) {
      return false;
    }

    var points = {};

    // Select closest result
    var result = $(this).closest('.result');

    $.each(['from', 'to'], function(i, v) {
      points[v] = parseInt(result.find('[data-result="' + v + '"]').val());
    });

    if(points.to - points.from > 15) {
      return blinkClass($(this), 'result__scores-field--error')
    }

    if(points.from > points.to) {
      return blinkClass($(this), 'result__scores-field--error')
    }

    updatePosters(result, points);
  });


  /**
   * Display poster field
   */
  box.on('click', '.poster__display', function(e) {
    e.preventDefault();

    var field = $(this).siblings('.poster__field');

    if(field.val().length === 0) {
      return blinkClass(field, 'poster__field--error');
    }

    var poster = $(this).closest('.result').find('.result__image-poster');

    // Create image if not exists
    if(poster.find('img').length === 0) {
      $('<img />').prependTo(poster);
    }

    var image = poster.find('img');

    image.load(function() {
      image.fadeIn(100);
    });

    image.attr('src', field.val()).hide();
  });


  /**
   * Onload set up
   */
  (function() {
    // Sort questions and update editor
    sortQuestions(function() {
      box.find('.item:not(:first)').each(function(i) {
        var item = $(this);

        // Sort answers
        sortAnswers(item);

        // Update editor
        updateEditor(item);

        // Toggle answers button
        item.find('.item__manage-toggle').attr('disabled',
          item.find('.answer').length <= 1
        );
      });
    });


    // Sort results and update editor
    sortResults(function() {
      box.find('.result:not(:first)').each(function(i) {
        var result = $(this);

        // Sort posters
        sortPosters(result);

        // Update editor
        updateEditor(result);
      });
    });


    // Set manage classes
    box.find('.manage input[data-manage]').each(function() {
      var option = $(this);

      toggleManage(option);
    });

    // Set format classes
    box.find('.manage input[data-format]:checked').each(function() {
      var option = $(this);

      toggleFormat(option);
    });

    // Set summary classes
    box.find('.summary input[data-summary]').each(function() {
      var option = $(this);

      toggleSummary(option);
    });

    // Set details classes
    box.find('.summary input[data-details]:checked').each(function() {
      var option = $(this);

      toggleDetails(option);
    });


    // Init remark editor
    updateEditor(box.find('.remark'));

    // Show items
    box.find('.box--items').addClass('box--expand');

    // Show results
    box.find('.box--results').addClass('box--expand');
  })();
});
