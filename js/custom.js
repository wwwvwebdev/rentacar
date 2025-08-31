document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sidenav
    var sidenavs = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenavs);

    // Initialize Datepickers
    var datepickers = document.querySelectorAll('.datepicker');
    M.Datepicker.init(datepickers, {
        format: 'yyyy-mm-dd',
        autoClose: true
    });

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
});
