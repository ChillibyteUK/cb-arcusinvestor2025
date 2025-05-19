document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('complianceModal');
    const disclaimerText = document.getElementById('disclaimerText');
    const acceptButton = document.getElementById('acceptButton');
    const regionSelect = document.getElementById('regionSelect');
    const investorCheckbox = document.getElementById('investorCheckbox');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    if (!modalElement) {
        console.error('Modal element not found.');
        return;
    }

    const modal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();

    // Helper: Show specific step
    const showStep = (stepToShow) => {
        [step1, step2, step3].forEach((step) => {
            step.classList.add('d-none');
        });
        stepToShow.classList.remove('d-none');
    };

    // Add scroll target to the bottom of disclaimerText
    const scrollTarget = document.createElement('div');
    scrollTarget.setAttribute('id', 'scrollTarget');
    scrollTarget.style.height = '1px'; // Small height, just for detection
    disclaimerText.appendChild(scrollTarget);

    // Intersection Observer for enabling the Accept button
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                acceptButton.disabled = false; // Enable the button when visible
            } else {
                acceptButton.disabled = true; // Disable the button otherwise
            }
        });
    }, {
        root: disclaimerText, // Observe within the disclaimerText container
        threshold: 1.0 // Fully visible in the viewport
    });

    // Observe the scrollTarget
    observer.observe(scrollTarget);

    // Checkbox: Show Step 2 when checked
    investorCheckbox.addEventListener('change', function () {
        if (this.checked) {
            showStep(step2); // Show Step 2 (select a region)
        } else {
            showStep(step1); // Reset to Step 1
        }
    });

    // Dropdown: Fetch disclaimer and show Step 3 (while keeping dropdown visible)
    regionSelect.addEventListener('change', function () {
        const selectedRegionSlug = regionSelect.options[regionSelect.selectedIndex].getAttribute('data-region');
        if (!selectedRegionSlug) {
            disclaimerText.innerHTML = '<p>Please select a region to view the disclaimer.</p>';
            return;
        }

        // Fetch disclaimer content
        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=fetch_region_disclaimer&region_slug=${encodeURIComponent(selectedRegionSlug)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    disclaimerText.innerHTML = data.data.disclaimer;

                    // Re-append scroll target after content update
                    disclaimerText.appendChild(scrollTarget);

                    // Transition to Step 3 while keeping the dropdown visible
                    step2.classList.remove('d-none'); // Keep dropdown visible
                    step3.classList.remove('d-none'); // Show disclaimer
                } else {
                    disclaimerText.innerHTML = `<p>Error: ${data.data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching disclaimer:', error);
                disclaimerText.innerHTML = '<p>Failed to load disclaimer. Please try again later.</p>';
            });
    });

    // Accept button: Set session variable and reload
    acceptButton.addEventListener('click', function () {
        const selectedRegionSlug = regionSelect.options[regionSelect.selectedIndex].getAttribute('data-region');
        if (!selectedRegionSlug) {
            console.error('No region selected. Cannot set session variable.');
            return;
        }

        // Set session variable via AJAX
        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_region_session&region_slug=${encodeURIComponent(selectedRegionSlug)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.hide(); // Hide modal
                    setTimeout(() => {
                        location.reload(); // Reload page
                    }, 300);
                } else {
                    console.error('Error setting session variable:', data.data.message);
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
            });
    });
});
