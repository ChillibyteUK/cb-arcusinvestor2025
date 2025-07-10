<?php
/**
 * File: cb-docrepo-admin.php
 * Description: Contains admin-related functionalities for the CB Arcus Investor 2025 theme.
 * Author: Chillibyte - DS
 * Version: 1.0.0
 *
 * @package CB_ArcusInvestor2025
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended
// phpcs:disable WordPress.Security.NonceVerification.Missing

/**
 * Set custom 'docstatus' field for attachments uploaded by doceditor and docadmin.
 *
 * @param int $post_id The ID of the uploaded attachment.
 */
function cb_set_docstatus_based_on_role( $post_id ) {
    // Get the current user.
    $current_user = wp_get_current_user();

    // Check if the uploaded post is an attachment.
    if ( get_post_type( $post_id ) === 'attachment' ) {
        // Set 'docstatus' based on user role.
        if ( in_array( 'doceditor', (array) $current_user->roles, true ) ) {
            update_post_meta( $post_id, 'docstatus', 'pending' );
        } elseif ( in_array( 'docadmin', (array) $current_user->roles, true ) ) {
            update_post_meta( $post_id, 'docstatus', 'approved' );
        } else {
			update_post_meta( $post_id, 'docstatus', 'pending' );
		}

		// Retrieve folder name.
        global $wpdb;

		$cache_key   = "folder_name_{$post_id}";
        $folder_name = wp_cache_get( $cache_key );

        if ( false === $folder_name ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $folder_name = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT rml.name 
                    FROM {$wpdb->prefix}realmedialibrary_posts rml_posts
                    LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
                    WHERE rml_posts.attachment = %d LIMIT 1",
                    $post_id
                )
            );
            wp_cache_set( $cache_key, $folder_name );
        }

        // Log the upload action.
        $file_title = get_the_title( $post_id );
        $file_name  = basename( get_attached_file( $post_id ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $wpdb->prefix . 'cb_download_log',
            array(
                'user_id'       => $current_user->ID,
                'attachment_id' => $post_id,
                'file_title'    => $file_title,
                'file_name'     => $file_name,
                'folder_name'   => $folder_name,
                'action'        => 'upload',
            )
        );
    }
}
add_action( 'add_attachment', 'cb_set_docstatus_based_on_role' );

/**
 * Log file deletions.
 *
 * @param int $post_id The ID of the deleted attachment.
 */
function cb_log_file_deletion( $post_id ) {
    if ( get_post_type( $post_id ) === 'attachment' ) {
        global $wpdb;

        $current_user = wp_get_current_user();
        $file_title   = get_the_title( $post_id );
        $file_name    = basename( get_attached_file( $post_id ) );

		// Retrieve folder name.
		$cache_key   = "folder_name_{$post_id}";
        $folder_name = wp_cache_get( $cache_key );

		if ( false === $folder_name ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$folder_name = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT rml.name 
					FROM {$wpdb->prefix}realmedialibrary_posts rml_posts
					LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
					WHERE rml_posts.attachment = %d LIMIT 1",
					$post_id
				)
			);
			wp_cache_set( $cache_key, $folder_name );
		}

        // Log the deletion action.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $wpdb->prefix . 'cb_download_log',
            array(
                'user_id'       => $current_user->ID,
                'attachment_id' => $post_id,
                'file_title'    => $file_title,
                'file_name'     => $file_name,
                'folder_name'   => $folder_name,
                'action'        => 'delete',
            )
        );
    }
}
add_action( 'delete_attachment', 'cb_log_file_deletion' );

/**
 * Add 'docstatus' column to the Media Library.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function cb_add_docstatus_column_to_media_library( $columns ) {
    $columns['docstatus'] = __( 'Document Status', 'cb-arcusinvestor2025' );
    return $columns;
}
add_filter( 'manage_upload_columns', 'cb_add_docstatus_column_to_media_library' );

/**
 * Populate the 'docstatus' column in the Media Library.
 *
 * @param string $column_name Column name.
 * @param int    $post_id     Post ID.
 */
function cb_display_docstatus_in_media_library( $column_name, $post_id ) {
    if ( 'docstatus' === $column_name ) {
        $docstatus = get_post_meta( $post_id, 'docstatus', true );
        echo esc_html( ucfirst( $docstatus ) );
    }
}
add_action( 'manage_media_custom_column', 'cb_display_docstatus_in_media_library', 10, 2 );

/**
 * Add bulk action to toggle 'docstatus' in the Media Library.
 *
 * @param array $bulk_actions Existing bulk actions.
 * @return array Modified bulk actions.
 */
