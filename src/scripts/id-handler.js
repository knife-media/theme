/**
 * Comments and profiles handler
 *
 * @since 1.13
 */

(function () {
  const comments = document.getElementById('comments');

  if (comments === null) {
    return false;
  }


  // Draw comment function
  const drawComment = (item) => {
    console.log(item)
  }


  // Show comments
  const showComments = (fields) => {
    console.log(fields);
  }


  /**
   * Make AJAX request
   */
  const makeRequest = (url, method, callback) => {
    console.log(method);
    const request = new XMLHttpRequest();
    request.open(method, url, true);
    request.setRequestHeader("Content-Type", "application/json");

    request.onload = () => {
      if (request.status === 200) {
        return callback(JSON.parse(request.responseText));
      }

      console.error(`Error while ${url} loading`);
    }

    request.send();
  }

  /**
   * Load comments
   */
  makeRequest('/id/comments?post=91039', 'GET', (response) => {
    let items = [];

    // Get fields from response
    const fields = response.fields || [];

    // Sort comments
    fields.forEach(item => {
      if (item.parent !== null) {
        let parent = items.find(v => v.id === item.parent);

        if (parent.id) {
          //items.
        }
      }

      items.push(item);
    });

    //showComments(fields);
  });


})();