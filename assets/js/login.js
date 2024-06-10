$(document).ready(function() {
    $('#formAuthentication').submit(function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Display overlay with spinner and text
        showOverlay();

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'controllers/form_process.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Display success message
                    alert('Login successful!');

                    // Redirect after a delay
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000); // Change the delay time as needed
                } else {
                    // Hide overlay on error and display error message
                    hideOverlay();
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                // Hide overlay on error and display error message
                hideOverlay();
                alert('AJAX Error: ' + error);
            }
        });
    });

    // Function to display overlay with spinner and text
    function showOverlay() {
        var overlay = $('<div id="reload-overlay"></div>');
        var spinner = $('<div class="sk-chase sk-primary"></div>');
        for (var i = 0; i < 6; i++) {
            spinner.append('<div class="sk-chase-dot"></div>');
        }
        overlay.append(spinner);
        overlay.append('<p>កំពុងដំណើរការ...</p>');
        $('body').append(overlay);
    }

    // Function to hide overlay
    function hideOverlay() {
        $('#reload-overlay').remove();
    }
});
