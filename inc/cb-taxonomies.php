<?php
/**
 * File: cb-taxonomies.php
 *
 * Custom taxonomy registration for attachments.
 *
 * This file defines and registers the 'library' taxonomy for WordPress attachments.
 *
 * @package cb-arcusinvestor2025
 */

/**
 * Register the 'library' taxonomy for attachments.
 */
function register_library_taxonomy_for_attachments() {
	register_taxonomy(
		'library',
		'attachment',
		array(
			'label'             => 'Libraries',
			'labels'            => array(
				'name'          => 'Libraries',
				'singular_name' => 'Library',
				'search_items'  => 'Search Libraries',
				'all_items'     => 'All Libraries',
				'edit_item'     => 'Edit Library',
				'update_item'   => 'Update Library',
				'add_new_item'  => 'Add New Library',
				'new_item_name' => 'New Library Name',
				'menu_name'     => 'Libraries',
			),
			'public'            => false, // Not queryable from the front end directly.
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true, // Needed if using block editor or REST queries.
			'rewrite'           => false,
			'hierarchical'      => true,
			'show_tagcloud'     => false,
			'capabilities'      => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'upload_files',
			),
		)
	);
}
add_action( 'init', 'register_library_taxonomy_for_attachments' );
