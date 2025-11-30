document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const messageDiv = document.getElementById('message');

    // Clear previous messages
    messageDiv.textContent = '';

    // Send data to PHP via fetch
    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and redirect after a short delay
            messageDiv.textContent = 'Please wait, redirecting you...';
            messageDiv.style.color = 'green';
            setTimeout(() => {
                // Redirect based on UserType
                if (data.userType === 'Admin') {
                    window.location.href = 'dashboard.php';
                } else if (data.userType === 'Resident') {
                    window.location.href = 'homepage.php';
                }
            }, 1000);
        } else {
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        messageDiv.textContent = 'An error occurred. Please try again.';
        console.error('Error:', error);
    });
});