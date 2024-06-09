/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Fetches the NodeJS status and version information from the server.
 * Sends a POST request to the AJAX_URL with the 'proc' parameter set to 'nodejs'.
 * Updates the DOM with the received status and version information.
 *
 * @async
 * @function getNodeJSStatus
 * @returns {Promise<void>}
 */
async function getNodeJSStatus() {
  const url = AJAX_URL;
  const proc = 'nodejs';
  const senddata = new URLSearchParams();
  senddata.append(`proc`, proc);
  const options = {
    method: 'POST',
    body: senddata
  }
  let response = await fetch(url, options);
  if (!response.ok) {
    console.log('Error receiving from ajax.php');
  } else {
    let myajaxresponse = await response.text();
    let data;
    try {
      data = JSON.parse(myajaxresponse);
    } catch (error) {
      console.error('Failed to parse response:', error);
    }

    let q = document.querySelector('.nodejs-status');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.status);

    q = document.querySelector('.nodejs-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

/**
 * Event listener for the DOMContentLoaded event.
 * Checks if an anchor element with the name 'nodejs' exists.
 * If it exists, calls the getNodeJSStatus function to fetch and display NodeJS status and version information.
 *
 * @event DOMContentLoaded
 */
document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=nodejs]').name === 'nodejs') {
    getNodeJSStatus();
  }
});
