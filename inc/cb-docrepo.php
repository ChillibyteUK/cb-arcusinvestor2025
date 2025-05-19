<?php
/**
 * CB DocRepo - Handles document repository functionality.
 *
 * @package cb-arcusinvestor2025
 */

require_once CB_THEME_DIR . '/inc/cb-docrepo-user.php';

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

				$user_id = get_current_user_id();

				if ( user_can( $user_id, 'portal_user' ) ) {
					$wpdb->insert(
						$wpdb->prefix . 'cb_download_log',
						array(
							'user_id'       => $user_id,
							'attachment_id' => $file_id,
							'action'        => $mode,
						)
					);
				}

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

// ----------------- LOGGING ----------------- //

/**
 * Creates the download log table in the database.
 *
 * This function sets up a table to log user download actions, including
 * user ID, attachment ID, action type, and timestamp.
 */
function cb_ensure_download_log_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'cb_download_log';

	// Check if the table exists first
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) {
		// If not, create it
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			attachment_id BIGINT(20) UNSIGNED DEFAULT NULL,
			action VARCHAR(20) NOT NULL,
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

add_action( 'after_setup_theme', 'cb_ensure_download_log_table' );


add_action(
	'wp_login',
	function ( $user_login, $user ) {
		if ( user_can( $user, 'portal_user' ) ) {
			global $wpdb;
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

// Logging admin bits.
add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Portal Logs',
			'Portal Logs',
			'manage_options',
			'cb-portal-logs',
			'cb_render_logs_admin_page',
			'dashicons-media-spreadsheet',
			25
		);
	}
);

/**
 * Renders the admin page for Portal Logs.
 *
 * This function outputs the HTML for the Portal Logs admin page,
 * including options to export login activity, user activity, and file access logs.
 */
function cb_render_logs_admin_page() {
	?>
	<div class="wrap">
		<h1>Portal Logs</h1>
		<form method="post">
			<p><button name="cb_export_logins" class="button button-primary">Export Login Activity</button></p>
			<p>
				<select name="cb_user_id">
					<option value="">Select User</option>
					<?php
					foreach ( get_users( array( 'role__in' => array( 'portal_user' ) ) ) as $user ) {
						echo '<option value="' . esc_attr( $user->ID ) . '">' . esc_html( $user->display_name ) . '</option>';
					}
					?>
				</select>
				<button name="cb_export_user_activity" class="button">Export User Activity</button>
			</p>
			<p><button name="cb_export_file_access" class="button">Export File Access Log</button></p>
		</form>
	</div>
	<?php
}

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
		$table = $wpdb->prefix . 'cb_download_log';

		if ( isset( $_POST['cb_export_logins'] ) ) {
			$results = $wpdb->get_results(
				"
				SELECT 
					l.*,
					u.user_login,
					u.user_email,
					u.display_name
				FROM {$table} l
				LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
				WHERE l.action = 'login'
				ORDER BY l.timestamp DESC
			", ARRAY_A );
		
			cb_export_csv( $results, 'login-log.csv' );
		}

		if ( isset( $_POST['cb_export_user_activity'] ) ) {
			if ( ! empty( $_POST['cb_user_id'] ) ) {
				$user_id = intval( $_POST['cb_user_id'] );
		
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT 
							l.*,
							p.post_title AS attachment_title,
							REPLACE(pm.meta_value, %s, '') AS attachment_filename
						FROM {$table} l
						LEFT JOIN {$wpdb->posts} p ON l.attachment_id = p.ID
						LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
						WHERE l.user_id = %d
						ORDER BY l.timestamp DESC
						",
						'wp-content/uploads/', // optional: strip upload path prefix
						$user_id
					),
					ARRAY_A
				);
		
				cb_export_csv( $results, 'user-' . $user_id . '-activity.csv' );
			}
		}

		if ( isset( $_POST['cb_export_file_access'] ) ) {
			$raw_results = $wpdb->get_results(
				"
				SELECT 
					l.*,
					u.user_login,
					u.user_email,
					u.display_name
				FROM {$table} l
				LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
				WHERE l.attachment_id IS NOT NULL
				ORDER BY l.timestamp DESC
			", ARRAY_A );
		
			$results = array();
		
			foreach ( $raw_results as $row ) {
				if ( ! empty( $row['attachment_id'] ) ) {
					$post = get_post( $row['attachment_id'] );
					if ( $post && 'attachment' === $post->post_type ) {
						$row['file_title'] = $post->post_title;
						$row['file_name']  = basename( get_attached_file( $post->ID ) );
					} else {
						$row['file_title'] = '(Unknown)';
						$row['file_name']  = '(Missing)';
					}
				}
				$results[] = $row;
			}
		
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
		wp_die( 'No data to export.' );
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
