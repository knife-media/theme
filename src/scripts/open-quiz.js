/**
 * Quiz post type front-end handler
 *
 * @since 1.7
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
   * Calculate scores to this var
   */
  var scores = 0;


  /**
   * Show choice answers
   */
  function showChoiceAnswers(answers, vote) {
    var message = null;

    // Create message field if need
    if(knife_quiz_options.hasOwnProperty('message') && knife_quiz_options.message) {
      message = document.createElement('div');
      message.classList.add('entry-quiz__vote-message');
    }

    // Choice click trigger
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

      // Show message if exists
      if(message && answer.hasOwnProperty('message') && answer.message) {
        message.innerHTML = answer.message;
        vote.appendChild(message);
      }

      // Calculate points and return
      if(knife_quiz_options.hasOwnProperty('points') && knife_quiz_options.points) {
        if(answer.hasOwnProperty('points') && answer.points) {
          scores = scores + parseInt(answer.points)
        }

        return target.classList.add('entry-quiz__vote-choice--selected');
      }

      // If user made right decision
      if(answer.hasOwnProperty('binary') && answer.binary) {
        scores = scores + 1;

        return target.classList.add('entry-quiz__vote-choice--correct');
      }

      // Loop through answers to find and mark correct
      for(var i = 0; i < answers.length; i++) {
        if(answers[i].hasOwnProperty('binary') && answers[i].binary) {
          vote.children[i].classList.add('entry-quiz__vote-choice--missed');
        }
      }

      return target.classList.add('entry-quiz__vote-choice--wrong');
    });


    // Append answers choice to vote
    for(var i = 0; i < answers.length; i++) {
      var choice = document.createElement('div');

      choice.classList.add('entry-quiz__vote-choice');
      choice.setAttribute('data-answer', i);
      choice.innerHTML = answers[i].choice;

      vote.appendChild(choice);
    }
  }


  /**
   * Show choice answers
   */
  function showAttachmentAnswers(answers, vote) {
  }


  /**
   * Show answers
   */
  function showAnswers(answers, position) {
    var vote = document.createElement('div');
    vote.classList.add('entry-quiz__vote');

    quiz.insertBefore(vote, position);

    // Clear all tags inside vote
    while (vote.firstChild) {
      vote.removeChild(vote.firstChild);
    }

    // Shuffle answers if need
    if(knife_quiz_options.hasOwnProperty('shuffle') && knife_quiz_options.shuffle) {
      answers.sort(function() {
        return 0.5 - Math.random()
      });
    }

    // Decide what to show as answers accoring option
    if(knife_quiz_options.hasOwnProperty('attachment') && knife_quiz_options.attachment) {
      return showAttachmentAnswers(answers, vote);
    }

    return showChoiceAnswers(answers, vote);
  }


  /**
   * Show quiz item by index
   */
  function showItem(item, index, total) {
    var content = quiz.querySelector('.entry-quiz__content');
    if(content && item.hasOwnProperty('question')) {
      content.innerHTML = item.question;
    }

    var info = quiz.querySelector('.entry-quiz__info');
    if(info && index > 0) {
      info.textContent = index + ' / ' + total;
    }

    var vote = quiz.querySelector('.entry-quiz__vote');
    if(vote && vote.parentNode) {
      vote.parentNode.removeChild(vote);
    }

    if(typeof item.answers === 'object' && item.answers.length > 0) {
      showAnswers(item.answers, content.nextSibling);
    }

    if(quiz.classList.contains('entry-quiz--item') === false) {
      quiz.classList.add('entry-quiz--item');

      if(knife_quiz_options.hasOwnProperty('button_next')) {
        button.textContent = knife_quiz_options.button_next;
      }
    }

    if(index === total) {
      if(typeof knife_quiz_results === 'undefined') {
        return button.
      }


      && knife_quiz_options.hasOwnProperty('button_last')) {
      button.textContent = knife_quiz_options.button_last;
    }
  }


  /**
   * Show results
   */
  function showResults() {
    console.log(scores);
  }


  /**
   * Create quiz button
   */
  (function() {
    button = document.createElement('button');

    button.classList.add('entry-quiz__button', 'button');
    button.setAttribute('type', 'button');

    if(knife_quiz_options.hasOwnProperty('button_start')) {
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
      knife_quiz_results = [];
    }
  })();


  /**
   * Route button clicks
   */
  button.addEventListener('click', function() {
    // Scroll to quiz parent
    quiz.parentNode.scrollIntoView({block: 'start', behavior: 'smooth'});

    var total = knife_quiz_items.length;

    if(total > 0 && progress < total) {
      var item = knife_quiz_items[progress];

      return showItem(item, ++progress, total);
    }

    return showResults();
  });
})();
