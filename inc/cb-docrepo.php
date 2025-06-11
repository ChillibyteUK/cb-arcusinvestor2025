<?php
/**
 * CB DocRepo - Handles document repository functionality.
 *
 * This file contains the implementation for managing the document repository,
 * including download proxy, logging, and admin interface.
 *
 * @package cb-arcusinvestor2025
 */

require_once CB_THEME_DIR . '/inc/cb-docrepo-user.php';
require_once CB_THEME_DIR . '/inc/cb-docrepo-admin.php';

// phpcs:disable WordPress.Security.NonceVerification.Recommended
// phpcs:disable WordPress.Security.NonceVerification.Missing

// Register the proxy endpoint.
add_action(
	'init',
	function () {
		add_rewrite_rule( '^download/?$', 'index.php?cb_download_proxy=1', 'top' );
		add_rewrite_tag( '%cb_download_proxy%', '1' );
		add_rewrite_tag( '%file%', '([0-9]+)' );
		add_rewrite_tag( '%mode%', '([a-z]+)' );
	}
);

// Intercept download requests.
add_action(
	'template_redirect',
	function () {
		if ( get_query_var( 'cb_download_proxy' ) && is_user_logged_in() ) {

			$file_id         = absint( get_query_var( 'file' ) );
			$mode            = get_query_var( 'mode', 'download' ); // default to download.
			$current_user    = wp_get_current_user();
			$allowed_folders = cb_get_user_rml_folder_ids();

			if ( $file_id && ! empty( $allowed_folders ) ) {
				global $wpdb;

				$user_id    = get_current_user_id();
				$file_title = get_the_title( $file_id );
                $file_name  = basename( get_attached_file( $file_id ) );

                // Retrieve folder name.
				$cache_key   = 'cb_folder_name_' . $file_id;
				$folder_name = wp_cache_get( $cache_key, 'cb_download_proxy' );

				if ( false === $folder_name ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$folder_name = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT rml.name 
							FROM {$wpdb->prefix}realmedialibrary_posts rml_posts
							LEFT JOIN {$wpdb->prefix}realmedialibrary rml ON rml_posts.fid = rml.id
							WHERE rml_posts.attachment = %d LIMIT 1",
							$file_id
						)
					);
					wp_cache_set( $cache_key, $folder_name, 'cb_download_proxy' );
				}

				// phpcs:ignore WordPress.WP.Capabilities.Unknown
				if ( user_can( $user_id, 'portal_user' ) ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wpdb->insert(
						$wpdb->prefix . 'cb_download_log',
						array(
							'user_id'       => $user_id,
							'attachment_id' => $file_id,
							'file_title'    => $file_title,
                            'file_name'     => $file_name,
                            'folder_name'   => $folder_name,
							'action'        => $mode,
						)
					);
				}

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$folder_id = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT fid FROM {$wpdb->prefix}realmedialibrary_posts WHERE attachment = %d LIMIT 1",
						$file_id
					)
				);

				if ( in_array( intval( $folder_id ), $allowed_folders, true ) ) {
					$file_path = get_attached_file( $file_id );

					if ( file_exists( $file_path ) ) {
						header( 'Content-Description: File Transfer' );
						header( 'Content-Type: ' . mime_content_type( $file_path ) );
						header( 'Expires: 0' );
						header( 'Cache-Control: must-revalidate' );
						header( 'Pragma: public' );
						header( 'Content-Length: ' . filesize( $file_path ) );

						if ( 'download' === $mode ) {
							header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
						} else {
							header( 'Content-Disposition: inline; filename="' . basename( $file_path ) . '"' );
						}

						readfile( $file_path );
						exit;
					}
				}
			}

			wp_die( 'Access denied or file not found.', 'Error', array( 'response' => 403 ) );
		}
	}
);

/**
 * Filter attachments by 'docstatus' on the front end.
 *
 * @param WP_Query $query The WP_Query instance.
 */
function cb_filter_attachments_by_docstatus( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->get( 'post_type' ) === 'attachment' ) {
        $meta_query = array(
            array(
                'key'     => 'docstatus',
                'value'   => 'approved',
                'compare' => '=',
            ),
        );
        $query->set( 'meta_query', $meta_query );
    }
}
add_action( 'pre_get_posts', 'cb_filter_attachments_by_docstatus' );

