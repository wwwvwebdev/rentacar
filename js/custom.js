document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sidenav
    var sidenavs = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenavs);

    // Initialize Datepickers
    var datepickers = document.querySelectorAll('.datepicker');
    var datepicker_options = {
        format: 'yyyy-mm-dd',
        autoClose: true,
        minDate: new Date()
    };
    M.Datepicker.init(datepickers, datepicker_options);

    // Initialize Material-Boxed Images
    var materialboxes = document.querySelectorAll('.materialboxed');
    M.Materialbox.init(materialboxes);

    // Initialize Form Selects
    var selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    // Initialize Character Counters for text inputs
    var text_inputs = document.querySelectorAll('input[type=text], input[type=email], input[type=password]');
    M.CharacterCounter.init(text_inputs);

    console.log("All Materialize components initialized.");

    // --- Dynamic Price Calculation for Booking Form ---
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        const startDateEl = document.getElementById('start_date');
        const endDateEl = document.getElementById('end_date');
        const withDriverEl = document.getElementById('with_driver');
        const totalPriceEl = document.getElementById('total-price-display');

        const carPrice = parseFloat(bookingForm.dataset.carPrice);
        const driverPrice = parseFloat(bookingForm.dataset.driverPrice);

        function calculateTotal() {
            const startDate = M.Datepicker.getInstance(startDateEl).date;
            const endDate = M.Datepicker.getInstance(endDateEl).date;

            if (startDate && endDate && endDate > startDate) {
                const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                const diffDays = Math.round(Math.abs((endDate - startDate) / oneDay));

                let total = diffDays * carPrice;

                if (withDriverEl && withDriverEl.checked) {
                    total += diffDays * driverPrice;
                }

                totalPriceEl.textContent = `Total: $${total.toFixed(2)}`;
            } else {
                totalPriceEl.textContent = 'Total: $0.00';
            }
        }

        // We need to get the datepicker instances to add 'onClose' event listeners
        var startDateInstance = M.Datepicker.getInstance(startDateEl);
        startDateInstance.options.onClose = calculateTotal;

        var endDateInstance = M.Datepicker.getInstance(endDateEl);
        endDateInstance.options.onClose = calculateTotal;

        if (withDriverEl) {
            withDriverEl.addEventListener('change', calculateTotal);
        }
    }
});
