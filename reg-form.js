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

    if (hasLowercase && hasUppercase && hasNumbers) return "Strong";
    if ((hasLowercase || hasUppercase) && hasNumbers) return "Middle";
    if (hasLowercase || hasUppercase) return "Weak";
    return "Invalid";
}

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const mobile = document.getElementById('mobile').value.trim();
    const city = document.getElementById('city').value.trim();
    const country = document.getElementById('country').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const registrationDate = document.getElementById('registrationDate').value;

    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const strengthDiv = document.getElementById('strength');

    let valid = true;

    if (!validateEmail(email)) {
        emailError.textContent = "Please enter a valid email address.";
        valid = false;
    } else {
        emailError.textContent = "";
    }

    if (!validatePasswords(password, confirmPassword)) {
        passwordError.textContent = "Passwords do not match.";
        valid = false;
    } else {
        passwordError.textContent = "";
    }

    const strength = checkPasswordStrength(password);
    strengthDiv.textContent = `Password strength: ${strength}`;
    strengthDiv.className = strength;

    if (!valid || strength === "Invalid") return;

    const formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('mobile', mobile);
    formData.append('city', city);
    formData.append('country', country);
    formData.append('password', password);
    formData.append('registrationDate', registrationDate);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Registration successful! You can now log in.');
            window.location.href = 'log-form.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Unexpected error. Try again later.');
    });
});