function cb_add_bulk_toggle_docstatus_action( $bulk_actions ) {
    $bulk_actions['toggle_docstatus'] = __( 'Toggle Document Status', 'cb-arcusinvestor2025' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-upload', 'cb_add_bulk_toggle_docstatus_action' );

/**
 * Handle the bulk action to toggle 'docstatus'.
 *
 * @param string $redirect_url The redirect URL after the action.
 * @param string $action       The action name.
 * @param array  $post_ids     The IDs of the posts being acted upon.
 * @return string Modified redirect URL.
 */
function cb_handle_bulk_toggle_docstatus_action( $redirect_url, $action, $post_ids ) {
    if ( 'toggle_docstatus' === $action ) {
        foreach ( $post_ids as $post_id ) {
            $current_status = get_post_meta( $post_id, 'docstatus', true );
            $new_status     = ( 'pending' === $current_status ) ? 'approved' : 'pending';
            update_post_meta( $post_id, 'docstatus', $new_status );

            // Log the toggle action.
            global $wpdb;
			$file_title = get_the_title( $post_id );
            $file_name  = basename( get_attached_file( $post_id ) );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->insert(
                $wpdb->prefix . 'cb_download_log',
                array(
                    'user_id'       => get_current_user_id(),
                    'attachment_id' => $post_id,
					'file_title'    => $file_title,
                    'file_name'     => $file_name,
                    'action'        => 'toggle_docstatus',
                )
            );
        }

        $redirect_url = add_query_arg( 'bulk_docstatus_toggled', count( $post_ids ), $redirect_url );
    }

    return $redirect_url;
}
add_filter( 'handle_bulk_actions-upload', 'cb_handle_bulk_toggle_docstatus_action', 10, 3 );

/**
 * Display admin notice after bulk action.
 */
function cb_bulk_toggle_docstatus_admin_notice() {
    if ( ! empty( $_GET['bulk_docstatus_toggled'] ) ) {
        $count = intval( $_GET['bulk_docstatus_toggled'] );
        printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( sprintf( '%d document statuses toggled.', $count ) ) );
    }
}
add_action( 'admin_notices', 'cb_bulk_toggle_docstatus_admin_notice' );

/**
 * Add custom roles: docadmin and doceditor.
 */
function cb_add_custom_roles() {
    // Add docadmin role.
    add_role(
        'docadmin',
        'Document Admin',
        array_merge(
            get_role( 'editor' )->capabilities,
            array(
                'upload_files'      => true,
                'publish_posts'     => true,
                'edit_others_posts' => true,
				'create_users'      => true,
				'edit_users'        => true,
				'delete_users'      => true,
				'list_users'        => true,
				'promote_users'     => true,
            )
        )
    );

    // Add doceditor role.
	remove_role( 'doceditor' );
    add_role(
        'doceditor',
        'Document Editor',
        array_merge(
            get_role( 'editor' )->capabilities,
            array(
                'upload_files'      => true,
                'edit_posts'        => true,
                'edit_others_posts' => true,
                'publish_posts'     => false,
            )
        )
    );
}
add_action( 'init', 'cb_add_custom_roles' );


/*
 * Hide the unused roles from the user interface.
 */
add_filter(
	'editable_roles',
	function ( $roles ) {
		$allowed = array( 'administrator', 'docadmin', 'doceditor', 'portal_user' );
		foreach ( array_keys( $roles ) as $role ) {
			if ( ! in_array( $role, $allowed, true ) ) {
				unset( $roles[ $role ] );
			}
		}
		return $roles;
	}
);

/**
 * Restrict admin menu items and set default dashboard for doceditor role.
 */
function cb_restrict_docroles_admin_menu() {
    // Get the current user.
    $current_user = wp_get_current_user();

    if ( in_array( 'doceditor', (array) $current_user->roles, true ) ) {
        // Remove specific menu items.
        remove_menu_page( 'edit.php?post_type=page' ); // Pages.
        remove_menu_page( 'wpcf7' ); // Contact Form 7.
        remove_menu_page( 'index.php' ); // Dashboard.
        remove_menu_page( 'tools.php' ); // Tools.
		remove_menu_page( 'admin.php?page=_stack_cache_admin' );
    }

	if ( in_array( 'docadmin', (array) $current_user->roles, true ) ) {
        // Remove specific menu items.
        remove_menu_page( 'edit.php?post_type=page' ); // Pages.
        remove_menu_page( 'wpcf7' ); // Contact Form 7.
        remove_menu_page( 'index.php' ); // Dashboard.
		remove_menu_page( 'tools.php' ); // Tools.
		remove_menu_page( 'admin.php?page=_stack_cache_admin' );
    }
}
add_action( 'admin_menu', 'cb_restrict_docroles_admin_menu', 999 );

/**
 * Update capabilities for the 'docadmin' role.
 */
function cb_update_docadmin_capabilities() {
    $role = get_role( 'docadmin' );

    if ( $role ) {
        $role->add_cap( 'create_users' );
        $role->add_cap( 'edit_users' );
        $role->add_cap( 'delete_users' );
        $role->add_cap( 'list_users' );
        $role->add_cap( 'promote_users' );
    }
}
add_action( 'init', 'cb_update_docadmin_capabilities' );

/**
 * Redirect doceditor users to the Document Management dashboard by default.
 */
function cb_redirect_docroles_dashboard() {
    // Get the current user.
    $current_user = wp_get_current_user();

    // Check if the user has the 'doceditor' role and is on the default dashboard.
    if ( in_array( 'doceditor', (array) $current_user->roles, true ) && is_admin() && 'index.php' === $GLOBALS['pagenow'] ) {
        wp_safe_redirect( admin_url( 'upload.php' ) );
        exit;
    }
    // Check if the user has the 'docadmin' role and is on the default dashboard.
    if ( in_array( 'docadmin', (array) $current_user->roles, true ) && is_admin() && 'index.php' === $GLOBALS['pagenow'] ) {
        wp_safe_redirect( admin_url( 'admin.php?page=cb-document-management' ) );
        exit;
    }
}
add_action( 'admin_init', 'cb_redirect_docroles_dashboard' );

// ======== Document Management Dashboard ======== //

/**
 * Enqueue admin styles and scripts for the Document Management page.
 */
function cb_enqueue_admin_styles_scripts() {
	$screen = get_current_screen();
	if ( $screen && 'toplevel_page_cb-document-management' === $screen->id ) {
		wp_enqueue_style( 'bootstrap-5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0' );
		wp_enqueue_script( 'bootstrap-5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.0', true );
	}
}
add_action( 'admin_enqueue_scripts', 'cb_enqueue_admin_styles_scripts' );


/**
 * Register the Document Management page.
 */
function cb_register_document_management_page() {
    add_menu_page(
        'Document Management',
        'Document Management',
        'publish_posts',
        'cb-document-management',
        'cb_render_document_management_page',
        'dashicons-clipboard',
        26
    );
}
add_action( 'admin_menu', 'cb_register_document_management_page' );

/**
 * Render the Document Management page.
 */
function cb_render_document_management_page() {
    // Check if we have verification results to determine active tab.
    $has_verification_results = isset( $_SESSION['cb_verification_result'] );
    $has_pdf_report_results   = isset( $_SESSION['cb_pdf_report_result'] );
    ?>
    <div class="wrap">
        <h1 class="mb-4">Document Management</h1>
        <ul class="nav nav-tabs">
            <li class="nav-item mb-0">
                <a class="nav-link <?php echo ( ! $has_verification_results && ! $has_pdf_report_results ) ? 'active' : ''; ?>" href="#pending-approvals" data-bs-toggle="tab">Pending Approvals</a>
            </li>
            <li class="nav-item mb-0">
                <a class="nav-link" href="#logs" data-bs-toggle="tab">Logs</a>
            </li>
            <!-- <li class="nav-item mb-0">
                <a class="nav-link" href="#summary" data-bs-toggle="tab">Summary</a>
            </li> -->
            <li class="nav-item mb-0">
                <a class="nav-link <?php echo ( $has_verification_results && ! $has_pdf_report_results ) ? 'active' : ''; ?>" href="#watermark-verification" data-bs-toggle="tab">Watermark Verification</a>
            </li>
            <li class="nav-item mb-0">
                <a class="nav-link <?php echo $has_pdf_report_results ? 'active' : ''; ?>" href="#pdf-report" data-bs-toggle="tab">PDF Report</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade <?php echo ( ! $has_verification_results && ! $has_pdf_report_results ) ? 'show active' : ''; ?>" id="pending-approvals">
                <?php cb_render_pending_approvals(); ?>
            </div>
            <div class="tab-pane fade" id="logs">
                <?php cb_render_logs(); ?>
            </div>
            <!-- <div class="tab-pane fade" id="summary">
                <?php cb_render_summary(); ?>
            </div> -->
            <div class="tab-pane fade <?php echo ( $has_verification_results && ! $has_pdf_report_results ) ? 'show active' : ''; ?>" id="watermark-verification">
                <?php cb_render_watermark_verification(); ?>
            </div>
            <div class="tab-pane fade <?php echo $has_pdf_report_results ? 'show active' : ''; ?>" id="pdf-report">
                <?php cb_render_pdf_report(); ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render the Pending Approvals tab.
 */
function cb_render_pending_approvals() {
    global $wpdb;

    // Query for documents with 'pending' status.
    $cache_key    = 'pending_docs_cache';
    $pending_docs = wp_cache_get( $cache_key );

    if ( false === $pending_docs ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $pending_docs = $wpdb->get_results(
            "
            SELECT p.ID, p.post_title, p.post_date, u.display_name, rml.name AS folder_name
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->users} u ON p.post_author = u.ID
            LEFT JOIN {$wpdb->prefix}realmedialibrary_posts rml_posts ON p.ID = rml_posts.attachment
            LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
            WHERE EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm
                WHERE pm.post_id = p.ID AND pm.meta_key = 'docstatus' AND pm.meta_value = 'pending'
            )
            ORDER BY p.post_date DESC
            ",
            ARRAY_A
        );
        wp_cache_set( $cache_key, $pending_docs, '', 3600 ); // Cache for 1 hour.
    }

    ?>
    <div class="pending-approvals pt-4">
        <h3>Pending Approvals</h3>
        <?php
		if ( empty( $pending_docs ) ) {
			?>
            <p>No documents are pending approval.</p>
        	<?php
		} else {
			?>
            <form method="post">
                <table class="table table-striped table-hover table-sm small">
                    <thead class="table-dark">
                        <tr>
                            <th>Upload Date</th>
                            <th>Folder</th>
                            <th>Title</th>
                            <th>Uploaded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $pending_docs as $doc ) : ?>
                            <tr>
                                <td><?php echo esc_html( $doc['post_date'] ); ?></td>
                                <td><?php echo esc_html( $doc['folder_name'] ?? 'Uncategorized' ); ?></td>
                                <td><?php echo esc_html( $doc['post_title'] ); ?></td>
                                <td><?php echo esc_html( $doc['display_name'] ); ?></td>
                                <td>
                                    <button name="cb_approve_doc" value="<?php echo esc_attr( $doc['ID'] ); ?>" class="btn btn-success btn-sm">Approve</button>
                                    <button name="cb_reject_doc" value="<?php echo esc_attr( $doc['ID'] ); ?>" class="btn btn-danger btn-sm">Reject</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        	<?php
		}
		?>
    </div>
    <?php
}

/**
 * Render the Logs tab.
 */
