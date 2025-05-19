document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');

    // Function to replace text inputs with checkboxes
    function replaceTaxonomyFields() {
        // Try to locate the attachment ID within the form fields
        var attachmentDetails = document.querySelector('.attachment-details'); // Targeting modal content
        console.log('Attachment details:', attachmentDetails);

        if (attachmentDetails) {
            // Check if the form fields contain an attachment ID (search within the hidden input or other input fields)
            var attachmentID = document.querySelector('.compat-item input[name^="attachments["]').getAttribute('name').match(/\d+/)[0]; 
            console.log('Attachment ID:', attachmentID);

            if (attachmentID) {
                var taxonomies = ['doccat', 'doctype']; // Your taxonomy slugs
                taxonomies.forEach(function(taxonomy) {
                    var taxonomyField = document.querySelector('input[name="attachments[' + attachmentID + '][' + taxonomy + ']"]');
                    console.log('Found taxonomy field for:', taxonomy, taxonomyField);

                    if (taxonomyField) {
                        // AJAX request to replace with checkboxes
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', ajaxurl, true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                var response = JSON.parse(xhr.responseText);
                                console.log('AJAX response for', taxonomy, response);

                                if (response && response.success && response.data.terms_html) {
                                    taxonomyField.parentElement.innerHTML = response.data.terms_html;
                                    console.log('Replaced taxonomy field with checkboxes for:', taxonomy);
                                }
                            }
                        };
                        xhr.send('action=fetch_taxonomy_terms&taxonomy=' + taxonomy + '&attachment_id=' + attachmentID);
                    }
                });
            }
        }
    }

    // Function to handle field replacement after navigation
    function handleModalNavigation() {
        console.log('Handling modal navigation...');
        setTimeout(function() {
            replaceTaxonomyFields();
        }, 200); // Delay to allow modal content to load
    }

    // Detect when a media item is clicked in the grid
    document.body.addEventListener('click', function(event) {
        var mediaItem = event.target.closest('li[data-id]');
        if (mediaItem) {
            console.log('Media item clicked:', mediaItem);
            setTimeout(function() {
                replaceTaxonomyFields(); // Replace fields after the modal opens
            }, 200);
        }
    });

    // Detect left/right button clicks for navigation in the modal
    document.body.addEventListener('click', function(event) {
        if (event.target.closest('.left') || event.target.closest('.right')) {
            console.log('Left/Right button clicked for navigation');
            handleModalNavigation();
        }
    });

    // Set up MutationObserver to track changes in modal content (when navigating)
    var modalContent = document.querySelector('.media-modal-content');
    if (modalContent) {
        console.log('Setting up MutationObserver on modal content');
        var observer = new MutationObserver(function(mutationsList) {
            mutationsList.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    console.log('Modal content changed, handling navigation...');
                    handleModalNavigation();
                }
            });
        });

        // Observe changes in modal content
        observer.observe(modalContent, { childList: true, subtree: true });
    }
});
