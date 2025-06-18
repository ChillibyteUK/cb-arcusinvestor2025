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

add_filter(
	'authenticate',
	function ( $user, $username, $password ) {
		if ( isset( $_POST['log'] ) && ! empty( $_POST['log'] ) && isset( $_POST['pwd'] ) ) {
			$user = wp_authenticate_username_password( null, $username, $password );
			if ( is_wp_error( $user ) ) {
				wp_safe_redirect( add_query_arg( 'login', 'failed', wp_get_referer() ) );
				exit;
			}
		}
		return $user;
	},
	30,
	3
);


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
		wp_safe_redirect( add_query_arg( 'login', 'expired', home_url( '/portal-login/' ) ) );
		exit;
	}

	return $user;
}
add_filter( 'wp_authenticate_user', 'cb_check_user_expiry' );


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
		'/request-access/',
		'/forgot-password/',
		'/reset-password/',
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
 * Additional user profile fields.
 *
 * @param array $methods An array of contact methods.
 * @return array Modified array of contact methods.
 */
function cb_additional_user_fields( $methods ) {
	$methods['company_name']     = __( 'Company Name', 'cb-arcusinvestor2025' );
	$methods['domicile']         = __( 'Domicile', 'cb-arcusinvestor2025' );
	$methods['regulator_id']     = __( 'Regulator & Regulator ID', 'cb-arcusinvestor2025' );
	$methods['access_requested'] = __( 'Access Requested', 'cb-arcusinvestor2025' );
	return $methods;
}
add_filter( 'user_contactmethods', 'cb_additional_user_fields' );


/**
 * Output the additional fields on the "Add New User" screen.
 */
function cb_additional_fields_user_new_form() {
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const websiteRow = document.getElementById('url').closest('tr');
			if (websiteRow) {

				const accessRow = document.createElement('tr');
				accessRow.innerHTML = `
				<th><label for="access_requested">Access Requested</label></th>
				<td>
				<textarea rows="3" name="access_requested" id="access_requested" value="" class="regular-text" /></textarea>
				</td>
				`;
				websiteRow.parentNode.insertBefore(accessRow, websiteRow.nextSibling);

				const regulatorRow = document.createElement('tr');
				regulatorRow.innerHTML = `
				<th><label for="regulator_id">Regulator &amp; Regulator ID</label></th>
				<td>
				<input type="text" name="regulator_id" id="regulator_id" value="" class="regular-text" />
				</td>
				`;
				websiteRow.parentNode.insertBefore(regulatorRow, websiteRow.nextSibling);

				const domicileRow = document.createElement('tr');
				domicileRow.innerHTML = `
				<th><label for="domicile">Domicile</label></th>
				<td>
				<input type="text" name="domicile" id="domicile" value="" class="regular-text" />
				</td>
				`;
				websiteRow.parentNode.insertBefore(domicileRow, websiteRow.nextSibling);

				const companyRow = document.createElement('tr');
				companyRow.innerHTML = `
				<th><label for="company_name">Company Name</label></th>
				<td>
				<input type="text" name="company_name" id="company_name" value="" class="regular-text" />
				</td>
				`;
				websiteRow.parentNode.insertBefore(companyRow, websiteRow.nextSibling);

			} 

		});
		</script>
	<?php
}
add_action( 'user_new_form', 'cb_additional_fields_user_new_form' );