function cb_render_logs() {
	global $wpdb;

	// Simple query - get all logs without server-side filtering
	$cache_key = 'recent_logs_cache';
	$logs      = wp_cache_get( $cache_key );

	if ( false === $logs ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$logs = $wpdb->get_results(
			"
			SELECT l.user_id, u.display_name, l.folder_name, l.file_title, l.file_name, l.action, l.notes, l.timestamp
			FROM {$wpdb->prefix}cb_download_log l
			LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
			ORDER BY l.timestamp DESC
			LIMIT 100
			",
			ARRAY_A
		);
		wp_cache_set( $cache_key, $logs, '', 1800 ); // Cache for 30 minutes.
	}

	// Get unique actions for checkboxes
	$all_actions = array();
	foreach ( $logs as $log ) {
		if ( ! in_array( $log['action'], $all_actions, true ) ) {
			$all_actions[] = $log['action'];
		}
	}
	sort( $all_actions );

	?>
	<div class="logs pt-4">
		<h3>Logs</h3>

		<!-- Export Forms -->
		<form method="post">
			<div class="row mb-4">
				<div class="col-md-4">
					<button name="cb_export_file_access" class="btn btn-secondary btn-sm">Export File Access Log</button>
				</div>
				<div class="col-md-4">
					<button name="cb_export_logins" class="btn btn-secondary btn-sm">Export Login Activity</button>
				</div>
				<div class="col-md-4">
					<div class="row d-flex align-items-center">
						<div class="col-md-6">
							<select name="cb_user_id" class="form-select">
								<option value="">Select User</option>
								<?php
								foreach ( get_users( array( 'role__in' => array( 'portal_user', 'docadmin', 'doceditor' ) ) ) as $user ) {
									echo '<option value="' . esc_attr( $user->ID ) . '">' . esc_html( $user->display_name ) . '</option>';
								}
								?>
							</select>
						</div>
						<div class="col-md-6">
							<button name="cb_export_user_activity" class="btn btn-secondary btn-sm">Export User Activity</button>
						</div>
					</div>
				</div>
			</div>
		</form>

		<!-- Filter by Action -->
		<div class="mb-4">
			
			<div class="d-flex flex-wrap gap-4 mb-3">
				<strong>Filter by Action:</strong>
				<div class="form-check d-flex align-items-center">
					<input class="form-check-input" type="checkbox" id="show-all" checked>
					<label class="form-check-label fw-bold" for="show-all">
						Show All
					</label>
				</div>

				<?php foreach ( $all_actions as $action ) : ?>
				<div class="form-check d-flex align-items-center gap-2">
					<input class="form-check-input action-filter" 
						type="checkbox" 
						value="<?php echo esc_attr( $action ); ?>" 
						id="filter-<?php echo esc_attr( $action ); ?>">
					<label class="form-check-label" for="filter-<?php echo esc_attr( $action ); ?>">
						<?php echo esc_html( ucwords( str_replace( '_', ' ', $action ) ) ); ?>
					</label>
				</div>
				<?php endforeach; ?>
			</div>
			
			<!-- div>
				<span class="text-muted" id="filter-status">Showing all entries</span>
			</div -->
		</div>

		<?php if ( empty( $logs ) ) : ?>
			<div class="alert alert-info">
				<p class="mb-0">No logs available.</p>
			</div>
		<?php else : ?>
			<h4>Recent Activity</h4>
			<table class="table table-striped table-hover table-sm small" id="logs-table">
				<thead class="table-dark">
					<tr>
						<th>Timestamp</th>
						<th>User</th>
						<th>Action</th>
						<th>Folder</th>
						<th>File Name</th>
						<th>Notes</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $logs as $log ) : ?>
						<tr data-action="<?php echo esc_attr( $log['action'] ); ?>">
							<td><?php echo esc_html( $log['timestamp'] ); ?></td>
							<td><?php echo esc_html( $log['display_name'] ); ?></td>
							<td><?php echo esc_html( ucwords( str_replace( '_', ' ', $log['action'] ) ) ); ?></td>
							<td><?php echo esc_html( $log['folder_name'] ?? 'N/A' ); ?></td>
							<td><?php echo esc_html( $log['file_name'] ?? 'N/A' ); ?></td>
							<td><?php echo esc_html( $log['notes'] ?? 'N/A' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const showAllCheckbox = document.getElementById('show-all');
		const actionCheckboxes = document.querySelectorAll('.action-filter');
		const tableRows = document.querySelectorAll('#logs-table tbody tr');
		const statusSpan = document.getElementById('filter-status');

		function updateTable() {
			let visibleCount = 0;
			const checkedActions = [];
			
			// Get checked actions
			actionCheckboxes.forEach(checkbox => {
				if (checkbox.checked) {
					checkedActions.push(checkbox.value);
				}
			});

			// Show/hide rows based on filter state
			tableRows.forEach(row => {
				const action = row.getAttribute('data-action');
				
				// If "Show All" is checked or no specific actions are selected, show all rows
				if (showAllCheckbox.checked || checkedActions.length === 0) {
					row.style.display = '';
					visibleCount++;
				} else if (checkedActions.includes(action)) {
					// Show only rows matching selected actions
					row.style.display = '';
					visibleCount++;
				} else {
					row.style.display = 'none';
				}
			});

			// Update status
			if (showAllCheckbox.checked || checkedActions.length === 0) {
				statusSpan.textContent = 'Showing all entries (' + visibleCount + ')';
			} else {
				statusSpan.textContent = 'Showing ' + visibleCount + ' entries for: ' + 
					checkedActions.map(a => a.replace(/_/g, ' ')).join(', ');
			}
		}

		// Show all checkbox
		showAllCheckbox.addEventListener('change', function() {
			if (showAllCheckbox.checked) {
				// When "Show All" is checked, uncheck all individual filters
				actionCheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
			}
			updateTable();
		});

		// Individual action checkboxes
		actionCheckboxes.forEach(checkbox => {
			checkbox.addEventListener('change', function() {
				if (checkbox.checked) {
					// When any individual action is checked, uncheck "Show All"
					showAllCheckbox.checked = false;
				} else {
					// If no individual actions are checked, check "Show All"
					const anyChecked = Array.from(actionCheckboxes).some(cb => cb.checked);
					if (!anyChecked) {
						showAllCheckbox.checked = true;
					}
				}
				
				updateTable();
			});
		});

		// Initial update
		updateTable();
	});
	</script>
	<?php
}

/**
 * Render the Pending Approvals tab.
 */
function cb_render_summary() {
    global $wpdb;

    // Fetch counts for summary.
    $cache_key     = 'pending_count_cache';
    $pending_count = wp_cache_get( $cache_key );

    if ( false === $pending_count ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$pending_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'docstatus' AND pm.meta_value = 'pending'"
        );
        wp_cache_set( $cache_key, $pending_count, '', 3600 ); // Cache for 1 hour.
    }

	$cache_key      = 'recent_uploads_cache';
    $recent_uploads = wp_cache_get( $cache_key );

    if ( false === $recent_uploads ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    	$recent_uploads = $wpdb->get_var(
        	"SELECT COUNT(*) FROM {$wpdb->posts} p
        	WHERE p.post_type = 'attachment' AND p.post_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
    	);
		wp_cache_set( $cache_key, $recent_uploads, '', 3600 ); // Cache for 1 hour.
	}

	$cache_key             = 'recent_folder_actions_cache';
	$recent_folder_actions = wp_cache_get( $cache_key );

	if ( false === $recent_folder_actions ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$recent_folder_actions = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}cb_download_log
			WHERE action IN ('folder_create', 'folder_rename', 'folder_delete', 'folder_change') AND timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
		);
		wp_cache_set( $cache_key, $recent_folder_actions, '', 3600 ); // Cache for 1 hour.
	}

    ?>
    <div class="summary">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Approvals</h5>
                        <p class="card-text"><?php echo esc_html( $pending_count ); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Uploads</h5>
                        <p class="card-text"><?php echo esc_html( $recent_uploads ); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Folder Actions</h5>
                        <p class="card-text"><?php echo esc_html( $recent_folder_actions ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}


/**
 * Handle document approval/rejection actions.
 */
