function validateEmail(email) {
    const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]{2,}$/;
    return emailPattern.test(email);
}

function validatePasswords(password, confirmPassword) {
    return password === confirmPassword;
}

function checkPasswordStrength(password) {
    const hasLowercase = /[a-z]/.test(password);
    const hasUppercase = /[A-Z]/.test(password);
    const hasNumbers = /[0-9]/.test(password);

    if (hasLowercase && hasUppercase && hasNumbers) {
        return "Strong";
    } else if ((hasLowercase || hasUppercase) && hasNumbers) {
        return "Middle";
    } else if (hasLowercase || hasUppercase) {
        return "Weak";
    } else {
        return "Invalid";
    }
}

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const strengthDiv = document.getElementById('strength');
    let valid = true;

    const emailError = document.getElementById('emailError');
    if (!validateEmail(email)) {
        emailError.textContent = "Please enter a valid email address.";
        valid = false;
    } else {
        emailError.textContent = "";
    }

    const passwordError = document.getElementById('passwordError');
    if (!validatePasswords(password, confirmPassword)) {
        passwordError.textContent = "Passwords do not match.";
        valid = false;
    } else {
        passwordError.textContent = "";
    }

    const strength = checkPasswordStrength(password);
    strengthDiv.textContent = `Password strength: ${strength}`;
    strengthDiv.className = strength;

    if (valid && strength !== "Invalid") {
        alert("Registration successful!");
    }
});