add_action( 'admin_footer-user-new.php', function () {
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function findFolderAccessRow() {
        // Find the last tr containing the rml_folder_access field
        const folderField = document.querySelector('[name="acf[rml_folder_access][]"]');
        if (folderField) {
            return folderField.closest('tr');
        }
        // fallback: try by label
        const label = Array.from(document.querySelectorAll('label')).find(l => l.textContent.match(/User Folder Access/i));
        if (label) {
            return label.closest('tr');
        }
        return null;
    }

    function findPasswordRows() {
        // Collect all password-related rows
        const rows = [];
        const pass1 = document.getElementById('pass1')?.closest('tr');
        const pass2 = document.getElementById('pass2')?.closest('tr');
        const pwWeak = document.querySelector('tr.pw-weak');
        if (pass1) rows.push(pass1);
        if (pass2 && pass2 !== pass1) rows.push(pass2);
        if (pwWeak && !rows.includes(pwWeak)) rows.push(pwWeak);
        return rows;
    }

    function findNotificationRow() {
        // Find the tr that contains "Send User Notification" in any cell
        const rows = document.querySelectorAll('form#createuser tr');
        for (const row of rows) {
            if (row.innerText && row.innerText.match(/Send User Notification/i)) {
                return row;
            }
        }
        return null;
    }

    function reorderRows() {
        const folderRow = findFolderAccessRow();
        const passwordRows = findPasswordRows();
        const notifyRow = findNotificationRow();

        if (folderRow) {
            let insertAfter = folderRow;
            // Move password rows after folderRow, in order
            passwordRows.forEach(row => {
                if (row && row.parentNode) {
                    insertAfter.parentNode.insertBefore(row, insertAfter.nextSibling);
                    insertAfter = row;
                }
            });
            // Move notification row after last password row (or after folderRow if no password rows)
            if (notifyRow && notifyRow.parentNode) {
                insertAfter.parentNode.insertBefore(notifyRow, insertAfter.nextSibling);
                insertAfter = notifyRow;
            }
        }
    }

    // Observe for dynamic field rendering
    const form = document.getElementById('createuser');
    if (form) {
        let observer;
        function safeReorderRows() {
            observer.disconnect();
            reorderRows();
            observer.observe(form, { childList: true, subtree: true });
        }
        observer = new MutationObserver(safeReorderRows);
        observer.observe(form, { childList: true, subtree: true });
        reorderRows();
    }
});
</script>
<?php
});

/**
 * Save the additional user fields during user registration.
 *
 * @param int $user_id The ID of the user being registered.
 */
function cb_save_additional_fields_on_user_register( $user_id ) {
    $fields = array(
        'company_name',
        'domicile',
        'regulator_id',
        'access_requested',
    );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_user_meta( $user_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        } else {
            // Set default value '-' if not provided.
            update_user_meta( $user_id, $field, '-' );
        }
    }
}
add_action( 'user_register', 'cb_save_additional_fields_on_user_register' );


// Add the custom fields to the user management page.
add_filter(
	'manage_users_columns',
	function ( $columns ) {
		$columns['company_name']     = __( 'Company Name', 'cb-arcusinvestor2025' );
		$columns['domicile']         = __( 'Domicile', 'cb-arcusinvestor2025' );
		$columns['regulator_id']     = __( 'Regulator &amp; Regulator ID', 'cb-arcusinvestor2025' );
		$columns['access_requested'] = __( 'Access Requested', 'cb-arcusinvestor2025' );

		if ( isset( $columns['posts'] ) ) {
            unset( $columns['posts'] );
        }

		return $columns;
	}
);

// Populate custom columns.
add_filter(
	'manage_users_custom_column',
	function ( $value, $column_name, $user_id ) {
        if ( in_array( $column_name, array( 'company_name', 'domicile', 'regulator_id', 'access_requested' ), true ) ) {
            $meta = get_user_meta( $user_id, $column_name, true );
			return '' !== $meta ? esc_html( $meta ) : '&ndash;';
        }
		return $value;
	},
	10,
	3
);

// Make columns sortable.
add_filter(
	'manage_users_sortable_columns',
	function ( $columns ) {
		$columns['company_name']     = 'company_name';
		$columns['domicile']         = 'domicile';
		$columns['regulator_id']     = 'regulator_id';
		$columns['access_requested'] = 'access_requested';
		return $columns;
	}
);