function cb_handle_document_actions() {
    global $wpdb;

    if ( isset( $_POST['cb_approve_doc'] ) ) {
        $doc_id = intval( $_POST['cb_approve_doc'] );
        update_post_meta( $doc_id, 'docstatus', 'approved' );

        // Retrieve file details.
        $file_title = get_the_title( $doc_id );
        $file_name  = basename( get_attached_file( $doc_id ) );

        // Retrieve folder name.
		$cache_key   = "folder_name_{$doc_id}";
		$folder_name = wp_cache_get( $cache_key );

		if ( false === $folder_name ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$folder_name = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT rml.name 
					FROM {$wpdb->prefix}realmedialibrary_posts rml_posts
					LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
					WHERE rml_posts.attachment = %d LIMIT 1",
					$doc_id
				)
			);
			wp_cache_set( $cache_key, $folder_name );
		}

        // Log the approval action.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $wpdb->prefix . 'cb_download_log',
            array(
                'user_id'       => get_current_user_id(),
                'attachment_id' => $doc_id,
                'file_title'    => $file_title,
                'file_name'     => $file_name,
                'folder_name'   => $folder_name,
                'action'        => 'approved',
            )
        );

        wp_safe_redirect( admin_url( 'admin.php?page=cb-document-management' ) );
        exit;
    }

    if ( isset( $_POST['cb_reject_doc'] ) ) {
        $doc_id = intval( $_POST['cb_reject_doc'] );

        // Retrieve file details.
        $file_title = get_the_title( $doc_id );
        $file_name  = basename( get_attached_file( $doc_id ) );

        // Retrieve folder name.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $folder_name = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT rml.name 
                FROM {$wpdb->prefix}realmedialibrary_posts rml_posts
                LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
                WHERE rml_posts.attachment = %d LIMIT 1",
                $doc_id
            )
        );

        // Log the rejection action.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->insert(
            $wpdb->prefix . 'cb_download_log',
            array(
                'user_id'       => get_current_user_id(),
                'attachment_id' => $doc_id,
                'file_title'    => $file_title,
                'file_name'     => $file_name,
                'folder_name'   => $folder_name,
                'action'        => 'rejected',
            )
        );

        wp_delete_post( $doc_id );

        wp_safe_redirect( admin_url( 'admin.php?page=cb-document-management' ) );
        exit;
    }
}
add_action( 'admin_init', 'cb_handle_document_actions' );

/**
 * Render the Watermark Verification tab.
 */
function cb_render_watermark_verification() {
    ?>
    <div class="watermark-verification pt-4">
        <h3>Watermark Verification</h3>
        <p>Upload a PDF file to verify if it contains a valid watermark and matches our download logs.</p>
        
        <form method="post" enctype="multipart/form-data" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="cb_verify_file" class="form-label">Select PDF File</label>
                    <input type="file" class="form-control" id="cb_verify_file" name="cb_verify_file" accept=".pdf" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" name="cb_verify_watermark" class="btn btn-primary">Verify Watermark</button>
                </div>
            </div>
            <?php wp_nonce_field( 'cb_verify_watermark_nonce', 'cb_verify_nonce' ); ?>
        </form>

        <?php
        // Display verification results if available.
        if ( isset( $_SESSION['cb_verification_result'] ) ) {
            $result = $_SESSION['cb_verification_result'];
            unset( $_SESSION['cb_verification_result'] );

            if ( 'success' === $result['status'] ) {
                ?>
                <div class="alert alert-success">
                    <h5>Watermark Verification - SUCCESS</h5>
                    <p><strong>Watermark Found:</strong> <?php echo esc_html( $result['watermark'] ); ?></p>
                    <p><strong>UUID Source:</strong> <?php echo esc_html( ucfirst( $result['uuid_source'] ) ); ?></p>
                    <p><strong>UUID:</strong> <?php echo esc_html( $result['uuid'] ); ?></p>
                    <p><strong>Download Log Match:</strong> Yes</p>
                    <p><strong>User:</strong> <?php echo esc_html( $result['user_name'] ); ?></p>
                    <p><strong>Download Date:</strong> <?php echo esc_html( $result['download_date'] ); ?></p>
                    <p><strong>File:</strong> <?php echo esc_html( $result['file_title'] ); ?></p>
                </div>
                <?php
            } elseif ( 'not_found' === $result['status'] ) {
                ?>
                <div class="alert alert-warning">
                    <h5>Watermark Verification - NOT FOUND</h5>
                    <p><strong>Watermark Found:</strong> <?php echo esc_html( $result['watermark'] ); ?></p>
                    <p><strong>UUID Source:</strong> <?php echo esc_html( ucfirst( $result['uuid_source'] ) ); ?></p>
                    <p><strong>UUID:</strong> <?php echo esc_html( $result['uuid'] ); ?></p>
                    <p><strong>Download Log Match:</strong> No matching record found in download logs</p>
                    <p>This watermark does not correspond to any logged download event.</p>
                </div>
                <?php
            } elseif ( 'no_watermark' === $result['status'] ) {
                ?>
                <div class="alert alert-danger">
                    <h5>Watermark Verification - NO UUID FOUND</h5>
                    <?php if ( isset( $result['watermark'] ) && 'N/A' !== $result['watermark'] ) : ?>
                        <p><strong>Watermark Found:</strong> <?php echo esc_html( $result['watermark'] ); ?></p>
                        <p>Watermark metadata was found but does not contain a valid UUID.</p>
                    <?php else : ?>
                        <p>No watermark metadata found in this PDF file.</p>
                    <?php endif; ?>
                    <p>Also checked filename for UUID pattern - none found.</p>
                    <p>This file either wasn't downloaded through our system or the watermark/filename has been modified.</p>
                </div>
                <?php
            } else {
                ?>
                <div class="alert alert-danger">
                    <h5>Watermark Verification - ERROR</h5>
                    <p><?php echo esc_html( $result['message'] ); ?></p>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
}

/**
 * Handle watermark verification upload and processing.
 */
function cb_handle_watermark_verification() {
    if ( isset( $_POST['cb_verify_watermark'] ) && isset( $_POST['cb_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cb_verify_nonce'] ) ), 'cb_verify_watermark_nonce' ) ) {

        if ( ! isset( $_FILES['cb_verify_file'] ) || ! isset( $_FILES['cb_verify_file']['error'] ) || UPLOAD_ERR_OK !== $_FILES['cb_verify_file']['error'] ) {
            $_SESSION['cb_verification_result'] = array(
                'status'  => 'error',
                'message' => 'File upload failed.',
            );
            return;
        }

        $uploaded_file = $_FILES['cb_verify_file'];

        // Validate file type.
        if ( 'application/pdf' !== $uploaded_file['type'] ) {
            $_SESSION['cb_verification_result'] = array(
                'status'  => 'error',
                'message' => 'Only PDF files are allowed.',
            );
            return;
        }

        // Extract watermark from PDF.
        $watermark_data = cb_extract_pdf_watermark( $uploaded_file['tmp_name'] );
        $uuid           = null;
        $uuid_source    = 'none';

        // First, try to extract UUID from watermark metadata.
        if ( $watermark_data ) {
            $uuid = cb_extract_uuid_from_watermark( $watermark_data );
            if ( $uuid ) {
                $uuid_source = 'watermark';
            }
        }

        // If no UUID from watermark, try to extract from filename.
        if ( ! $uuid ) {
            $uuid = cb_extract_uuid_from_filename( $uploaded_file['name'] );
            if ( $uuid ) {
                $uuid_source = 'filename';
            }
        }

        // If no UUID found at all, return appropriate status.
        if ( ! $uuid ) {
            if ( $watermark_data ) {
                $_SESSION['cb_verification_result'] = array(
                    'status'    => 'no_watermark',
                    'watermark' => $watermark_data,
                );
            } else {
                $_SESSION['cb_verification_result'] = array(
                    'status' => 'no_watermark',
                );
            }
            return;
        }

        // Check download log for matching UUID.
        $log_entry = cb_find_download_log_by_uuid( $uuid );

        if ( $log_entry ) {
            $_SESSION['cb_verification_result'] = array(
                'status'        => 'success',
                'watermark'     => $watermark_data ? $watermark_data : 'N/A',
                'uuid'          => $uuid,
                'uuid_source'   => $uuid_source,
                'user_name'     => $log_entry['display_name'],
                'download_date' => $log_entry['timestamp'],
                'file_title'    => $log_entry['file_title'],
            );
        } else {
            $_SESSION['cb_verification_result'] = array(
                'status'      => 'not_found',
                'watermark'   => $watermark_data ? $watermark_data : 'N/A',
                'uuid'        => $uuid,
                'uuid_source' => $uuid_source,
            );
        }
    }
}
add_action( 'admin_init', 'cb_handle_watermark_verification' );

