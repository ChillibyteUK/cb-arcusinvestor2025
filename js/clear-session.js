document.addEventListener('DOMContentLoaded', function() {
    const clearButton = document.getElementById('clear-session-button');

    if (clearButton) {
        clearButton.addEventListener('click', function() {
            // Send AJAX request to clear session
            fetch(ajax_object.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=clear_session'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page after clearing session
                        location.reload();
                    } else {
                        console.error('Failed to clear session:', data);
                    }
                })
                .catch(error => {
                    console.error('Error clearing session:', error);
                });
        });
    }
});