// Handle sorting for custom columns.
add_action(
	'pre_get_users',
	function ( $query ) {
		if ( ! is_admin() ) {
			return;
		}
        $orderby        = $query->get( 'orderby' );
        $custom_columns = array( 'company_name', 'domicile', 'regulator_id', 'access_requested' );
        if ( in_array( $orderby, $custom_columns, true ) ) {
            $query->set( 'meta_key', $orderby );
            $query->set( 'orderby', 'meta_value' );
            // This ensures users with no value are included.
            $meta_query = array(
                'relation' => 'OR',
                array(
                    'key'     => $orderby,
                    'compare' => 'EXISTS',
                ),
                array(
                    'key'     => $orderby,
                    'compare' => 'NOT EXISTS',
                ),
            );
            $query->set( 'meta_query', $meta_query );
        }
	}
);


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
		if (!roleSelect) return;

		if (roleSelect.selectedIndex === 0 || !roleSelect.value || roleSelect.value === 'doceditor' || roleSelect.value === 'docadmin' || roleSelect.value === 'administrator') {
			const portalOption = roleSelect.querySelector('option[value="portal_user"]');
			if (portalOption) {
				portalOption.selected = true;
			}
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
            error_log( 'RML: wp_rml_objects() not available' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            return $field;
        }

        $field['choices'] = array();

        $folders = wp_rml_objects();
        if ( empty( $folders ) ) {
            error_log( 'RML: No folders returned' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            return $field;
        }

        // Build hierarchical choices.
        $field['choices'] = cb_build_folder_hierarchy_with_parents( $folders );

        return $field;
    }
);

/**
 * Build a hierarchical folder structure for ACF choices, ensuring parent folders are included.
 *
 * @param array $folders Array of folder objects from wp_rml_objects().
 * @return array Hierarchical folder choices.
 */
function cb_build_folder_hierarchy_with_parents( $folders ) {
    $hierarchy = array();

    // Organize folders by parent ID.
    $folders_by_parent = array();
    $folder_parents    = array(); // Map folder ID to its parent ID.
    foreach ( $folders as $folder ) {
        if ( method_exists( $folder, 'getParent' ) && method_exists( $folder, 'getId' ) ) {
            $parent_id                         = $folder->getParent();
            $folder_id                         = $folder->getId();
            $folders_by_parent[ $parent_id ][] = $folder;
            $folder_parents[ $folder_id ]      = $parent_id;
        }
    }

    // Recursive function to build the hierarchy.
    $build_hierarchy = function ( $parent_id, $level = 0 ) use ( &$build_hierarchy, &$folders_by_parent ) {
        $choices = array();

        if ( isset( $folders_by_parent[ $parent_id ] ) ) {
            foreach ( $folders_by_parent[ $parent_id ] as $folder ) {
                $folder_id   = $folder->getId();
                $folder_name = method_exists( $folder, 'getName' ) ? $folder->getName() : 'Unnamed Folder';

                // Add the folder with indentation based on its level.
                $choices[ $folder_id ] = str_repeat( '--', $level ) . ' ' . $folder_name;

                // Recursively add subfolders.
                $choices += $build_hierarchy( $folder_id, $level + 1 );
            }
        }

        return $choices;
    };

    // Start building the hierarchy from the root (parent ID = -1).
    $hierarchy = $build_hierarchy( -1 );

    // Ensure parent folders are included for any selected sub-folders.
    $hierarchy = cb_include_parent_folders( $hierarchy, $folder_parents );

    return $hierarchy;
}

/**
 * Ensure parent folders are included for any selected sub-folders.
 *
 * @param array $hierarchy      The hierarchical folder choices.
 * @param array $folder_parents Map of folder IDs to their parent IDs.
 * @return array Updated hierarchical folder choices.
 */
function cb_include_parent_folders( $hierarchy, $folder_parents ) {
    $updated_hierarchy = $hierarchy;

    foreach ( $hierarchy as $folder_id => $folder_name ) {
        $current_folder = $folder_id;

        // Traverse up the parent chain and include all parent folders.
        while ( isset( $folder_parents[ $current_folder ] ) && -1 !== $folder_parents[ $current_folder ] ) {
            $parent_id = $folder_parents[ $current_folder ];

            // If the parent is not already in the hierarchy, add it.
            if ( ! isset( $updated_hierarchy[ $parent_id ] ) ) {
                $updated_hierarchy[ $parent_id ] = $hierarchy[ $parent_id ] ?? 'Unnamed Parent Folder';
            }

            $current_folder = $parent_id;
        }
    }

    return $updated_hierarchy;
}