/**
 * Extract watermark metadata from PDF file.
 *
 * @param string $file_path Path to the PDF file.
 * @return string|false Watermark string or false if not found.
 */
function cb_extract_pdf_watermark( $file_path ) {
	// Use the same metadata extraction approach that's already working.
	$metadata = cb_extract_pdf_metadata( $file_path );
	
	// Check if the Keywords field contains our watermark format.
	if ( ! empty( $metadata['keywords'] ) && strpos( $metadata['keywords'], 'Downloaded by ' ) !== false ) {
		return $metadata['keywords'];
	}
	
	return false;
}

/**
 * Extract UUID from watermark string.
 *
 * @param string $watermark The watermark string.
 * @return string|false UUID or false if not found.
 */
function cb_extract_uuid_from_watermark( $watermark ) {
    // The watermark format is: "Downloaded by {name} | {email} | {date} | {uuid}".
    $parts = explode( ' | ', $watermark );

    if ( count( $parts ) >= 4 ) {
        return trim( $parts[3] );
    }

    return false;
}

/**
 * Extract UUID from filename.
 *
 * @param string $filename The filename to extract UUID from.
 * @return string|false UUID or false if not found.
 */
function cb_extract_uuid_from_filename( $filename ) {
    // Expected filename format: {original_name}_{UUID}.{extension}.
    // Extract the UUID from the filename before the extension.
    $filename_parts = pathinfo( $filename );
    $basename       = $filename_parts['filename']; // Filename without extension.
    
    // Look for pattern: anything_UUID where UUID is 36 characters (8-4-4-4-12).
    if ( preg_match( '/.*_([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/i', $basename, $matches ) ) {
        return $matches[1];
    }
    
    return false;
}

/**
 * Find download log entry by UUID.
 *
 * @param string $uuid The UUID to search for.
 * @return array|false Log entry or false if not found.
 */
function cb_find_download_log_by_uuid( $uuid ) {
    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT l.*, u.display_name 
             FROM {$wpdb->prefix}cb_download_log l
             LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
             WHERE l.notes = %s
             LIMIT 1",
            $uuid
        ),
        ARRAY_A
    );

    return $result ? $result : false;
}

/**
 * Initialize session for watermark verification.
 */
function cb_init_watermark_verification_session() {
    if ( ! session_id() ) {
        session_start();
    }
}
add_action( 'admin_init', 'cb_init_watermark_verification_session', 1 );

/**
 * Render the PDF Report tab.
 */
function cb_render_pdf_report() {
    ?>
    <div class="pdf-report pt-4">
        <h3>PDF Report</h3>
        <p>This report analyzes all PDF files in the uploads directory and displays their metadata.</p>
        
        <form method="post" class="mb-4">
            <button type="submit" name="cb_generate_pdf_report" class="btn btn-primary">Generate PDF Report</button>
            <?php wp_nonce_field( 'cb_pdf_report_nonce', 'cb_pdf_report_nonce' ); ?>
        </form>

        <?php
        // Display PDF report results if available.
        if ( isset( $_SESSION['cb_pdf_report_result'] ) ) {
            $report_data = $_SESSION['cb_pdf_report_result'];
            unset( $_SESSION['cb_pdf_report_result'] );

            if ( ! empty( $report_data ) ) {
                ?>
                <div class="alert alert-info">
                    <h5>PDF Report Generated</h5>
                    <p>Found <?php echo count( $report_data ); ?> PDF files in the uploads directory.</p>
                </div>
                
                <table class="table table-striped table-hover table-sm small">
                    <thead class="table-dark">
                        <tr>
                            <th>Filename</th>
                            <th>Author</th>
                            <th>Creator</th>
                            <th>Producer</th>
                            <th>File Size</th>
                            <th>Modified</th>
                            <th>Watermark</th>
                            <th>Metadata</th>
                            <th>Encrypted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $report_data as $pdf_info ) : ?>
                            <tr>
                                <td><?php echo esc_html( $pdf_info['filename'] ); ?></td>
                                <td><?php echo esc_html( $pdf_info['author'] ); ?></td>
                                <td><?php echo esc_html( $pdf_info['creator'] ); ?></td>
                                <td><?php echo esc_html( $pdf_info['producer'] ); ?></td>
                                <td><?php echo esc_html( $pdf_info['file_size'] ); ?></td>
                                <td><?php echo esc_html( $pdf_info['modified'] ); ?></td>
                                <td>
                                    <?php if ( $pdf_info['watermark_compatible'] ) : ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( $pdf_info['metadata_compatible'] ) : ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">No</span>
                                    <?php endif; ?>
                                </td>
								<td>
                                    <?php if ( isset( $pdf_info['encrypted'] ) && $pdf_info['encrypted'] ) : ?>
                                        <span class="badge bg-warning">Yes</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <form method="post" class="mt-3">
                    <button type="submit" name="cb_export_pdf_report" class="btn btn-secondary">Export to CSV</button>
                    <?php wp_nonce_field( 'cb_export_pdf_report_nonce', 'cb_export_pdf_report_nonce' ); ?>
                </form>
                <?php
            } else {
                ?>
                <div class="alert alert-warning">
                    <h5>No PDF Files Found</h5>
                    <p>No PDF files were found in the uploads directory.</p>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
}

/**
 * Handle PDF report generation.
 */
function cb_handle_pdf_report() {
    // Generate PDF report.
    if ( isset( $_POST['cb_generate_pdf_report'] ) && isset( $_POST['cb_pdf_report_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cb_pdf_report_nonce'] ) ), 'cb_pdf_report_nonce' ) ) {
        $pdf_data                         = cb_analyze_pdf_files();
        $_SESSION['cb_pdf_report_result'] = $pdf_data;

        // Store for CSV export.
        $_SESSION['cb_pdf_report_data'] = $pdf_data;
    }

    // Export PDF report to CSV.
    if ( isset( $_POST['cb_export_pdf_report'] ) && isset( $_POST['cb_export_pdf_report_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cb_export_pdf_report_nonce'] ) ), 'cb_export_pdf_report_nonce' ) ) {
        if ( isset( $_SESSION['cb_pdf_report_data'] ) ) {
            $csv_data = array();

            // Add headers.
            $csv_data[] = array(
                'Filename',
                'Author',
                'Creator',
                'Producer',
                'File Size',
                'Modified',
                'Encrypted',
                'Watermark',
                'Metadata',
            );

            // Add data rows.
            foreach ( $_SESSION['cb_pdf_report_data'] as $pdf_info ) {
                $csv_data[] = array(
                    $pdf_info['filename'],
                    $pdf_info['author'],
                    $pdf_info['creator'],
                    $pdf_info['producer'],
                    $pdf_info['file_size'],
                    $pdf_info['modified'],
                    ( isset( $pdf_info['encrypted'] ) && $pdf_info['encrypted'] ) ? 'Yes' : 'No',
                    $pdf_info['watermark_compatible'] ? 'Yes' : 'No',
                    $pdf_info['metadata_compatible'] ? 'Yes' : 'No',
                );
            }

            cb_export_csv( $csv_data, 'pdf-report-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv' );
        }
    }
}
add_action( 'admin_init', 'cb_handle_pdf_report' );

/**
 * Analyze all PDF files in the uploads directory.
 *
 * @return array Array of PDF file information.
 */
