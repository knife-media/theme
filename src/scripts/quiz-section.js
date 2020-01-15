/**
 * Quiz post type front-end handler
 *
 * @since 1.7
 * @version 1.11
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
   * Hold victorious result in this var
   */
  var hold = 0;


  /**
   * Store current scores progress
   */
  var ranking = {};


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
  function replaceShare(result, index) {
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

    var matches = [
      knife_quiz_options.permalink.replace(/\/?$/, '/') + index + '/',
      quiz.querySelector('.entry-quiz__title').textContent || ''
    ];

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
        case 'category':
          if(answer.hasOwnProperty('category') && answer.category) {
            var score = 0;

            if(ranking.hasOwnProperty(answer.category)) {
              score = ranking[answer.category];
            }

            ranking[answer.category] = score + 1;

            // Assign to hold currently max key
            hold = Object.keys(ranking).reduce(function(a, b) {
              return ranking[a] > ranking[b] ? a : b
            });
          }

          target.classList.add(cl + '--selected');
          break;

        case 'points':
          if(answer.hasOwnProperty('points') && answer.points) {
            hold = hold + parseInt(answer.points)
          }

          target.classList.add(cl + '--selected');
          break;

        case 'binary':
          if(answer.hasOwnProperty('binary') && answer.binary) {
            hold = hold + 1;

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
  function showResult(result, hold) {
    // Get quiz offset
    var offset = quiz.getBoundingClientRect().top + window.pageYOffset;

    // Try to scroll smoothy
    smoothScroll(offset - 76);

    // Set quiz results
    if(result.hasOwnProperty('poster')) {
      var poster = document.createElement('img');

      poster.classList.add('entry-quiz__poster');
      poster.setAttribute('src', result.poster);

      quiz.insertBefore(poster, quiz.firstChild);
    }

    var share = quiz.querySelector('.entry-quiz__share');
    if(document.body.contains(share)) {
      replaceShare(result, hold);
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
      // Check if result exists
      if(typeof knife_quiz_results[hold] === 'object') {
        quiz.classList.remove('entry-quiz--item');

        if(knife_quiz_options.hasOwnProperty('button_repeat')) {
          button.textContent = knife_quiz_options.button_repeat;
        }

        if(knife_quiz_options.hasOwnProperty('norepeat') && knife_quiz_options.norepeat) {
          button.parentNode.removeChild(button);
        }

        quiz.classList.add('entry-quiz--results');

        return showResult(knife_quiz_results[hold], hold);
      }
    }

    return document.location.reload();
  });
})();