add_filter(
    'acf/update_value/name=rml_folder_access',
    function ( $value, $post_id, $field ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
        if ( ! function_exists( 'wp_rml_objects' ) ) {
            return $value;
        }

        // Get all folders and their parent relationships.
        $folders        = wp_rml_objects();
        $folder_parents = array();

		if ( is_iterable( $folders ) ) {
			foreach ( $folders as $folder ) {
				if ( method_exists( $folder, 'getParent' ) && method_exists( $folder, 'getId' ) ) {
					$folder_id                    = $folder->getId();
					$parent_id                    = $folder->getParent();
					$folder_parents[ $folder_id ] = $parent_id;
				}
			}
		}

        // Ensure parent folders are included in the saved value.
        $updated_value = (array) $value; // Ensure it's an array.
        foreach ( $value as $folder_id ) {
            $current_folder = $folder_id;

            // Traverse up the parent chain and include all parent folders.
            while ( isset( $folder_parents[ $current_folder ] ) && -1 !== $folder_parents[ $current_folder ] ) {
                $parent_id = $folder_parents[ $current_folder ];
                if ( ! in_array( $parent_id, $updated_value, true ) ) {
                    $updated_value[] = $parent_id;
                }
                $current_folder = $parent_id;
            }
        }

        return $updated_value;
    },
    10,
    3
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

    // Ensure $folder_ids is always an array.
    if ( ! is_array( $folder_ids ) ) {
        $folder_ids = array();
    }

    return array_map( 'intval', $folder_ids );
}


// ---------------------- Password Reset --------------------- //

/**
 * Render the password reset request form.
 *
 * This function outputs a form for users to request a password reset.
 *
 * @return string The HTML content of the password reset request form.
 */
function cb_render_password_reset_request_form() {
	ob_start();

	?>
<div class="container">
	<form method="post" action="" class="login-form">
		<?php
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['reset'] ) && 'sent' === $_GET['reset'] ) {
			echo '<p class="alert alert-success">Check your email for the reset link.</p>';
		} elseif ( isset( $_GET['reset'] ) && 'error' === $_GET['reset'] ) {
			echo '<p class="alert alert-danger">We couldn’t find a user with that email address.</p>';
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		?>
		<div class="mb-3">
			<label for="user_email" class="form-label">Email address</label>
			<input type="email" class="form-control" id="user_email" name="user_email" required>
		</div>

		<!-- Honeypot -->
		<input type="text" name="your_name" style="display:none">

		<?php wp_nonce_field( 'cb_password_reset_request', 'cb_reset_nonce' ); ?>
		<div class="text-end">
			<button type="submit" name="cb_password_reset_submit" class="button">Reset Password</button>
		</div>
	</form>
</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'cb_password_reset_request', 'cb_render_password_reset_request_form' );