function cb_analyze_pdf_files() {
    $upload_dir = wp_upload_dir();
    $pdf_files  = array();

    // Get all PDF attachments from WordPress.
    $attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'application/pdf',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
		)
	);

	foreach ( $attachments as $attachment ) {
        $file_path = get_attached_file( $attachment->ID );

        if ( file_exists( $file_path ) ) {
            $pdf_info                         = cb_extract_pdf_metadata( $file_path );
            $pdf_info['filename']             = basename( $file_path );
            $pdf_info['file_size']            = size_format( filesize( $file_path ) );
            $pdf_info['modified']             = gmdate( 'Y-m-d H:i:s', filemtime( $file_path ) );
            $pdf_info['watermark_compatible'] = cb_test_pdf_watermark_compatibility( $file_path );
            $pdf_info['metadata_compatible']  = cb_test_pdf_metadata_compatibility( $file_path );

            $pdf_files[] = $pdf_info;
        }
    }

    return $pdf_files;
}

/**
 * Extract metadata from a PDF file.
 *
 * @param string $file_path Path to the PDF file.
 * @return array PDF metadata.
 */
function cb_extract_pdf_metadata( $file_path ) {
    $metadata = array(
        'title'     => 'N/A',
        'author'    => 'N/A',
        'creator'   => 'N/A',
        'producer'  => 'N/A',
        'keywords'  => 'N/A',
        'encrypted' => false,
    );

    try {
        require_once get_stylesheet_directory() . '/vendor/autoload.php';

        // Use FPDI to validate the PDF.
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->setSourceFile( $file_path );

        // Read the PDF content.
        $content = file_get_contents( $file_path );
        if ( false === $content ) {
            return $metadata;
        }

        // Extract metadata using improved methods.
        $metadata = cb_extract_pdf_metadata_improved( $content, $metadata );

    } catch ( Exception $e ) {
        // If FPDI fails, still try extraction.
        $content = file_get_contents( $file_path );
        if ( false !== $content ) {
            $metadata = cb_extract_pdf_metadata_improved( $content, $metadata );
        }
    }

    // Check if we failed to extract any meaningful metadata and file appears encrypted.
    if ( 'N/A' === $metadata['creator'] && 'N/A' === $metadata['producer'] && 'N/A' === $metadata['title'] && 'N/A' === $metadata['author'] ) {
        if ( cb_is_pdf_encrypted( $file_path ) ) {
            $metadata['encrypted'] = true;
            $metadata['creator']   = 'Encrypted';
            $metadata['producer']  = 'Encrypted';
        }
    }

    return $metadata;
}

/**
 * Improved PDF metadata extraction.
 *
 * @param string $content PDF file content.
 * @param array  $metadata Initial metadata array.
 * @return array Updated metadata array.
 */
function cb_extract_pdf_metadata_improved( $content, $metadata ) {
    // Step 1: Extract XMP metadata (most reliable).
    $metadata = cb_extract_xmp_metadata( $content, $metadata );

    // Step 2: Find and parse PDF Info objects.
    $metadata = cb_parse_pdf_info_objects( $content, $metadata );

    // Step 3: Search for direct metadata patterns in various encodings.
    $metadata = cb_extract_metadata_patterns( $content, $metadata );

    // Step 4: Look for known application signatures.
    $metadata = cb_extract_application_signatures( $content, $metadata );

    // Step 5: Handle metadata in encrypted or compressed PDF streams.
    $metadata = cb_extract_metadata_from_streams( $content, $metadata );

    // Step 6: Extract metadata from alternate encoding formats.
    $metadata = cb_extract_metadata_alternate_formats( $content, $metadata );

    // Step 7: Handle special cases for Microsoft Office generated PDFs.
    $metadata = cb_handle_office_pdf_formats( $content, $metadata );

    return $metadata;
}