// ----------------- LOGGING ----------------- //

/**
 * Creates the download log table in the database with updated columns.
 *
 * This function sets up a table to log user actions, including user ID, attachment ID,
 * file title, file name, action type, and timestamp.
 */
function cb_ensure_download_log_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'cb_download_log';

    // Define the table schema.
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        attachment_id BIGINT(20) UNSIGNED DEFAULT NULL,
        file_title VARCHAR(255) DEFAULT NULL,
        file_name VARCHAR(255) DEFAULT NULL,
		folder_name VARCHAR(255) DEFAULT NULL,
        action VARCHAR(20) NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
		notes TEXT DEFAULT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Execute the table creation.
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
add_action( 'after_setup_theme', 'cb_ensure_download_log_table' );


add_action(
	'wp_login',
	function ( $user_login, $user ) {
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( user_can( $user, 'portal_user' ) ) {
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$wpdb->prefix . 'cb_download_log',
				array(
					'user_id' => $user->ID,
					'action'  => 'login',
				)
			);
		}
	},
	10,
	2
);


/**
 * Log folder creation.
 *
 * @param int    $folder_id   The ID of the created folder.
 * @param string $folder_name The name of the created folder.
 */
function cb_log_folder_creation( $folder_id, $folder_name ) {
    global $wpdb;

    // Log the folder creation action.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->insert(
        $wpdb->prefix . 'cb_download_log',
        array(
            'user_id'     => get_current_user_id(),
            'action'      => 'folder_create',
            'folder_name' => $folder_name,
            'notes'       => "Folder created: {$folder_name}",
            'timestamp'   => current_time( 'mysql' ),
        )
    );
}
add_action( 'RML/Folder/Created', 'cb_log_folder_creation', 10, 2 );

/**
 * Log folder renaming.
 *
 * @param string $new_name  The new name of the folder.
 * @param object $folder    The folder object being renamed.
 * @param object $old_data  The old data of the folder, including the old name.
 */
function cb_log_folder_renaming( $new_name, $folder, $old_data ) {
    global $wpdb;

    // Extract the old name from the old data object.
    $old_name = isset( $old_data->name ) ? $old_data->name : 'Unknown Folder';

    // Log the folder renaming action.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->insert(
        $wpdb->prefix . 'cb_download_log',
        array(
            'user_id'     => get_current_user_id(),
            'action'      => 'folder_rename',
            'folder_name' => $new_name,
            'notes'       => "Folder renamed from '{$old_name}' to '{$new_name}'",
            'timestamp'   => current_time( 'mysql' ),
        )
    );
}
add_action( 'RML/Folder/Renamed', 'cb_log_folder_renaming', 10, 3 );

/**
 * Log folder deletion.
 *
 * @param int   $folder_id   The ID of the deleted folder.
 * @param mixed $folder_name The name of the deleted folder (string or object).
 */
function cb_log_folder_deletion( $folder_id, $folder_name ) {
    global $wpdb;

    // Ensure folder_name is a string.
    if ( is_object( $folder_name ) ) {
        $folder_name = isset( $folder_name->name ) ? $folder_name->name : 'Unknown Folder';
    }

    // Log the folder deletion action.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->insert(
        $wpdb->prefix . 'cb_download_log',
        array(
            'user_id'     => get_current_user_id(),
            'action'      => 'folder_delete',
            'folder_name' => $folder_name,
            'notes'       => "Folder deleted: {$folder_name}",
            'timestamp'   => current_time( 'mysql' ),
        )
    );
}
add_action( 'RML/Folder/Deleted', 'cb_log_folder_deletion', 10, 2 );


/**
 * Log folder changes for attachments.
 *
 * @param int $attachment_id The ID of the attachment being moved.
 * @param int $old_folder_id The ID of the old folder.
 * @param int $new_folder_id The ID of the new folder.
 */