add_action(
	'init',
	function () {
		if ( isset( $_POST['cb_password_reset_submit'] ) ) {

			if ( ! empty( $_POST['your_name'] ) ) {
				// Honeypot triggered.
				return;
			}

			if ( ! isset( $_POST['cb_reset_nonce'] ) ||
				! wp_verify_nonce( wp_unslash( $_POST['cb_reset_nonce'] ), 'cb_password_reset_request' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return;
			}

			$email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
			$user  = get_user_by( 'email', $email );

			if ( ! $user ) {
				wp_safe_redirect( add_query_arg( 'reset', 'error', wp_get_referer() ) );
				exit;
			}

			$key       = get_password_reset_key( $user );
			$reset_url = add_query_arg(
				array(
					'key'   => $key,
					'login' => rawurlencode( $user->user_login ),
				),
				home_url( '/reset-password/' )
			);

			wp_mail(
				$user->user_email,
				'Password Reset Request',
				"Click the following link to reset your password:\n\n" . $reset_url
			);

			wp_safe_redirect( add_query_arg( 'reset', 'sent', wp_get_referer() ) );
			exit;
		}
	}
);

/**
 * Render the password reset form.
 *
 * This function generates the HTML for the password reset form, including
 * fields for the new password and confirmation, and handles error messages.
 *
 * @return string The HTML content of the password reset form.
 */
function cb_render_password_reset_form() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['reset'] ) && 'success' === $_GET['reset'] ) {
		return '<p class="alert alert-success">Your password has been reset successfully. You may now <a href="/portal-login/">log in</a>.</p>';
	}

	if ( empty( $_GET['key'] ) || empty( $_GET['login'] ) ) {
		return '<p class="alert alert-danger">Invalid password reset link.</p>';
	}

	$key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
	$login = isset( $_GET['login'] ) ? sanitize_user( wp_unslash( $_GET['login'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	// Validate key/login.
	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		return '<p class="alert alert-danger">This password reset link is invalid or has expired.</p>';
	}

	// Begin building output.
	ob_start();

	?>

	<form method="post" class="cb-password-reset-form login-form">
		<?php
		// Display errors from query string.
		$error_msg = '';
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['reset_error'] ) ) {
			switch ( $_GET['reset_error'] ) {
				case 'invalid_nonce':
					$error_msg = 'Security check failed. Please try again.';
					break;
				case 'invalid_key':
					$error_msg = 'Invalid or expired reset key.';
					break;
				case 'mismatch':
					$error_msg = 'Passwords do not match.';
					break;
				case 'weak':
					$error_msg = 'Password must be at least 8 characters and contain uppercase, lowercase, and a number.';
					break;
				default:
					$error_msg = 'There was a problem resetting your password.';
					break;
			}

			echo '<p class="alert alert-danger">' . esc_html( $error_msg ) . '</p>';
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		?>
		<input type="hidden" name="rp_key" value="<?php echo esc_attr( $key ); ?>">
		<input type="hidden" name="rp_login" value="<?php echo esc_attr( $login ); ?>">
		<?php wp_nonce_field( 'cb_password_reset_confirm', 'cb_confirm_nonce' ); ?>

		<div class="mb-3">
			<label for="pass1" class="form-label">New Password</label>
			<input type="password" name="pass1" id="pass1" class="form-control" required>
		</div>

		<div class="mb-3">
			<label for="pass2" class="form-label">Confirm New Password</label>
			<input type="password" name="pass2" id="pass2" class="form-control" required>
		</div>

		<div class="text-end">
			<button type="submit" name="cb_password_reset_confirm" class="button">Set New Password</button>
		</div>
	</form>

	<?php
	return ob_get_clean();
}

add_shortcode( 'cb_password_reset_confirm', 'cb_render_password_reset_form' );

add_action(
	'init',
	function () {
		if ( isset( $_POST['cb_password_reset_confirm'] ) ) {
			if ( ! isset( $_POST['cb_confirm_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cb_confirm_nonce'] ) ), 'cb_password_reset_confirm' ) ) {
				$referer = wp_get_referer() ? wp_get_referer() : home_url( '/portal-login/' );
				wp_safe_redirect( add_query_arg( 'reset_error', 'invalid_nonce', $referer ) );
				exit;
			}

			$rp_key   = isset( $_POST['rp_key'] ) ? sanitize_text_field( wp_unslash( $_POST['rp_key'] ) ) : '';
			$rp_login = isset( $_POST['rp_login'] ) ? sanitize_user( wp_unslash( $_POST['rp_login'] ) ) : '';
			$user     = check_password_reset_key( $rp_key, $rp_login );

			if ( is_wp_error( $user ) ) {
				wp_safe_redirect( add_query_arg( 'reset_error', 'invalid_key', wp_get_referer() ) );
				exit;
			}

			$password = isset( $_POST['pass1'] ) ? sanitize_text_field( wp_unslash( $_POST['pass1'] ) ) : '';
			$confirm  = isset( $_POST['pass2'] ) ? sanitize_text_field( wp_unslash( $_POST['pass2'] ) ) : '';

			if ( $password !== $confirm ) {
				wp_safe_redirect( add_query_arg( 'reset_error', 'mismatch', wp_get_referer() ) );
				exit;
			}

			if ( strlen( $password ) < 8 ||
				! preg_match( '/[a-z]/', $password ) ||
				! preg_match( '/[A-Z]/', $password ) ||
				! preg_match( '/[0-9]/', $password ) ) {
				wp_safe_redirect( add_query_arg( 'reset_error', 'weak', wp_get_referer() ) );
				exit;
			}

			reset_password( $user, $password );
			wp_safe_redirect( home_url( '/portal-login/?reset=success' ) );
			exit;
		}
	}
);

