<?php
/**
 * File: cb-noblog.php
 * Description: This file contains functions to remove blog-related features from the WordPress theme.
 *
 * @package cb-arcusinvestor2025
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Removes support for various features of the 'post' post type.
 */
function cb_remove_post_support() {
	if ( post_type_exists( 'post' ) ) {
		remove_post_type_support( 'post', 'editor' );
		remove_post_type_support( 'post', 'thumbnail' );
		remove_post_type_support( 'post', 'excerpt' );
		remove_post_type_support( 'post', 'comments' );
		remove_post_type_support( 'post', 'trackbacks' );
		remove_post_type_support( 'post', 'revisions' );
		remove_post_type_support( 'post', 'author' );
		remove_post_type_support( 'post', 'custom-fields' );
		remove_post_type_support( 'post', 'page-attributes' );
	}
}
add_action( 'init', 'cb_remove_post_support' );

/**
 * Remove the 'post' post type from the admin menu.
 */
function cb_remove_posts_menu() {
	remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'cb_remove_posts_menu' );

/**
 * Redirects users from the 'post' post type to the home page.
 */
function cb_redirect_posts_to_home() {
	if ( is_singular( 'post' ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'template_redirect', 'cb_redirect_posts_to_home' );

/**
 * Remove the 'post' post type from the admin dashboard.
 */
function cb_remove_dashboard_widgets() {
	remove_meta_box( 'dashboard_recent_posts', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'cb_remove_dashboard_widgets' );

/**
 * Remove posts from the WordPress search results.
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 * @return WP_Query Modified query object.
 */
function cb_exclude_posts_from_search( $query ) {
	if ( $query->is_search && ! is_admin() ) {
		$query->set( 'post_type', 'page' );
	}
	return $query;
}
add_filter( 'pre_get_posts', 'cb_exclude_posts_from_search' );
