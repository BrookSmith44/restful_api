setAuthenticationToken();
// Set interval to get user list to start off with to ensure key has been set
const interval = setInterval(function() {
    // Get api key
    const apiKey = localStorage.getItem('key');
    if (apiKey !== null) {
        httpRequest('get', 'http://localhost:8080/REST_API/public/api/users/fetch', 'getUsers', '');
        clearInterval(interval);
    }
}, 10);

// Events
const options = document.getElementsByClassName('option');

// Loop though options
for (let i = 0; i < options.length; i++) {
    // Add event listeners to listen for click
    options[i].addEventListener('click', function() {
        // Check if current option has active class
        if (!options[i].classList.contains('active')) {
            // Get element that is currently active
            const currentActive = document.getElementsByClassName('active')[0];
            // Remove active class from element
            currentActive.classList.remove('active');
            // Add active class to current option
            options[i].classList.add('active');

            // get delete button
            const deleteButton = document.getElementById('button-delete');

            if (options[i].id == 'add') {
                // Call function to reset form
                resetForm();
                // Hide delete button for add form
                deleteButton.style.display = 'none';
            }
        }
    });
}

// Get submit button
const submit = document.getElementById('button-submit');

// Submit
submit.addEventListener('click', function(event) {
    // Prevent form from redirecting to action page
    event.preventDefault();

    // get form
    const requestForm = document.forms['request-form'];
    // get message element
    const message = document.getElementById('request-message'); 
    // Create validation object
    const validate = new Validate(requestForm, message);
    // Empty variable for validation
    let validated = [];
    // Check all inputs have been filled in
    validated['inputs_filled'] = validate.checkDataEntered();
    // Check phone number is in correct format
    validated['phone_number'] = validate.checkStringSize();

    if (validated['inputs_filled'] == true && validated['phone_number'] == true) {
        // create object to store data
        const user = {
            'FirstName': requestForm['fname'].value,
            'Surname': requestForm['surname'].value,
            'DateOfBirth': requestForm['dob'].value,
            'PhoneNumber': requestForm['phoneNo'].value,
            'Email': requestForm['email'].value
        };

        // Get element with active class assigned
        const active = document.getElementsByClassName('active')[0];

        // Decipher which request to send based on active element
        switch(active.id) {
            case 'add':
                // Send http request
                httpRequest('post', 'http://localhost:8080/REST_API/public/api/users/insert', 'updateUser', user);
                break;
            case 'update':
                // make sure user has been selected before sending request
                validated['userId'] = validate.userIdSet();

                // check validation came back true
                if (validated['userId'] == true) {
                    // Create url for api with user id as route parameter
                    const url = 'http://localhost:8080/REST_API/public/api/users/update/' + requestForm['userId'].value;
                    // Send http request
                    httpRequest('put', url, 'updateUser', user);
                }
                break;
        }
    }
});

// Get delete button
const deleteButton = document.getElementById('button-delete');

// Add click listener to delete button
deleteButton.addEventListener('click', function(event) {
    // Prevent form from submitting
    event.preventDefault();

    // get form
    const requestForm = document.forms['request-form'];
    // get message element
    const message = document.getElementById('request-message'); 
    // Create validation object
    const validate = new Validate(requestForm, message);
    // Empty variable for validation
    let validated = [];

     // make sure user has been selected before sending request
     validated['userId'] = validate.userIdSet();

     // check validation came back true
     if (validated['userId'] == true) {
         // Create url
        const url = 'http://localhost:8080/REST_API/public/api/users/delete/' + requestForm['userId'].value;
        // Send request
        httpRequest('delete', url, 'deleteUser', '');
     }
});

// Get search button
const searchButton = document.getElementById('button-search');

// Add click listener to search button
searchButton.addEventListener('click', function(event) {
    // Prevent form from submitting
    event.preventDefault();

    // get form
    const searchForm = document.forms['search-form'];

    const validate = new Validate(searchForm);

    const validation = validate.checkDataEntered(searchForm);

    if (validation == true) {
        // Create url
        let url = 'http://localhost:8080/REST_API/public/api/users/fetch';

        // Make sure search is not empty before sending request with paramerter
        if (searchForm['search'].value != '') {
            // Add route parameter if search is not empty
            url += '/' + searchForm['search'].value;
        }

        httpRequest('get', url, 'getUsers', '');
    }
});

// Functions
function httpRequest(method, url, operation, data) {

    // Create XHR request Object
    const xhr = new XMLHttpRequest();

    // get api key
    const apiKey = localStorage.getItem('key'); 
    
    // Open request
    xhr.open(method, url, true);

    // Set headers
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('Authorization', apiKey);

    // Function to handle the http response
    xhr.onload = function() {
        switch (this.status) {
            case 200:
                // Reset form
                resetForm();
                // Call function to handle ok response
                status200Response(operation, this);
                break;
            case 201:
                // Reset form
                resetForm();
                // Update user table if user has been added or updated in the system
                httpRequest('get', 'http://localhost:8080/REST_API/public/api/users/fetch', 'getUsers', '');
                // Get message element
                const requestElement = document.getElementById('request-message');
                // decode message
                const requestMessage = JSON.parse(this.responseText);
                // Create successful message
                displayMessage('success', requestElement, requestMessage);
                break;
            case 401:
            case 409:
            case 500:
                // Get message element
                const authElement = document.getElementById('user-message');
                // decode message
                const authMessage = JSON.parse(this.responseText);
                // Create successful message
                displayMessage('error', authElement, authMessage);
                break;
        }
    }

    // Send request
    xhr.send(JSON.stringify(data));
}