add_action(
	'init',
	function () {
		if ( isset( $_POST['cb_login_submit'] ) ) {

			if ( ! empty( $_POST['your_name'] ) ) {
				// Honeypot triggered.
				return;
			}

			if (
				! isset( $_POST['cb_login_nonce'] )
				|| ! wp_verify_nonce( wp_unslash( $_POST['cb_login_nonce'] ), 'cb_login_action' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			) {
				wp_safe_redirect( add_query_arg( 'login', 'invalid_nonce', wp_get_referer() ) );
				exit;
			}

			$credentials = array(
				'user_login'    => isset( $_POST['log'] ) ? sanitize_user( wp_unslash( $_POST['log'] ) ) : '',
				'user_password' => isset( $_POST['pwd'] ) ? sanitize_text_field( wp_unslash( $_POST['pwd'] ) ) : '',
				'remember'      => true,
			);

			$user = wp_signon( $credentials );

			if ( is_wp_error( $user ) ) {
				wp_safe_redirect( add_query_arg( 'login', 'failed', wp_get_referer() ) );
				exit;
			}

			wp_safe_redirect( home_url( '/portal-dashboard/' ) );
			exit;
		}
	}
);

/* Increase link timeout from 24 hours to 7 days. */
add_filter(
	'password_reset_expiration',
	function () {
		return WEEK_IN_SECONDS; // 604800 seconds = 7 days
	}
);

/**
 * Notification for user registration.
 *
 * This function customizes the email sent to new users upon registration.
 * It includes a password reset link and instructions for setting their password.
 *
 * @param array   $wp_new_user_notification_email The email parameters.
 * @param WP_User $user The user object for the newly registered user.
 * @param string  $blogname The name of the site.
 * @return array  Modified email parameters.
 */
function cb_custom_new_user_email( $wp_new_user_notification_email, $user, $blogname ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$key = get_password_reset_key( $user );

	$reset_url = add_query_arg(
		array(
			'key'   => $key,
			'login' => rawurlencode( $user->user_login ),
		),
		home_url( '/reset-password/' )
	);

	$wp_new_user_notification_email['subject'] = 'Welcome to the Arcus Client Portal';

	$wp_new_user_notification_email['headers'] = array( 'Content-Type: text/plain; charset=UTF-8' );

	$wp_new_user_notification_email['message']  = "Username: {$user->user_login},\n\n";
	$wp_new_user_notification_email['message'] .= "An account has been created for you on the Arcus client portal.\n\n";
	$wp_new_user_notification_email['message'] .= "To set your password, visit the following address:\n\n";
	$wp_new_user_notification_email['message'] .= $reset_url . "\n\n";
	$wp_new_user_notification_email['message'] .= "To log in to the Arcus client portal, visit the following address:\n\n";
	$wp_new_user_notification_email['message'] .= home_url() . "\n\n";
	$wp_new_user_notification_email['message'] .= 'If you were not expecting this, you can safely ignore this email.';

	return $wp_new_user_notification_email;
}

add_filter( 'wp_new_user_notification_email', 'cb_custom_new_user_email', 10, 3 );