/**
 * Parse PDF Info objects from the PDF content.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_parse_pdf_info_objects( $content, $metadata ) {
    // Find all PDF objects that contain metadata.
    if ( preg_match_all( '/(\d+)\s+\d+\s+obj\s*<<([^>]*)>>/is', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            $obj_content = $match[2];

            // Check if this object contains metadata fields.
            if ( preg_match( '/\/(?:Title|Author|Creator|Producer|Keywords)/', $obj_content ) ) {
                $metadata = cb_extract_metadata_from_object( $obj_content, $metadata );
            }
        }
    }

    return $metadata;
}

/**
 * Extract metadata from a PDF object content.
 *
 * @param string $obj_content Object content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_metadata_from_object( $obj_content, $metadata ) {
    $fields = array(
        'title'    => 'Title',
        'author'   => 'Author',
        'creator'  => 'Creator',
        'producer' => 'Producer',
        'keywords' => 'Keywords',
    );

    foreach ( $fields as $key => $field ) {
        if ( 'N/A' !== $metadata[ $key ] ) {
            continue; // Skip if already found.
        }

        // Try multiple extraction patterns.
        $patterns = array(
            // Standard format: /Field (value).
            '/\/' . $field . '\s*\(\s*([^)]+)\s*\)/',
            // Hex format: /Field <hex>.
            '/\/' . $field . '\s*<([0-9A-Fa-f]+)>/',
            // Binary format with Unicode markers.
            '/\/' . $field . '\s*\(\s*\xFE\xFF([^)]+)\s*\)/',
            '/\/' . $field . '\s*\(\s*\xFF\xFE([^)]+)\s*\)/',
            // Extended parentheses format.
            '/\/' . $field . '\s*\(\s*([^)]*(?:\\.[^)]*)*)\s*\)/',
        );

        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $obj_content, $matches ) ) {
                $value = $matches[1];

                // Handle hex encoding.
                if ( ctype_xdigit( $value ) && strlen( $value ) % 2 === 0 && strlen( $value ) > 4 ) {
                    $decoded = @hex2bin( $value );
                    if ( false !== $decoded ) {
                        $value = $decoded;
                    }
                }

                $cleaned = cb_clean_pdf_metadata_string( $value );
                if ( 'N/A' !== $cleaned && ! empty( $cleaned ) ) {
                    $metadata[ $key ] = $cleaned;
                    break;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Extract metadata using direct pattern matching on the entire PDF content.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_metadata_patterns( $content, $metadata ) {
    $fields = array(
        'title'    => 'Title',
        'author'   => 'Author',
        'creator'  => 'Creator',
        'producer' => 'Producer',
        'keywords' => 'Keywords',
    );

    foreach ( $fields as $key => $field ) {
        if ( 'N/A' !== $metadata[ $key ] ) {
            continue;
        }

        // More comprehensive patterns for different PDF formats.
        $patterns = array(
            // Standard format.
            '/\/' . $field . '\s*\(\s*([^)]+)\s*\)/i',
            // With escape sequences.
            '/\/' . $field . '\s*\(\s*([^)]*(?:\\.[^)]*)*)\s*\)/i',
            // Hex format.
            '/\/' . $field . '\s*<([0-9A-Fa-f]+)>/i',
            // Array format.
            '/\/' . $field . '\s*\[\s*\(\s*([^)]+)\s*\)\s*\]/i',
            // Dictionary format.
            '/\/' . $field . '\s*<<[^>]*\/S\s*\(\s*([^)]+)\s*\)/i',
        );

        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $content, $matches ) ) {
                $value = $matches[1];

                // Handle various encodings.
                if ( ctype_xdigit( $value ) && strlen( $value ) % 2 === 0 && strlen( $value ) > 4 ) {
                    $decoded = @hex2bin( $value );
                    if ( false !== $decoded ) {
                        $value = $decoded;
                    }
                }

                $cleaned = cb_clean_pdf_metadata_string( $value );
                if ( 'N/A' !== $cleaned && ! empty( $cleaned ) ) {
                    $metadata[ $key ] = $cleaned;
                    break;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Extract application signatures for Creator and Producer fields.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_application_signatures( $content, $metadata ) {
    // Known application signatures with their typical roles.
    $signatures = array(
        // Microsoft Office applications (typically Creator).
        'Microsoft® Word for Microsoft 365'        => 'creator',
        'Microsoft® PowerPoint® for Microsoft 365' => 'creator',
        'Microsoft® Excel® for Microsoft 365'      => 'creator',
        'Microsoft Word'                           => 'creator',
        'Microsoft PowerPoint'                     => 'creator',
        'Microsoft Excel'                          => 'creator',

        // Adobe applications.
        'Adobe PDF Library'                        => 'producer',
        'Adobe Acrobat'                            => 'producer',
        'Adobe InDesign'                           => 'creator',
        'Adobe Illustrator'                        => 'creator',
        'Acrobat Distiller'                        => 'producer',
        'Acrobat PDFMaker'                         => 'creator',
        'Adobe PDF Printer'                        => 'producer',

        // Other common applications.
        'PScript5.dll'                             => 'producer',
        'PDFKit.NET'                               => 'producer',
        'Microsoft: Print To PDF'                  => 'producer',
        'LibreOffice'                              => 'creator',
        'OpenOffice'                               => 'creator',
        'Chrome PDF Plugin'                        => 'producer',
        'wkhtmltopdf'                              => 'producer',
    );

    foreach ( $signatures as $signature => $field ) {
        if ( 'N/A' !== $metadata[ $field ] ) {
            continue; // Skip if already found.
        }

        // Search for the signature in the PDF content.
        $pos = stripos( $content, $signature );
        if ( false !== $pos ) {
            // Found the signature, try to extract the full version string.
            $start   = max( 0, $pos - 50 );
            $length  = min( strlen( $content ) - $start, 200 );
            $context = substr( $content, $start, $length );

            // Look for patterns that include version numbers.
            $version_patterns = array(
                '/(' . preg_quote( $signature, '/' ) . '[^)]*\d+[^)]*)/i',
                '/(' . preg_quote( $signature, '/' ) . '[^\\x00-\\x1F]*)/i',
                '/(' . preg_quote( $signature, '/' ) . ')/i',
            );

            foreach ( $version_patterns as $pattern ) {
                if ( preg_match( $pattern, $context, $matches ) ) {
                    $full_signature = cb_clean_pdf_metadata_string( $matches[1] );
                    if ( 'N/A' !== $full_signature && ! empty( $full_signature ) ) {
                        $metadata[ $field ] = $full_signature;
                        break 2; // Break out of both loops.
                    }
                }
            }

            // Fallback to the basic signature.
            if ( 'N/A' === $metadata[ $field ] ) {
                $metadata[ $field ] = $signature;
            }
        }
    }

    return $metadata;
}

/**
 * Extract XMP metadata from PDF content.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_xmp_metadata( $content, $metadata ) {
    // XMP metadata is usually embedded in XML format.
    // Look for common XMP patterns.

    // Adobe/Dublin Core metadata.
    $xmp_patterns = array(
        'title'    => array(
            '/<dc:title><rdf:Alt><rdf:li[^>]*>([^<]+)<\/rdf:li><\/rdf:Alt><\/dc:title>/',
            '/<dc:title>([^<]+)<\/dc:title>/',
        ),
        'author'   => array(
            '/<dc:creator><rdf:Seq><rdf:li>([^<]+)<\/rdf:li><\/rdf:Seq><\/dc:creator>/',
            '/<dc:creator>([^<]+)<\/dc:creator>/',
        ),
        'creator'  => array(
            '/<xmp:CreatorTool>([^<]+)<\/xmp:CreatorTool>/',
            '/<pdf:Creator>([^<]+)<\/pdf:Creator>/',
        ),
        'producer' => array(
            '/<pdf:Producer>([^<]+)<\/pdf:Producer>/',
        ),
        'keywords' => array(
            '/<dc:subject><rdf:Bag><rdf:li>([^<]+)<\/rdf:li><\/rdf:Bag><\/dc:subject>/',
            '/<pdf:Keywords>([^<]+)<\/pdf:Keywords>/',
        ),
    );

    foreach ( $xmp_patterns as $field => $patterns ) {
        if ( 'N/A' !== $metadata[ $field ] ) {
            continue; // Skip if already found.
        }

        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $content, $matches ) ) {
                $value = cb_clean_pdf_metadata_string( $matches[1] );
                if ( 'N/A' !== $value && ! empty( $value ) ) {
                    $metadata[ $field ] = $value;
                    break;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Extract metadata from encrypted or compressed PDF streams.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_metadata_from_streams( $content, $metadata ) {
    // Look for Info dictionary references.
    if ( preg_match( '/\/Info\s+(\d+)\s+\d+\s+R/', $content, $matches ) ) {
        $info_obj_id = $matches[1];

        // Try to find the Info object.
        $pattern = '/' . $info_obj_id . '\s+\d+\s+obj\s*<<([^>]*)>>/is';
        if ( preg_match( $pattern, $content, $obj_matches ) ) {
            $metadata = cb_extract_metadata_from_object( $obj_matches[1], $metadata );
        }
    }

    // Look for metadata in trailer.
    if ( preg_match( '/trailer\s*<<([^>]*)>>/is', $content, $matches ) ) {
        $trailer_content = $matches[1];

        // Extract metadata from trailer.
        $fields = array(
            'title'    => 'Title',
            'author'   => 'Author',
            'creator'  => 'Creator',
            'producer' => 'Producer',
            'keywords' => 'Keywords',
        );

        foreach ( $fields as $key => $field ) {
            if ( 'N/A' !== $metadata[ $key ] ) {
                continue;
            }

            // Look for field in trailer.
            if ( preg_match( '/\/' . $field . '\s*\(\s*([^)]+)\s*\)/', $trailer_content, $field_matches ) ) {
                $value = cb_clean_pdf_metadata_string( $field_matches[1] );
                if ( 'N/A' !== $value && ! empty( $value ) ) {
                    $metadata[ $key ] = $value;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Extract metadata from alternate encoding formats.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_extract_metadata_alternate_formats( $content, $metadata ) {
    // Look for metadata in various formats that might be used by different PDF creators.
    $alternate_patterns = array(
        'title'    => array(
            '/Title\s*:\s*([^\r\n]+)/i',
            '/DocumentTitle\s*:\s*([^\r\n]+)/i',
            '/dc:title>\s*([^<]+)/i',
        ),
        'author'   => array(
            '/Author\s*:\s*([^\r\n]+)/i',
            '/DocumentAuthor\s*:\s*([^\r\n]+)/i',
            '/dc:creator>\s*([^<]+)/i',
        ),
        'creator'  => array(
            '/Creator\s*:\s*([^\r\n]+)/i',
            '/Application\s*:\s*([^\r\n]+)/i',
            '/CreatorTool\s*:\s*([^\r\n]+)/i',
        ),
        'producer' => array(
            '/Producer\s*:\s*([^\r\n]+)/i',
            '/Generator\s*:\s*([^\r\n]+)/i',
            '/pdf:Producer>\s*([^<]+)/i',
        ),
        'keywords' => array(
            '/Keywords\s*:\s*([^\r\n]+)/i',
            '/Subject\s*:\s*([^\r\n]+)/i',
            '/dc:subject>\s*([^<]+)/i',
        ),
    );

    foreach ( $alternate_patterns as $field => $patterns ) {
        if ( 'N/A' !== $metadata[ $field ] ) {
            continue;
        }

        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $content, $matches ) ) {
                $value = cb_clean_pdf_metadata_string( $matches[1] );
                if ( 'N/A' !== $value && ! empty( $value ) ) {
                    $metadata[ $field ] = $value;
                    break;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Handle special cases for Microsoft Office generated PDFs.
 *
 * @param string $content PDF content.
 * @param array  $metadata Current metadata.
 * @return array Updated metadata.
 */