function status200Response(operation, response) {
    // Set variable for decoded message
    let json_decode;
    // Make sure there is a response before attempting to decode
    if (response.responseText != '') {
        // Decode json
        json_decode = JSON.parse(response.responseText);
    }

    // Switch case to decide what to do with response
    switch (operation) {
        // For request to get api key
        case 'getKey':
            // Store in localstorage
            localStorage.setItem('key', json_decode);
            break;
        case'getUsers':
            // Call function to add table data
            createTable(json_decode);
            // Get message element
            const userElement = document.getElementById('user-message');
            // Check if decoded message is array
            if (!Array.isArray(json_decode)) {
                // Create error message
                displayMessage('error', userElement, json_decode);
            } else {
                userElement.style.display = 'none';
            }
            break;
        case 'deleteUser':
            // Update user table if user has been added or updated in the system
            httpRequest('get', 'http://localhost:8080/REST_API/public/api/users/fetch', 'getUsers', '');
            // Get message element
            const element = document.getElementById('request-message');
            // Create successful message
            displayMessage('success', element, json_decode);
            break;
    }
}

// Function to create user table
function createTable(users) {
    // get table
    const table = document.getElementById('user-table');

    // Clear table to avoid duplicate records
    clearTable();

    // Make sure array has been given
    if (Array.isArray(users)) {
        // Loop through users
        for (let i = 0; i < users.length; i++) {
            // Create new row
            const row = table.insertRow(i+1);

            // add row
            row.classList.add('row');

            // Create cells for each header
            let id = row.insertCell(0);
            let fname = row.insertCell(1);
            let surname = row.insertCell(2);
            let dob = row.insertCell(3);
            let phoneNo = row.insertCell(4);
            let email = row.insertCell(5);

            // Insert data into cell
            id.innerHTML = users[i].user_id;
            fname.innerHTML = users[i].Firstname;
            surname.innerHTML = users[i].Surname;
            dob.innerHTML = users[i].DateOfBirth;
            phoneNo.innerHTML = users[i].PhoneNumber;
            email.innerHTML = users[i].Email;

            // Create event listeners for rows
            row.addEventListener('click', function() {
                // Set user attributes in form
                updateForm(users[i]);

                // Display delete button
                // get delete button
                const deleteButton = document.getElementById('button-delete');

                deleteButton.style.display = 'block';
            });
        }
    }
}

// function to clear table
function clearTable() {
    // get table rows
    const rows = document.getElementsByClassName('row');
    length = rows.length;

    // Loop through rolls
    for (let i = length - 1; i >= 0; i--) {
        rows[i].remove();
    }
}

// Function to reset form
function resetForm() {
    // get form
    const form = document.forms['request-form'];

    // reset form
    form.reset();

    // reset user id 
    form['userId'].value = '';
}

// function to fill in update form
function updateForm(user) {
    // Get update user option
    const updateOption = document.getElementById('update');

    // Check if option contains active class - if not set active class
    if (!updateOption.classList.contains('active')) {
        // Get add option
        const addOption = document.getElementById('add');

        // remove active class
        addOption.classList.remove('active');

        // Set update option to active
        updateOption.classList.add('active');
    }

    // get form
    const form = document.forms['request-form'];

    // Set attributes
    form['userId'].value = user.user_id;
    form['fname'].value = user.Firstname;
    form['surname'].value = user.Surname;
    form['dob'].value = user.DateOfBirth;
    form['phoneNo'].value = user.PhoneNumber;
    form['email'].value = user.Email;
}

// Function to display message recieved from JSON
function displayMessage(type, element, message) {
    // Show element
    element.style.display = 'flex';

    // Set message text
    element.innerHTML = message;
    
    // Swtich between types of message
    switch(type) {
        case 'success':
            // remove error class if it is on
            if (element.classList.contains('error')) {
                // remove error class
                element.classList.remove('error');
            }

            // add success class
            element.classList.add('success');
            break;
        case 'error':
            // remove error class if it is on
            if (element.classList.contains('success')) {
                // remove error class
                element.classList.remove('success');
            }

            // add success class
            element.classList.add('error');
            break;
    }
}

// Function to set authentication token
function setAuthenticationToken() {
    // Send authentication initially then set interval to send request every hour
    httpRequest('get', 'http://localhost:8080/REST_API/public/auth', 'getKey', '');
    // Set interval that happens every hour
    setInterval(function() {
        // Set new key every hour
        httpRequest('get', 'http://localhost:8080/REST_API/public/auth', 'getKey', '');
    },60000 * 60);
}