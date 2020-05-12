/**
 * Quiz post type front-end handler
 *
 * @since 1.7
 * @version 1.13
 */

(function() {
  var quiz = document.getElementById('quiz');


  /**
   * Check if quiz object exists
   */
  if(quiz === null || typeof knife_quiz_options === 'undefined') {
    return false;
  }


  /**
   * Button element
   */
  var button = null;


  /**
   * Currently showing item index
   */
  var progress = 0;


  /**
   * Hold victorious advance in this var
   */
  var advance = 0;


  /**
   * Store current scores progress
   */
  var ranking = {};


  /**
   * Store poster name code
   */
  var dynamic = '';


  /**
   * Smooth scroll
   */
  function smoothScroll(to) {
    if('scrollBehavior' in document.documentElement.style) {
      return window.scrollTo({top: to, behavior: 'smooth'});
    }

    window.scrollTo(to, 0);
  }


  /**
   * Replace share links
   */
  function replaceShare(result, dynamic) {
    // Check quiz share links
    if(typeof knife_quiz_options.share_links === 'undefined') {
      return false;
    }

    // Check quiz permalink
    if(typeof knife_quiz_options.permalink === 'undefined') {
      return false;
    }

    // Skip replacement if option set
    if(knife_quiz_options.hasOwnProperty('noshare') && knife_quiz_options.noshare) {
      return false;
    }

    var permalink = knife_quiz_options.permalink.replace(/\/?$/, '/') + result.index + '/';

    if(dynamic.length > 0) {
      permalink = permalink + dynamic + '/';
    }

    var title = quiz.querySelector('.entry-quiz__title').textContent || '';

    // Create substitution array
    var matches = [permalink, title];

    // Get all links by class
    var links = quiz.querySelectorAll('.share > .share__link');

    for(var i = 0, link; link = links[i]; i++) {
      var label = link.getAttribute('data-label');

      if(typeof knife_quiz_options.share_links[label] === 'undefined') {
        continue;
      }

      var options = knife_quiz_options.share_links[label];

      link.href = options.link.replace(/%([\d])\$s/g, function(match, i) {
        return encodeURIComponent(matches[i - 1]);
      });
    }

    if(typeof window.shareButtons === 'function') {
      window.shareButtons();
    }
  }


  /**
   * Click listener for answers
   */
  function addAnswerListener(answers, vote, cl) {
    if(!knife_quiz_options.hasOwnProperty('format')) {
      knife_quiz_options.format = 'binary';
    }

    // Click trigger
    vote.addEventListener('click', function(e) {
      var target = e.target || e.srcElement;

      if(!target.hasAttribute('data-answer')) {
        return;
      }

      if(vote.classList.contains('entry-quiz__vote--complete')) {
        return;
      }

      var answer = answers[target.dataset.answer];
      vote.classList.add('entry-quiz__vote--complete');

      switch(knife_quiz_options.format) {
        case 'dynamic':
          if(answer.hasOwnProperty('dynamic')) {
            dynamic = dynamic + answer.dynamic.toString();
          }

          target.classList.add(cl + '--selected');
          break;

        case 'category':
          if(answer.hasOwnProperty('category')) {
            var score = 0;

            if(ranking.hasOwnProperty(answer.category)) {
              score = ranking[answer.category];
            }

            ranking[answer.category] = score + 1;

            // Assign to advance currently max key
            advance = Object.keys(ranking).reduce(function(a, b) {
              return ranking[a] > ranking[b] ? a : b
            });
          }

          target.classList.add(cl + '--selected');
          break;

        case 'points':
          if(answer.hasOwnProperty('points') && answer.points) {
            advance = advance + parseInt(answer.points)
          }

          target.classList.add(cl + '--selected');
          break;

        case 'binary':
          if(answer.hasOwnProperty('binary') && answer.binary) {
            advance = advance + 1;

            target.classList.add(cl + '--correct');
            break;
          }

          // Loop through answers to find and mark correct
          for(var i = 0; i < answers.length; i++) {
            var sibling = target.parentNode.children[i];

            if(answers[i].hasOwnProperty('binary') && answers[i].binary) {
              sibling.classList.add(cl + '--missed');
            }
          }

          target.classList.add(cl + '--wrong');
          break;
      }

      // Show message field if exists or trigger button click
      if(knife_quiz_options.hasOwnProperty('message') && knife_quiz_options.message) {
        var message = document.createElement('div');
        message.classList.add('entry-quiz__vote-message');

        if(answer.message) {
          message.innerHTML = answer.message;
          return vote.appendChild(message);
        }
      }

      return button.click();
    });
  }


  /**
   * Show choice answers
   */
  function showAnswersChoice(answers, vote) {
    // Append answers choice to vote
    for(var i = 0; i < answers.length; i++) {
      var choice = document.createElement('div');

      if(typeof answers[i].choice !== 'undefined') {
        choice.classList.add('entry-quiz__vote-choice');
        choice.setAttribute('data-answer', i);
        choice.innerHTML = answers[i].choice;

        vote.appendChild(choice);
      }
    }

    // Add answer click listener
    addAnswerListener(answers, vote, 'entry-quiz__vote-choice');

    vote.classList.add('entry-quiz__vote--choice');
  }


  /**
   * Show choice answers
   */
  function showAnswersAttachment(answers, vote) {
    var grid = document.createElement('div');
    grid.classList.add('entry-quiz__vote-grid');
    vote.appendChild(grid);

    // Append answers images to vote
    for(var i = 0; i < answers.length; i++) {
      var attachment = document.createElement('figure');

      if(typeof answers[i].attachment !== 'undefined') {
        var image = document.createElement('img');
        image.setAttribute('src', answers[i].attachment);

        attachment.appendChild(image);
        attachment.classList.add('entry-quiz__vote-attachment');
        attachment.setAttribute('data-answer', i);

        grid.appendChild(attachment);
      }
    }

    // Add answer click listener
    addAnswerListener(answers, vote, 'entry-quiz__vote-attachment');

    vote.classList.add('entry-quiz__vote--attachment');
  }


  /**
   * Show answers
   */
  function showAnswers(answers, position) {
    var vote = document.createElement('div');
    vote.classList.add('entry-quiz__vote');

    quiz.insertBefore(vote, position);

    // Shuffle answers if need
    if(knife_quiz_options.hasOwnProperty('shuffle') && knife_quiz_options.shuffle) {
      answers.sort(function() {
        return 0.5 - Math.random()
      });
    }

    // Decide what to show as answers accoring option
    if(knife_quiz_options.hasOwnProperty('attachment') && knife_quiz_options.attachment) {
      return showAnswersAttachment(answers, vote);
    }

    return showAnswersChoice(answers, vote);
  }


  /**
   * Show quiz item by index
   */
  function showItem(item, index, total) {
    // Get quiz offset
    var offset = quiz.getBoundingClientRect().top + window.pageYOffset;

    // Try to scroll smoothly
    smoothScroll(offset - 76);

    // Set quiz content
    var content = quiz.querySelector('.entry-quiz__content');
    if(item.hasOwnProperty('question')) {
      content.innerHTML = item.question;
    }

    var info = quiz.querySelector('.entry-quiz__info');
    info.textContent = index + ' / ' + total;

    var vote = quiz.querySelector('.entry-quiz__vote');
    if(document.body.contains(vote)) {
      vote.parentNode.removeChild(vote);
    }

    if(typeof item.answers === 'object' && item.answers.length > 0) {
      showAnswers(item.answers, content.nextSibling);
    }
  }


  /**
   * Show results
   */
  function showResult(result) {
    // Get quiz offset
    var offset = quiz.getBoundingClientRect().top + window.pageYOffset;

    // Try to scroll smoothy
    smoothScroll(offset - 76);

    // Set quiz results
    if(result.hasOwnProperty('poster')) {
      var poster = document.createElement('img');

      // Update poster if dynamic
      if(dynamic.length > 0) {
        result.poster = result.poster.replace('*', dynamic);
      }

      poster.classList.add('entry-quiz__poster');
      poster.setAttribute('src', result.poster);

      quiz.insertBefore(poster, quiz.firstChild);
    }

    var share = quiz.querySelector('.entry-quiz__share');
    if(document.body.contains(share)) {
      replaceShare(result, dynamic);
    }

    var vote = quiz.querySelector('.entry-quiz__vote');
    if(document.body.contains(vote)) {
      vote.parentNode.removeChild(vote);
    }

    var content = quiz.querySelector('.entry-quiz__content');
    if(result.hasOwnProperty('details')) {
      return content.innerHTML = result.details;
    }

    return content.parentNode.removeChild(content);
  }


  /**
   * Set quiz predefined options
   */
  (function() {
    if(knife_quiz_options.hasOwnProperty('center') && knife_quiz_options.center) {
      quiz.classList.add('entry-quiz--center');
    }
  })();


  /**
   * Create quiz button
   */
  (function() {
    if(knife_quiz_options.hasOwnProperty('button_start')) {
      button = document.createElement('button');

      button.classList.add('entry-quiz__button', 'button');
      button.setAttribute('type', 'button');

      button.textContent = knife_quiz_options.button_start;
    }

    quiz.appendChild(button);
  })();


  /**
   * Prepare items if exist
   */
  (function() {
    if(typeof knife_quiz_items !== 'object') {
      knife_quiz_items = [];
    }

    if(knife_quiz_options.hasOwnProperty('random') && knife_quiz_options.random) {
      knife_quiz_items.sort(function() {
        return 0.5 - Math.random()
      });
    }
  })();


  /**
   * Prepare results
   */
  (function() {
    if(typeof knife_quiz_results !== 'object') {
      knife_quiz_results = {};
    }
  })();


  /**
   * Route button clicks
   */
  button.addEventListener('click', function() {
    var total = knife_quiz_items.length;

    // If we still can show items, show one
    if(total > 0 && progress < total) {
      var item = knife_quiz_items[progress];

      progress = progress + 1;

      if(quiz.classList.contains('entry-quiz--item') === false) {
        quiz.classList.add('entry-quiz--item');

        if(knife_quiz_options.hasOwnProperty('button_next')) {
          button.textContent = knife_quiz_options.button_next;
        }
      }

      if(progress === total && knife_quiz_options.hasOwnProperty('button_last')) {
        button.textContent = knife_quiz_options.button_last;
      }

      return showItem(item, progress, total);
    }


    if(quiz.classList.contains('entry-quiz--results') === false) {
      var results = [];

      // Try to find correct results
      for(var i = 0; i < knife_quiz_results.length; i++) {
        var result = knife_quiz_results[i];

        // Save current index
        result.index = i;

        if(!result.hasOwnProperty('advance') || result.advance === advance) {
          results.push(result);
        }
      }

      // Check if results exist
      if(results.length > 0) {
        var random = results[Math.floor(Math.random() * results.length)];

        quiz.classList.remove('entry-quiz--item');

        if(knife_quiz_options.hasOwnProperty('button_repeat')) {
          button.textContent = knife_quiz_options.button_repeat;
        }

        if(knife_quiz_options.hasOwnProperty('norepeat') && knife_quiz_options.norepeat) {
          button.parentNode.removeChild(button);
        }

        quiz.classList.add('entry-quiz--results');

        return showResult(random);
      }
    }

    return document.location.reload();
  });
})();
