// public/js/validation.js
document.addEventListener('DOMContentLoaded', () => {
    const usernameField = document.querySelector('#username');
    
    usernameField.addEventListener('keyup', () => {
        const request = new XMLHttpRequest();
        request.open('POST', 'path_to_your_validation_script.php');
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        request.onload = () => {
            if (request.status >= 200 && request.status < 400) {
                // Handle the response from your validation script
                // E.g., Displaying error messages next to the input fields
            }
        };
        
        request.send('username=' + usernameField.value);
    });
});
