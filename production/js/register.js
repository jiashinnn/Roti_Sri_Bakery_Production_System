document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.register-form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const phoneNumber = document.getElementById('contact');
    const email = document.getElementById('email');
    const toggleButtons = document.querySelectorAll('.toggle-password');

    // Password visibility toggle
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            // Toggle password visibility
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Form validation
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Email validation (basic)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            alert('Please enter a valid email address.');
            return;
        }

        // Phone number validation (9-15 digits)
        const phoneRegex = /^\d{9,15}$/;
        if (!phoneRegex.test(phoneNumber.value)) {
            alert('Please enter a valid phone number (9-15 digits).');
            return;
        }

        // Password validation
        if (password.value !== confirmPassword.value) {
            alert('Passwords do not match!');
            return;
        }

        // If all validations pass, submit the form
        this.submit();
    });
}); 