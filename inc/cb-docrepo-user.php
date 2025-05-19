<?php
/**
 * File: cb-docrepo-user.php
 * Description: Contains user-related functionalities for the CB Arcus Investor 2025 theme.
 * Author: Chillibyte - DS
 * Version: 1.0.0
 *
 * @package CB_ArcusInvestor2025
 */

// phpcs:disable WordPress.Security.NonceVerification.Recommended
// phpcs:disable WordPress.Security.NonceVerification.Missing

/**
 * Prevent login if the user's account has expired.
 *
 * @param WP_User|WP_Error $user The authenticated user object or error.
 * @return WP_User|WP_Error
 */
function cb_check_user_expiry( $user ) {
	if ( is_wp_error( $user ) ) {
		return $user;
	}

	$expiry = get_field( 'account_expiry_date', 'user_' . $user->ID );

	if ( ! $expiry ) {
		return $user; // No expiry set.
	}

	$expiry_timestamp = strtotime( $expiry );
	$now              = strtotime( current_time( 'Y-m-d' ) );

	if ( $expiry_timestamp && $expiry_timestamp < $now ) {
		return new WP_Error(
			'expired_account',
			__( 'Your account has expired. Please contact the administrator.', 'your-textdomain' )
		);
	}

	return $user;
}
add_filter( 'wp_authenticate_user', 'cb_check_user_expiry' );


/**
 * Redirect failed login back to custom portal login page.
 */
function cb_login_failed_redirect() {
	$referrer = wp_get_referer();

	if ( ! empty( $referrer ) && strpos( $referrer, 'portal-login' ) !== false ) {
		wp_safe_redirect( add_query_arg( 'login', 'failed', $referrer ) );
		exit;
	}
}
add_action( 'wp_login_failed', 'cb_login_failed_redirect' );

/**
 * Redirect non-logged-in users to the custom login page.
 */
add_action( 'template_redirect', 'cb_redirect_to_portal_login' );

/**
 * Redirect non-logged-in users to the custom login page.
 *
 * This function checks if the user is not logged in and not accessing
 * excluded paths, then redirects them to the custom portal login page.
 */
function cb_redirect_to_portal_login() {

	if ( is_user_logged_in() ) {
		return;
	}

	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$excluded_paths = array(
		'/portal-login/',
		'/wp-login.php',
		'/wp-json/',
		'/wp-cron.php',
	);

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	foreach ( $excluded_paths as $excluded ) {
		if ( strpos( $request_uri, $excluded ) === 0 || strpos( $request_uri, $excluded ) !== false ) {
			return;
		}
	}

	wp_safe_redirect( home_url( '/portal-login/' ) );
	exit;
}


// --------------------- User Management --------------------- //

/**
 * Simplify the portal user profile by hiding unnecessary fields.
 *
 * This function hides fields such as Visual Editor, Keyboard Shortcuts,
 * Admin Colour Scheme, Language, Nickname, Display name dropdown,
 * Biographical Info, and Profile Picture on the profile and user-edit pages.
 */
function cb_simplify_portal_user_profile() {
	if ( ! in_array( get_current_screen()->id, array( 'profile', 'user-edit' ), true ) ) {
		return;
	}

	echo '<style>
		/* Hide: Visual Editor, Keyboard Shortcuts, Admin Colour Scheme, Language */
		.user-rich-editing-wrap,
		.user-syntax-highlighting-wrap,
		.user-comment-shortcuts-wrap,
		.user-admin-color-wrap,
		.user-language-wrap,

		/* Hide: Nickname and Display name dropdown */
		.user-nickname-wrap,
		.user-display-name-wrap,

		/* Hide: Biographical Info and Profile Picture */
		.user-description-wrap,
		tr.user-profile-picture {
			display: none !important;
		}

		/* Hide: Application Passwords */
		.application-passwords {
			display: none !important;
		}
	</style>';
}
add_action( 'admin_head-user-edit.php', 'cb_simplify_portal_user_profile' );
add_action( 'admin_head-profile.php', 'cb_simplify_portal_user_profile' );

/**
 * Force the user's nickname and display name to match their username.
 *
 * @param int $user_id The ID of the user being updated.
 */
function cb_force_nickname_to_username( $user_id ) {
	$user = get_userdata( $user_id );
	if ( $user && isset( $user->user_login ) ) {
		wp_update_user(
			array(
				'ID'           => $user_id,
				'nickname'     => $user->user_login,
				'display_name' => $user->user_login,
			)
		);
	}
}
add_action( 'personal_options_update', 'cb_force_nickname_to_username' );
add_action( 'edit_user_profile_update', 'cb_force_nickname_to_username' );

// disable application passwords.
add_filter( 'wp_is_application_passwords_available', '__return_false' );

/**
 * Disable the admin bar for non-admin users.
 *
 * @param int $user_id The ID of the user being updated.
 */
function cb_force_toolbar_by_role( $user_id ) {
	$user = get_userdata( $user_id );

	if ( in_array( 'administrator', (array) $user->roles, true ) ) {
		update_user_meta( $user_id, 'show_admin_bar_front', 'true' );
	} else {
		update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
	}
}
add_action( 'edit_user_profile_update', 'cb_force_toolbar_by_role' );
add_action( 'personal_options_update', 'cb_force_toolbar_by_role' );
add_action( 'user_register', 'cb_force_toolbar_by_role' );

/**
 * Hide the admin bar options.
 */
