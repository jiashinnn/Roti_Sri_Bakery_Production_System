document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    const toggleButton = document.querySelector('.toggle-password');

    // Password visibility toggle
    toggleButton.addEventListener('click', function() {
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

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            alert('Please fill in all fields');
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });
}); 