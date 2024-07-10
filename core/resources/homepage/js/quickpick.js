/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

document.getElementById('moduleDropdown').addEventListener('change', async function () {
    const selectedModule = this.value;
    if (!selectedModule) return;

    const url = AJAX_URL; // Use the dynamically set AJAX URL
    const senddata = new URLSearchParams();
    senddata.append('module', selectedModule);

    const options = {
        method: 'POST',
        body: senddata
    };

    try {
        let response = await fetch(url, options);
        console.log(response.status, response.statusText); // Log the response status and status text
        if (!response.ok) {
            let errorText = await response.text();
            console.error('Error response:', errorText); // Log the error response text
            throw new Error('Network response was not ok');
        }
        let responseData = await response.json();
        console.log('Response Data:', responseData);

        // Log the content to the console
        console.log(responseData.content);

        // Process the versions
        const versions = responseData.versions[selectedModule];

        // Clear previous versions
        const moduleVersions = document.getElementById('moduleVersions');
        moduleVersions.innerHTML = '';
        // Add new versions (example)
        versions.forEach(version => {
            const option = document.createElement('option');
            option.value = version;
            option.textContent = version;
            moduleVersions.appendChild(option);
        });

        if (versions.length > 0) {
            const ul = document.createElement('ul');
            ul.classList.add('list-group');
            versions.forEach(version => {
                const li = document.createElement('li');
                li.classList.add('list-group-item');
                li.textContent = version;

                // Create submenu for each version
                const submenu = document.createElement('div');
                submenu.classList.add('submenu');
                submenu.style.display = 'none';
                submenu.textContent = `Install ${version}`;
                submenu.addEventListener('click', async function () {
                    await installModule(selectedModule, version);
                });

                li.appendChild(submenu);
                li.addEventListener('click', function () {
                    submenu.style.display = 'block'; // Show the submenu
                });

                ul.appendChild(li);
            });
            moduleVersions.appendChild(ul);
        } else {
            moduleVersions.textContent = 'No versions available';
        }
    } catch (error) {
        console.error('Failed to fetch module versions:', error);
    }
});