function cb_hide_toolbar_ui() {
	echo '<style>
		.user-admin-bar-front-wrap {
			display: none !important;
		}
	</style>';
}
add_action( 'admin_head-user-edit.php', 'cb_hide_toolbar_ui' );
add_action( 'admin_head-profile.php', 'cb_hide_toolbar_ui' );

/**
 * Add contact phone to user profile.
 *
 * @param array $methods An array of contact methods.
 * @return array Modified array of contact methods.
 */
function cb_add_contact_phone_field( $methods ) {
	$methods['contact_phone'] = __( 'Contact Phone', 'cb-arcusinvestor2025' );
	return $methods;
}
add_filter( 'user_contactmethods', 'cb_add_contact_phone_field' );


/**
 * Output the contact phone field on the "Add New User" screen.
 */
function cb_contact_phone_user_new_form() {
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const websiteRow = document.getElementById('url').closest('tr');
			if (websiteRow) {
				const phoneRow = document.createElement('tr');
				phoneRow.innerHTML = `
				<th><label for="contact_phone">Contact Phone</label></th>
				<td>
				<input type="text" name="contact_phone" id="contact_phone" value="" class="regular-text" />
				</td>
				`;
				websiteRow.parentNode.insertBefore(phoneRow, websiteRow.nextSibling);
			}
		});
		</script>
	<?php
}
add_action( 'user_new_form', 'cb_contact_phone_user_new_form' );

/**
 * Save the contact phone number for a user during registration.
 *
 * @param int $user_id The ID of the user being registered.
 */
function cb_save_contact_phone_on_user_register( $user_id ) {
	if ( isset( $_POST['contact_phone'] ) ) {
		update_user_meta( $user_id, 'contact_phone', sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) );
	}
}
add_action( 'user_register', 'cb_save_contact_phone_on_user_register' );


/**
 * Remove the "About the user" heading from the user profile and edit screens.
 *
 * This function uses JavaScript to find and remove the "About the user" heading
 * from the user profile and edit screens in the WordPress admin.
 */
function cb_remove_about_the_user_heading() {
	$screen = get_current_screen();
	if ( 'user' === $screen->base || 'profile' === $screen->base ) {
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const headings = document.querySelectorAll('h2');
			headings.forEach(function (h2) {
				if (h2.textContent.trim() === 'About the user') {
					h2.remove();
				}
			});
		});
		</script>
		<?php
	}
}
add_action( 'admin_footer', 'cb_remove_about_the_user_heading' );


/**
 * Automatically selects the 'Portal User' role by default on the "Add New User" screen.
 */
add_action(
	'user_new_form',
	function () {
		?>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const roleSelect = document.getElementById('role');
		if (!roleSelect) {
			console.log('Role select not found.');
			return;
		}

		if (roleSelect.value === 'subscriber') {
			console.log('Overriding default role to "portal_user"');
			const portalOption = roleSelect.querySelector('option[value="portal_user"]');
			if (portalOption) {
				portalOption.selected = true;
			}
		} else {
			console.log('Role already changed or set explicitly:', roleSelect.value);
		}
	});
</script>
		<?php
	}
);


add_filter(
	'default_role',
	function () {
		return 'portal_user';
	}
);

add_action(
	'admin_head-user-new.php',
	function () {
		?>
	<style>
		.user-language-wrap {
			display: none !important;
		}
	</style>
		<?php
	}
);

add_action(
	'admin_init',
	function () {
		$user = wp_get_current_user();
		if ( is_admin() && ! defined( 'DOING_AJAX' ) && in_array( 'portal_user', (array) $user->roles, true ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}
);


// --------------------- User Functions --------------------- //

/**
 * Add a custom user role 'Portal User' based on the 'Contributor' role.
 */
function cb_add_portal_user_role() {
	if ( ! get_role( 'portal_user' ) ) {
		$contributor = get_role( 'contributor' );

		if ( $contributor ) {
			add_role( 'portal_user', 'Portal User', $contributor->capabilities );
		}
	}
}
add_action( 'init', 'cb_add_portal_user_role' );

// --------------------- User Library Access --------------------- //


add_filter(
	'acf/load_field/name=rml_folder_access',
	function ( $field ) {
		if ( ! function_exists( 'wp_rml_objects' ) ) {
			error_log( 'RML: wp_rml_objects() not available' );
			return $field;
		}

		$field['choices'] = array();

		$folders = wp_rml_objects();
		if ( empty( $folders ) ) {
			error_log( 'RML: No folders returned' );
			return $field;
		}

		foreach ( $folders as $folder ) {
			// Only list real folders (exclude smart folders etc.).
			if ( method_exists( $folder, 'getId' ) && method_exists( $folder, 'getName' ) ) {
				$field['choices'][ (string) $folder->getId() ] = $folder->getName();
			}
		}

		return $field;
	}
);

// phpcs:enable WordPress.Security.NonceVerification.Recommended
// phpcs:enable WordPress.Security.NonceVerification.Missing

/**
 * Get RML folder IDs the user has access to.
 *
 * @param int|null $user_id Optional. Defaults to current user.
 * @return int[] Array of folder IDs the user can access.
 */
function cb_get_user_rml_folder_ids( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$folder_ids = get_field( 'rml_folder_access', 'user_' . $user_id );

	if ( is_array( $folder_ids ) ) {
		return array_map( 'intval', $folder_ids );
	}

	return array();
}


// add_action('init', function() {
// 	if (is_user_logged_in()) {
// 		$ids = cb_get_user_rml_folder_ids();
// 		error_log('cb_get_user_rml_folder_ids: ' . print_r($ids, true));
// 	}
// });
