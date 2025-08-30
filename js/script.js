document.addEventListener('DOMContentLoaded', function() {
    console.log("Script loaded and DOM is ready.");

    // --- Registration Form Validation ---
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (password.length < 6) {
                alert('Password must be at least 6 characters long.');
                event.preventDefault(); // Stop form submission
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                event.preventDefault(); // Stop form submission
                return;
            }
        });
    }

    // --- Booking Form Validation ---
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
            const startDateStr = document.getElementById('start_date').value;
            const endDateStr = document.getElementById('end_date').value;

            if (!startDateStr || !endDateStr) {
                alert('Please select both a start and end date.');
                event.preventDefault();
                return;
            }

            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Set to midnight for accurate date comparison

            if (startDate < today) {
                alert('Start date cannot be in the past.');
                event.preventDefault();
                return;
            }

            if (endDate <= startDate) {
                alert('End date must be after the start date.');
                event.preventDefault();
                return;
            }
        });
    }
});