function cb_handle_office_pdf_formats( $content, $metadata ) {
    // Look for specific Microsoft Office patterns.
    if ( stripos( $content, 'Microsoft Word' ) !== false || stripos( $content, 'Microsoft: Print To PDF' ) !== false ) {
        // Try to extract title from Microsoft Word document names.
        if ( 'N/A' === $metadata['title'] ) {
            if ( preg_match( '/Microsoft Word - ([^\\r\\n\\x00-\\x1F]+)/', $content, $matches ) ) {
                $title = cb_clean_pdf_metadata_string( $matches[1] );
                if ( 'N/A' !== $title && ! empty( $title ) ) {
                    $metadata['title'] = $title;
                }
            }
        }

        // Set producer if not found.
        if ( 'N/A' === $metadata['producer'] && stripos( $content, 'Print To PDF' ) !== false ) {
            $metadata['producer'] = 'Microsoft: Print To PDF';
        }
    }

    // Look for PScript patterns.
    if ( stripos( $content, 'PScript5.dll' ) !== false ) {
        if ( 'N/A' === $metadata['creator'] ) {
            if ( preg_match( '/(PScript5\\.dll[^\\r\\n\\x00-\\x1F]*)/i', $content, $matches ) ) {
                $creator = cb_clean_pdf_metadata_string( $matches[1] );
                if ( 'N/A' !== $creator && ! empty( $creator ) ) {
                    $metadata['creator'] = $creator;
                }
            }
        }
    }

    // Look for Acrobat Distiller patterns.
    if ( stripos( $content, 'Acrobat Distiller' ) !== false ) {
        if ( 'N/A' === $metadata['producer'] ) {
            if ( preg_match( '/(Acrobat Distiller[^\\r\\n\\x00-\\x1F]*)/i', $content, $matches ) ) {
                $producer = cb_clean_pdf_metadata_string( $matches[1] );
                if ( 'N/A' !== $producer && ! empty( $producer ) ) {
                    $metadata['producer'] = $producer;
                }
            }
        }
    }

    return $metadata;
}

/**
 * Clean PDF metadata string by handling various encodings and removing control characters.
 *
 * @param string $value Raw metadata value.
 * @return string Cleaned value or 'N/A'.
 */
function cb_clean_pdf_metadata_string( $value ) {
    if ( empty( $value ) ) {
        return 'N/A';
    }

    // Convert to string if not already.
    $value = (string) $value;

    // Handle UTF-16 BOM (Big Endian).
    if ( substr( $value, 0, 2 ) === "\xFE\xFF" ) {
        $value = mb_convert_encoding( substr( $value, 2 ), 'UTF-8', 'UTF-16BE' );
    }

    // Handle UTF-16 BOM (Little Endian).
    if ( substr( $value, 0, 2 ) === "\xFF\xFE" ) {
        $value = mb_convert_encoding( substr( $value, 2 ), 'UTF-8', 'UTF-16LE' );
    }

    // Handle UTF-8 BOM.
    if ( substr( $value, 0, 3 ) === "\xEF\xBB\xBF" ) {
        $value = substr( $value, 3 );
    }

	// Remove null bytes and other control characters.
    $value = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value );

    // Handle escape sequences.
    $value = stripcslashes( $value );

    // Try to convert encoding if it's not UTF-8.
    if ( ! mb_check_encoding( $value, 'UTF-8' ) ) {
        // Try common encodings.
        $encodings = array( 'ISO-8859-1', 'Windows-1252', 'UTF-16', 'UTF-16LE', 'UTF-16BE' );
        foreach ( $encodings as $encoding ) {
            $converted = mb_convert_encoding( $value, 'UTF-8', $encoding );
            if ( $converted && mb_check_encoding( $converted, 'UTF-8' ) ) {
                $value = $converted;
                break;
            }
        }
    }

    // Clean up whitespace.
    $value = trim( $value );
    $value = preg_replace( '/\s+/', ' ', $value );

    // Remove any remaining garbage characters.
    $value = preg_replace( '/[^\x20-\x7E\x80-\xFF]/', '', $value );

    return empty( $value ) ? 'N/A' : $value;
}

/**
 * Test if a PDF is editable (simplified check).
 *
 * @param string $file_path Path to PDF file.
 * @return bool True if editable, false otherwise.
 */
function cb_test_pdf_editability( $file_path ) {
    try {
        require_once get_stylesheet_directory() . '/vendor/autoload.php';

        // Use FPDI to test if we can read the PDF.
        $pdf        = new \setasign\Fpdi\Fpdi();
        $page_count = $pdf->setSourceFile( $file_path );

        // If we can read pages and there are pages, consider it editable.
        return $page_count > 0;

    } catch ( Exception $e ) {
        // If we can't read it with FPDI, it might be encrypted or corrupted.
        return false;
    }
}

/**
 * Check if a PDF is encrypted.
 *
 * @param string $file_path Path to PDF file.
 * @return bool True if encrypted, false otherwise.
 */
function cb_is_pdf_encrypted( $file_path ) {
    $content = file_get_contents( $file_path );
    if ( false === $content ) {
        return false;
    }

    // Look for encryption indicators in the PDF.
    $encryption_patterns = array(
        '/\/Encrypt\s+\d+\s+\d+\s+R/', // Encrypt object reference.
        '/\/Filter\s*\/Standard/',     // Standard encryption filter.
        '/\/V\s*[1-9]/',               // Encryption version.
        '/\/O\s*<[0-9A-Fa-f]+>/',      // Owner password hash.
        '/\/U\s*<[0-9A-Fa-f]+>/',      // User password hash.
        '/DocuSign/',                  // DocuSign signature.
        '/Adobe\.PPKLite/',            // Adobe signature.
        '/adbe\.pkcs7\.detached/',     // PKCS7 signature.
    );

    foreach ( $encryption_patterns as $pattern ) {
        if ( preg_match( $pattern, $content ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Test if a PDF supports watermarking (visual watermark + metadata).
 *
 * @param string $file_path Path to the PDF file.
 * @return bool True if watermarking is supported, false otherwise.
 */
function cb_test_pdf_watermark_compatibility( $file_path ) {
    try {
        require_once get_stylesheet_directory() . '/vendor/autoload.php';

        $pdf        = new \setasign\Fpdi\Fpdi();
        $page_count = $pdf->setSourceFile( $file_path );

        // Test if we can add a page and watermark.
        if ( $page_count > 0 ) {
            $tpl_id = $pdf->importPage( 1 );
            $size   = $pdf->getTemplateSize( $tpl_id );

            $orientation = ( $size['width'] > $size['height'] ) ? 'L' : 'P';
            $pdf->AddPage( $orientation, array( $size['width'], $size['height'] ) );
            $pdf->useTemplate( $tpl_id, 0, 0, $size['width'], $size['height'], true );

            // Test watermark addition.
            $pdf->SetFont( 'Arial', '', 6 );
            $pdf->SetTextColor( 100, 100, 100 );
            $pdf->SetXY( 5, 5 );
            $pdf->Cell( 0, 5, 'Test Watermark', 0, 0, 'L' );

            // Test metadata addition.
            $pdf->SetKeywords( 'Test Keywords' );

            return true;
        }

        return false;

    } catch ( Exception $e ) {
        return false;
    }
}

/**
 * Test if a PDF supports metadata addition (without visual watermark).
 *
 * @param string $file_path Path to the PDF file.
 * @return bool True if metadata addition is supported, false otherwise.
 */
function cb_test_pdf_metadata_compatibility( $file_path ) {
    // Actually test the metadata addition process
    $temp_output = tempnam( sys_get_temp_dir(), 'metadata_test_' );
    $test_watermark = 'Test metadata: ' . time();
    
    $result = cb_add_metadata_only_pdf( $file_path, $temp_output, $test_watermark );
    
    // Clean up
    if ( file_exists( $temp_output ) ) {
        unlink( $temp_output );
    }
    
    return $result;
}

/**
 * Test if a PDF supports direct metadata modification without rewriting.
 *
 * @param string $file_path Path to the PDF file.
 * @return bool True if direct metadata modification is possible, false otherwise.
 */
function cb_test_direct_metadata_modification( $file_path ) {
	// Actually test the metadata modification process
	$temp_output = tempnam( sys_get_temp_dir(), 'metadata_test_' );
	$test_watermark = 'Test metadata: ' . time();
	
	$result = cb_add_direct_metadata_modification( $file_path, $temp_output, $test_watermark );
	
	// Clean up
	if ( file_exists( $temp_output ) ) {
		unlink( $temp_output );
	}
	
	return $result;
}

