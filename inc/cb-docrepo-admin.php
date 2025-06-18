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
    ?>
    <div class="wrap">
        <h1 class="mb-4">Document Management</h1>
        <ul class="nav nav-tabs">
            <li class="nav-item mb-0">
                <a class="nav-link active" href="#pending-approvals" data-bs-toggle="tab">Pending Approvals</a>
            </li>
            <li class="nav-item mb-0">
                <a class="nav-link" href="#logs" data-bs-toggle="tab">Logs</a>
            </li>
            <li class="nav-item mb-0">
                <a class="nav-link" href="#summary" data-bs-toggle="tab">Summary</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pending-approvals">
                <?php cb_render_pending_approvals(); ?>
            </div>
            <div class="tab-pane fade" id="logs">
                <?php cb_render_logs(); ?>
            </div>
            <div class="tab-pane fade" id="summary">
                <?php cb_render_summary(); ?>
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

    // Query for logs.
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
            LIMIT 50
            ",
            ARRAY_A
        );
        wp_cache_set( $cache_key, $logs, '', 3600 ); // Cache for 1 hour.
    }

    ?>
    <div class="logs pt-4">
        <h3>Logs</h3>

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
        <?php
		if ( empty( $logs ) ) {
			?>
            <p>No logs available.</p>
	        <?php
		} else {
			?>
			<h4>Recent Activity</h4>
            <table class="table table-striped table-hover table-sm small">
                <thead class="table-dark">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Folder</th>
                        <th>File Name</th>
                        <th>File Title</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $logs as $log ) : ?>
                        <tr>
							<td><?php echo esc_html( $log['timestamp'] ); ?></td>
                            <td><?php echo esc_html( $log['display_name'] ); ?></td>
                            <td><?php echo esc_html( ucfirst( $log['action'] ) ); ?></td>
                            <td><?php echo esc_html( $log['folder_name'] ?? 'N/A' ); ?></td>
                            <td><?php echo esc_html( $log['file_name'] ?? 'N/A' ); ?></td>
                            <td><?php echo esc_html( $log['file_title'] ?? 'N/A' ); ?></td>
                            <td><?php echo esc_html( $log['notes'] ?? 'N/A' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        	<?php
		}
		?>
    </div>
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