/*$(document).ready(function() {
  if ($('a[name=php]').length) {
    $.ajax({
      data: {
        proc: 'php'
      },
      success: function(data) {
        $('.php-status').append(data.status);
        $('.php-status').find('.loader').remove();

        $('.php-version-list').append(data.versions);
        $('.php-version-list').find('.loader').remove();

        $('.php-extscount').append(data.extscount);
        $('.php-extscount').find('.loader').remove();

        $('.php-pearversion').append(data.pearversion);
        $('.php-pearversion').find('.loader').remove();

        $('.php-extslist').append(data.extslist);
        $('.php-extslist').find('.loader').remove();
      }
    });
  }
});*/

async function getPHPStatus() {
  let url = '/b30a9b2155cf4012e52675f2d0559415/ajax.php';
  let data = new URLSearchParams();
  let proc='php';
  data.append(`proc`, proc);
  const options = {
    method: 'POST',
    body: data
  }
  let response = await fetch(url, options);
  if (!response.ok) {
    console.log('Error receiving from ajax.php');
  } else {
    let myajaxresponse = await response.text();
    let data = JSON.parse(myajaxresponse);

    let q = document.querySelector('.php-status');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.status);

    q = document.querySelector('.php-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.versions);

    q = document.querySelector('.php-extscount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.extscount);

    q = document.querySelector('.php-pearversion');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.pearversion);

    q = document.querySelector('.php-extslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.extslist);
  }
}
document.addEventListener("DOMContentLoaded", function() {
  if (document.querySelector('a[name=php]').name = 'php') {
    getPHPStatus();
  }
})