function cb_log_folder_change( $attachment_id, $old_folder_id, $new_folder_id ) {
    global $wpdb;

    // Retrieve folder names.

	$cache_key       = 'old_folder_name_' . $old_folder_id;
	$old_folder_name = wp_cache_get( $cache_key );

	if ( false === $old_folder_name ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$old_folder_name = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT name FROM {$wpdb->prefix}realmedialibrary WHERE id = %d LIMIT 1",
				$old_folder_id
			)
		);
		wp_cache_set( $cache_key, $old_folder_name );
	}

	$cache_key       = 'new_folder_name_' . $new_folder_id;
	$new_folder_name = wp_cache_get( $cache_key );

	if ( false === $new_folder_name ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$new_folder_name = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT name FROM {$wpdb->prefix}realmedialibrary WHERE id = %d LIMIT 1",
				$new_folder_id
			)
		);
		wp_cache_set( $cache_key, $new_folder_name );
	}

	// If folder names are not found, use default values.
	if ( ! $old_folder_name ) {
		$old_folder_name = 'Unorganized';
	}
	if ( ! $new_folder_name ) {
		$new_folder_name = 'Unorganized';
	}

	if ( $old_folder_name === $new_folder_name ) {
		// If the folder names are the same, no need to log.
		return;
	}

    // Retrieve file details.
    $file_title = get_the_title( $attachment_id );
    $file_name  = basename( get_attached_file( $attachment_id ) );

    // Log the folder change action.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    $wpdb->insert(
        $wpdb->prefix . 'cb_download_log',
        array(
            'user_id'       => get_current_user_id(),
            'attachment_id' => $attachment_id,
            'file_title'    => $file_title,
            'file_name'     => $file_name,
            'folder_name'   => $new_folder_name,
            'action'        => 'folder_change',
			'notes'         => "File moved from '{$old_folder_name}' to '{$new_folder_name}'",
            'timestamp'     => current_time( 'mysql' ),
        )
    );
}
add_action( 'RML/Item/Moved', 'cb_log_folder_change', 10, 3 );

/**
 * Handles the export of CSV files for different log types.
 *
 * This function checks which export button was clicked and retrieves the relevant data from the database.
 * It then generates a CSV file for download.
 */
add_action(
	'admin_init',
	function () {
		global $wpdb;

		if ( isset( $_POST['cb_export_logins'] ) ) {
			$table = esc_sql( $wpdb->prefix . 'cb_download_log' );

			$cache_key = 'cb_login_log_data';
			$results   = wp_cache_get( $cache_key );

			if ( false === $results ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					"
					SELECT 
						l.user_id,
						u.user_login,
						u.user_email,
						u.display_name,
						l.action,
						l.timestamp
					FROM {$wpdb->prefix}cb_download_log l
					LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
					WHERE l.action = 'login'
					ORDER BY l.timestamp DESC
					",
					ARRAY_A
				);
				wp_cache_set( $cache_key, $results );
			}

			cb_export_csv( $results, 'login-log.csv' );
		}

		$table = esc_sql( $wpdb->prefix . 'cb_download_log' );

		if ( isset( $_POST['cb_export_user_activity'] ) ) {
			if ( ! empty( $_POST['cb_user_id'] ) ) {
				$user_id = intval( $_POST['cb_user_id'] );

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT 
							l.user_id,
							u.user_login,
							u.user_email,
							u.display_name,
							l.folder_name,
							l.file_title,
							l.file_name,
							l.action,
							l.notes,
							l.timestamp
						FROM {$wpdb->prefix}cb_download_log l
						LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
						WHERE l.user_id = %d
						ORDER BY l.timestamp DESC
						",
						$user_id
					),
					ARRAY_A
				);

				cb_export_csv( $results, 'user-' . $user_id . '-activity.csv' );
			}
		}

		if ( isset( $_POST['cb_export_file_access'] ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery 
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT 
						l.user_id,
						u.user_login,
						u.user_email,
						u.display_name,
						l.folder_name,
						l.file_title,
						l.file_name,
						l.action,
						l.timestamp
					FROM {$wpdb->prefix}cb_download_log l
					LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
					ORDER BY l.timestamp DESC
					"
				),
				ARRAY_A
			);

			cb_export_csv( $results, 'file-access-log.csv' );
		}
	}
);


