class Validate {

    // Constuctor method
    constructor(form, message) {
        this.form = form;
        this.message = message;
    }

    // Function to switch to sign up
    checkDataEntered() {
        // Set input filled variable to false initially
        let input_filled = [];

        const inputs = this.form.getElementsByTagName('input');

        // Loop through all form inputs
        for (var i = 1; i < inputs.length; i++) {
            if (inputs[i].value == "") {
                if (inputs[i].style.display !== 'none') {
                    // Change styles to red to indicate error
                    inputs[i].style.borderBottomColor = "red";
                    // Push false boolean to array to show input not filled in
                    input_filled.push(false);
                } 
            } else {
                // Push true to array to show input filled in
                input_filled.push(true);
                // Change styles to white to show input is filled
                inputs[i].style.borderBottomColor = '#8ec100';
            }
        }

        // Return true if all inputs are filled in
        let validate = input_filled.every(function (e) {
            return e === true;
        });

        // If validate is not true display error message
        if (validate !== true) {
            this.displayMessage('All fields must be filled out!', this.message);
        }

        // Return validation result
        return validate;
    }

    // Function to ensure string size is smaller than limit
    checkStringSize() {
        // Set check to false initially 
        let correct_size = false;
        // get team name
        const string = this.form['PhoneNumber'].value;

        // count string size
        const string_size = string.length;

        if (string_size == 11 && !isNaN(string)) {
            // set correct size to true
            correct_size = true;
        } else  {
            // Display message
            this.displayMessage('Phone Number must be 11 numbers!', this.message);
            // Change border
            this.form['PhoneNumber'].style.borderBottomColor = 'red';
        }

        return correct_size;
    }

    userIdSet() {
        // Initially set to false
        let userIdSet = true;
        // Get form
        const userId = this.form['userId'];
        // Check to see input has been filled
        if (userId.value == '' || userId.value == null || userId.value == undefined) {
            // set to false
            userIdSet = false;
            // display error message
            this.displayMessage('Must select a user!', this.message);
        }

        return userIdSet;
    }   

    // Function to display error message
    displayMessage(msg, element) {
        // Show element
        element.style.display = 'flex';
        // Add error Message to P tag
        element.innerHTML = msg;

        // remove error class if it is on
        if (element.classList.contains('success')) {
            // remove error class
            element.classList.remove('success');
        }

        // Style Error Message
        element.classList.add('error');
    }
}