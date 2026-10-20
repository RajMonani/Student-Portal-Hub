document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function(event) {
        let isValid = true;
        
        const username = document.getElementById("username")?.value.trim();
        const email = document.getElementById("email")?.value.trim();
        const password = document.getElementById("password")?.value;
        const confirmPassword = document.getElementById("confirm_password")?.value;

        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;

        if (username && !usernameRegex.test(username)) {
            alert("Username must be between 3 and 20 characters and contain only letters, numbers, or underscores.");
            isValid = false;
        }

        if (email && !emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            isValid = false;
        }

        if (password && !passwordRegex.test(password)) {
            alert("Password must be at least 8 characters long and contain at least one letter and one number.");
            isValid = false;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
});