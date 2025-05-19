<?php
/**
 * Compliance Modal Functionality
 *
 * This file contains the code for displaying and managing the compliance modal,
 * including AJAX handlers, session management, and dynamic region data retrieval.
 *
 * @package cb-arcusinvestor2025
 */

add_action(
	'init',
	function () {
		if ( ! session_id() ) {
			session_start();
		}
	}
);

add_action('wp_footer', function () {
    echo '<div class="container-xl"><hr><div class="fw-bold mb-2">DEBUG INFO</div>';
    echo '<pre>SESSION: ' . print_r($_SESSION, true) . '</pre>';
    echo '<button id="clear-session-button" class="btn btn-secondary">Clear Session & Reload</button>';
    echo '</div>';
});

// DEBUG function to clear session
function enqueue_clear_session_script()
{
    wp_enqueue_script(
        'clear-session-script',
        get_stylesheet_directory_uri() . '/js/clear-session.js',
        array('child-understrap-scripts'),
        '1.0',
        true
    );

    // Localize AJAX URL
    wp_localize_script('clear-session-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_clear_session_script');

// Function to enqueue modal scripts and styles
function enqueue_compliance_modal_scripts()
{
    // error_log('Compliance modal script enqueued.');

    // Only enqueue if region session is not set
    if (!isset($_SESSION['region'])) {
        // Enqueue custom modal JavaScript
        wp_enqueue_script(
            'compliance-modal',
            get_stylesheet_directory_uri() . '/js/compliance-modal.js',
            array('child-understrap-scripts'),
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_compliance_modal_scripts');

// Function to dynamically retrieve regions and disclaimers
function get_compliance_regions()
{
    $regions = get_terms(array(
        'taxonomy' => 'region',
        'hide_empty' => false,
    ));

    $region_data = array();

    foreach ($regions as $region) {
        $countries = get_field('countries', 'region_' . $region->term_id);
        $disclaimer = get_field('disclaimer', 'region_' . $region->term_id);

        $region_data[] = array(
            'slug' => $region->slug,
            'name' => $region->name,
            'countries' => $countries ? explode("\n", $countries) : [],
            'disclaimer' => $disclaimer ?: '',
        );
    }

    return $region_data;
}

// AJAX handler to fetch the disclaimer for a region term
function fetch_region_disclaimer()
{
    // Check if the region slug is provided
    if (!isset($_POST['region_slug']) || empty($_POST['region_slug'])) {
        wp_send_json_error(['message' => 'Region slug not provided.']);
        return;
    }

    $region_slug = sanitize_text_field($_POST['region_slug']);

    // Get the term by slug
    $term = get_term_by('slug', $region_slug, 'region');
    if (!$term) {
        wp_send_json_error(['message' => 'Region not found.']);
        return;
    }

    // Get the 'disclaimer' ACF field for the term
    $disclaimer = get_field('disclaimer', 'region_' . $term->term_id);
    if (!$disclaimer) {
        wp_send_json_error(['message' => 'Disclaimer not found for this region.']);
        return;
    }

    // Return the disclaimer text
    // wp_send_json_success(['disclaimer' => $disclaimer]);
    wp_send_json_success(['disclaimer' => wp_kses_post($disclaimer)]);
}
add_action('wp_ajax_fetch_region_disclaimer', 'fetch_region_disclaimer');
add_action('wp_ajax_nopriv_fetch_region_disclaimer', 'fetch_region_disclaimer');



// Function to output the modal HTML
function display_compliance_modal()
{
    if (isset($_SESSION['region'])) {
        return;
    }

    $regions = get_compliance_regions();
    ?>
    <style>
        #disclaimerText {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
            padding-bottom: 2rem;
        }
        .compliance-backdrop {
            background-image: url(<?=get_stylesheet_directory_uri()?>/img/modal-bg.svg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: bottom left;
            opacity: 1 !important;
        }
    </style>
    <div class="modal fade" id="complianceModal" tabindex="-1" aria-labelledby="complianceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="<?= get_stylesheet_directory_uri() ?>/img/arcus-logo.svg" width=141 height=34>
                </div>
                <div class="modal-body">
                    <div id="step1">
                        <p>Please confirm:</p>
                        <div class="form-check">
                            <label for="investorCheckbox" class="form-check-label">I am a professional or institutional investor</label>
                            <input type="checkbox" class="form-check-input" id="investorCheckbox">
                        </div>
                    </div>
                    <div id="step2" class="d-none">
                        <label for="regionSelect" class="form-label">Select your country</label>
                        <select id="regionSelect" class="form-select">
                            <option value="">-- Select a country --</option>
                            <?php
                                $regions = get_terms(array('taxonomy' => 'region', 'hide_empty' => false));
    foreach ($regions as $region) {
        // Fetch the 'countries' ACF field for the current term
        $countries = get_field('countries', 'region_' . $region->term_id);

        // If 'countries' field exists, loop through its values
        if ($countries) {
            $countries_array = explode("\n", $countries); // Split lines into an array
            foreach ($countries_array as $country) {
                echo '<option data-region="' . esc_attr($region->slug) . '">' . esc_html(trim($country)) . '</option>';
            }
        }
    }
    ?>
                        </select>
                    </div>
                    <div id="step3" class="d-none">
                        <div id="disclaimerText">
                            <p>Select a country to view the disclaimer.</p>
                        </div>
                        <button id="acceptButton" class="btn btn-primary mt-3" disabled>Accept</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const complianceModal = document.getElementById('complianceModal');

    complianceModal.addEventListener('show.bs.modal', function () {
        setTimeout(() => {
            document.querySelector('.modal-backdrop').classList.add('compliance-backdrop');
        }, 10);
    });

    complianceModal.addEventListener('hidden.bs.modal', function () {
        document.querySelector('.modal-backdrop')?.classList.remove('compliance-backdrop');
    });
});
</script>
<?php
}
add_action('wp_footer', 'display_compliance_modal');

function set_region_session()
{
    // Ensure the session is started
    if (!session_id()) {
        session_start();
    }

    // Validate the region slug
    if (!isset($_POST['region_slug']) || empty($_POST['region_slug'])) {
        wp_send_json_error(['message' => 'Region slug not provided.']);
        return;
    }

    $region_slug = sanitize_text_field($_POST['region_slug']);

    // Set the region in the session
    $_SESSION['region'] = $region_slug;

    wp_send_json_success(['message' => 'Session variable set.']);
}
add_action('wp_ajax_set_region_session', 'set_region_session');
add_action('wp_ajax_nopriv_set_region_session', 'set_region_session');
