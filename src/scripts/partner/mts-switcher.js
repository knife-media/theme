(function() {
  let switcher = document.createElement('div');
  switcher.id = 'tumbler'

  document.body.appendChild(switcher);

  /*
window['Ya'].adfoxCode.create({
        params: {
            pp: 'g',
            ps: 'ehgv',
            p2: 'gxnl',
            puid1: '',
            puid2: '',
            puid3: '',
            puid4: '',
            puid5: '',
            puid6: '',
            puid7: ''
        }
    });
    */

  let options =  {
    ownerId: 265942,
    containerId: 'tumbler',
    isTurbo: false,
    params: {
      pp: 'g',
      ps: 'ehgv',
      p2: 'gxnl'
    }
  };

  options.onLoad = function (handle) {
    console.log(handle);
  }


  window['adfoxAsyncParams'] = window['adfoxAsyncParams'] || [];
  window['adfoxAsyncParams'].push(options);


  console.log('mts-tumblr');
})();