/**
 * Exports data as a CSV file.
 *
 * This function takes an array of rows and a filename, then generates
 * and outputs a CSV file for download.
 *
 * @param array  $rows     The data rows to be exported.
 * @param string $filename The name of the CSV file to be generated.
 */
function cb_export_csv( $rows, $filename ) {
	if ( empty( $rows ) ) {
        $redirect_url = add_query_arg( 'cb_export_error', urlencode( 'No data to export.' ), wp_get_referer() );
        wp_safe_redirect( $redirect_url );
        exit;
    }

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	$output = fopen( 'php://output', 'w' );

	fputcsv( $output, array_keys( $rows[0] ) );
	foreach ( $rows as $row ) {
		fputcsv( $output, $row );
	}
	fclose( $output );
	exit;
}


add_action(
	'admin_notices',
	function () {
		if ( isset( $_GET['cb_export_error'] ) ) {
			$message = sanitize_text_field( urldecode( $_GET['cb_export_error'] ) );
			echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
		}
	}
);


// ----------------- SUB FOLDER HELPERS ----------------- //


/**
 * Render a list of files.
 *
 * @param array  $attachment_ids Array of attachment IDs.
 * @param string $heading        Optional heading for the file list.
 */
function cb_render_files_list( $attachment_ids, $heading = '' ) {

error_log('cb_render_files_list() called with attachment_ids: ' . print_r($attachment_ids, true));	
    $attachments = get_posts(
        array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post__in'       => $attachment_ids,
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => 'docstatus',
                    'value'   => 'approved',
                    'compare' => '=',
                ),
            ),
        )
    );

    if ( $attachments ) {
        if ( $heading ) {
            echo '<h4 class="mt-4">' . esc_html( $heading ) . '</h4>';
        }

        foreach ( $attachments as $doc ) {
            $url         = wp_get_attachment_url( $doc->ID );
            $filename    = basename( get_attached_file( $doc->ID ) );
            $ext         = strtoupper( pathinfo( $filename, PATHINFO_EXTENSION ) );
            $size        = size_format( filesize( get_attached_file( $doc->ID ) ) );
            $upload_time = get_the_time( 'd M Y', $doc->ID );

            $icon       = 'fa-regular fa-file';
            $icon_class = 'text-secondary';
            if ( 'PDF' === $ext ) {
                $icon       = 'fa-regular fa-file-pdf';
                $icon_class = 'text-danger';
            } elseif ( in_array( $ext, array( 'XLS', 'XLSX', 'CSV' ), true ) ) {
                $icon       = 'fa-regular fa-file-excel';
                $icon_class = 'text-success';
            } elseif ( in_array( $ext, array( 'DOC', 'DOCX' ), true ) ) {
                $icon       = 'fa-regular fa-file-word';
                $icon_class = 'text-primary';
            }

            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0" 
                data-ext="<?= esc_attr( $ext ); ?>"
                data-title="<?= esc_attr( strtolower( $doc->post_title ) ); ?>"
                data-date="<?= esc_attr( get_post_time( 'U', false, $doc->ID ) ); ?>">
				<span>
                    <i class="<?= esc_attr( $icon ); ?> me-2 <?= esc_attr( $icon_class ); ?>"></i>
                    <strong><?= esc_html( $doc->post_title ); ?></strong>
                    (<?= esc_html( $filename ); ?>)
                </span>
                <span class="text-muted small">
                    [<?= esc_html( $size ); ?>]
					&middot;
                    <?= esc_html( $upload_time ); ?>
                    <a href="<?= esc_url( site_url( '/download/?file=' . $doc->ID . '&mode=view' ) ); ?>" class="btn btn-sm btn-outline-secondary ms-2" target="_blank" rel="noopener noreferrer">View</a>
                    <a href="<?= esc_url( site_url( '/download/?file=' . $doc->ID . '&mode=download' ) ); ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank" rel="noopener noreferrer">Download</a>
                </span>
            </li>
            <?php
        }
    } else {
        echo '<p class="mt-4 text-muted">No approved files found.</p>';
    }
}