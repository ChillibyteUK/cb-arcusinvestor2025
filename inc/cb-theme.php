<?php
/**
 * CB Theme Functions
 *
 * This file contains theme-specific functions and customizations for the CB Arcus 2025 theme.
 *
 * @package cb-arcusinvestor2025
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

require_once CB_THEME_DIR . '/inc/cb-utility.php';
require_once CB_THEME_DIR . '/inc/cb-noblog.php';
require_once CB_THEME_DIR . '/inc/cb-docrepo.php';
require_once CB_THEME_DIR . '/inc/cb-blocks.php';

// Remove unwanted SVG filter injection WP.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/**
 * Removes the comment-reply.min.js script from the footer.
 */
function remove_comment_reply_header_hook() {
    wp_deregister_script( 'comment-reply' );
}
add_action( 'init', 'remove_comment_reply_header_hook' );

/**
 * Removes the comments menu from the WordPress admin dashboard.
 */
function remove_comments_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'remove_comments_menu' );

/**
 * Removes specific page templates from the available templates list.
 *
 * @param array $page_templates The list of page templates.
 * @return array The modified list of page templates.
 */
function child_theme_remove_page_template( $page_templates ) {
    unset(
		$page_templates['page-templates/blank.php'],
		$page_templates['page-templates/empty.php'],
		$page_templates['page-templates/left-sidebarpage.php'],
		$page_templates['page-templates/right-sidebarpage.php'],
		$page_templates['page-templates/both-sidebarspage.php']
	);
    return $page_templates;
}
add_filter( 'theme_page_templates', 'child_theme_remove_page_template' );

/**
 * Removes support for specific post formats in the theme.
 */
function remove_understrap_post_formats() {
	remove_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
add_action( 'after_setup_theme', 'remove_understrap_post_formats', 11 );

if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page(
        array(
            'page_title' => 'Site-Wide Settings',
            'menu_title' => 'Site-Wide Settings',
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'manage_options',
        )
    );
}

/**
 * Initializes widgets, menus, and theme supports.
 *
 * This function registers navigation menus, unregisters sidebars and menus,
 * and adds theme support for custom editor color palettes.
 */
function widgets_init() {

    register_nav_menus(
		array(
			'primary_nav'  => __( 'Primary Nav', 'cb-arcusinvestor2025' ),
			'footer_menu1' => __( 'Footer Nav', 'cb-arcusinvestor2025' ),
		)
	);

    unregister_sidebar( 'hero' );
    unregister_sidebar( 'herocanvas' );
    unregister_sidebar( 'statichero' );
    unregister_sidebar( 'left-sidebar' );
    unregister_sidebar( 'right-sidebar' );
    unregister_sidebar( 'footerfull' );
    unregister_nav_menu( 'primary' );

    add_theme_support( 'disable-custom-colors' );
    add_theme_support(
        'editor-color-palette',
        array(
            array(
                'name'  => 'Dark',
                'slug'  => 'dark',
                'color' => '#333333',
            ),
            array(
                'name'  => 'Light',
                'slug'  => 'light',
                'color' => '#f9f9f9',
            ),
        )
    );
}
add_action( 'widgets_init', 'widgets_init', 11 );

remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/**
 * Registers a custom dashboard widget for the Chillibyte theme.
 */
function register_cb_dashboard_widget() {
	wp_add_dashboard_widget(
		'cb_dashboard_widget',
        'Chillibyte',
        'cb_dashboard_widget_display'
    );
}
add_action( 'wp_dashboard_setup', 'register_cb_dashboard_widget' );

/**
 * Displays the content of the Chillibyte dashboard widget.
 */
function cb_dashboard_widget_display() {
	?>
    <div style="display: flex; align-items: center; justify-content: space-around;">
        <img style="width: 50%;"
            src="<?= esc_url( get_stylesheet_directory_uri() . '/img/cb-full.jpg' ); ?>">
        <a class="button button-primary" target="_blank" rel="noopener nofollow noreferrer"
            href="mailto:hello@chillibyte.co.uk/">Contact</a>
    </div>
    <div>
        <p><strong>Thanks for choosing Chillibyte!</strong></p>
        <hr>
        <p>Got a problem with your site, or want to make some changes & need us to take a look for you?</p>
        <p>Use the link above to get in touch and we'll get back to you ASAP.</p>
    </div>
	<?php
}

// phpcs:disable
// add_filter('wpseo_breadcrumb_links', function( $links ) {
//     global $post;
//     if ( is_singular( 'post' ) ) {
//         $t = get_the_category($post->ID);
//         $breadcrumb[] = array(
//             'url' => '/guides/',
//             'text' => 'Guides',
//         );

//         array_splice( $links, 1, -2, $breadcrumb );
//     }
//     return $links;
// }
// );

// remove discussion metabox
// function cc_gutenberg_register_files()
// {
//     // script file
//     wp_register_script(
//         'cc-block-script',
//         get_stylesheet_directory_uri() . '/js/block-script.js', // adjust the path to the JS file
//         array('wp-blocks', 'wp-edit-post')
//     );
//     // register block editor script
//     register_block_type('cc/ma-block-files', array(
//         'editor_script' => 'cc-block-script'
//     ));
// }
// add_action('init', 'cc_gutenberg_register_files');
// phpcs:enable

/**
 * Filters the excerpt content to modify or return it as is.
 *
 * @param string $post_excerpt The current post excerpt.
 * @return string The filtered or unmodified post excerpt.
 */
function understrap_all_excerpts_get_more_link( $post_excerpt ) {
    if ( is_admin() || ! get_the_ID() ) {
        return $post_excerpt;
    }
    return $post_excerpt;
}

// Remove Yoast SEO breadcrumbs from Revelanssi's search results.
/**
 * Removes shortcodes from the content during search queries.
 *
 * @param string $content The content to filter.
 * @return string The filtered content without shortcodes.
 */
function wpdocs_remove_shortcode_from_index( $content ) {
	if ( is_search() ) {
		$content = strip_shortcodes( $content );
    }
    return $content;
}
add_filter( 'the_content', 'wpdocs_remove_shortcode_from_index' );

// GF really is pants.
/**
 * Change submit from input to button.
 *
 * Do not use example provided by Gravity Forms as it strips out the button attributes including onClick.
 *
 * @param string $button_input The original input HTML for the submit button.
 * @param array  $form         The Gravity Forms form object.
 * @return string The modified button HTML.
 */
function wd_gf_update_submit_button( $button_input, $form ) {
    // save attribute string to $button_match[1].
    preg_match( '/<input([^\/>]*)(\s\/)*>/', $button_input, $button_match );

    // remove value attribute (since we aren't using an input).
    $button_atts = str_replace( "value='" . $form['button']['text'] . "' ", '', $button_match[1] );

    // create the button element with the button text inside the button element instead of set as the value.
    return '<button ' . $button_atts . '><span>' . $form['button']['text'] . '</span></button>';
}
add_filter( 'gform_submit_button', 'wd_gf_update_submit_button', 10, 2 );


/**
 * Enqueues theme-specific scripts and styles.
 *
 * This function deregisters jQuery and disables certain styles and scripts
 * that are commented out for potential use in the theme.
 */
function cb_theme_enqueue() {
    $the_theme = wp_get_theme();
	// phpcs:disable
    // wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_style('aos-style', "https://unpkg.com/aos@2.3.1/dist/aos.css", array());
    // wp_enqueue_script('aos', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), null, true);
    // wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true);
    // wp_enqueue_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array('jquery'), null, true);
	// phpcs:enable
    wp_deregister_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'cb_theme_enqueue' );

// phpcs:disable
// function add_custom_menu_item($items, $args)
// {
//     if ($args->theme_location == 'primary_nav') {
//         $new_item = '<li class="menu-item menu-item-type-post_tyep menu-item-object-page nav-item"><a href="' . esc_url(home_url('/search/')) . '" class="nav-link" title="Search"><span class="icon-search"></span></a></li>';
//         $items .= $new_item;
//     }
//     return $items;
// }
// add_filter('wp_nav_menu_items', 'add_custom_menu_item', 10, 2);
// phpcs:enable


add_filter(
	'wpcf7_form_elements',
	function ( $content ) {
		return preg_replace_callback(
			'#<input([^>]+type=["\']submit["\'][^>]*)>#i',
			function ( $matches ) {
				// Extract attributes from input.
				$attributes = $matches[1];

				// Pull out value attribute.
				if ( preg_match( '/value=["\']([^"\']+)["\']/', $attributes, $value_match ) ) {
					$label      = $value_match[1];
					$attributes = preg_replace( '/\s*value=["\'][^"\']+["\']/', '', $attributes );
				} else {
					$label = 'Submit';
				}

				return "<button type=\"submit\" {$attributes}>{$label}</button>";
			},
			$content
		);
	}
);

add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Determines if the block region is applicable based on the session and assigned regions.
 *
 * @return bool True if the block region is applicable, false otherwise.
 */
function is_block_region_applicable() {
    if ( session_status() === PHP_SESSION_NONE ) {
        session_start();
    }

    $session_region = isset( $_SESSION['region'] ) ? sanitize_text_field( $_SESSION['region'] ) : null;

    if ( ! $session_region ) {
        return false;
    }

    $block_regions = get_field( 'region' );

    if ( empty( $block_regions ) ) {
        return false;
    }

    // Handle Term Objects or IDs.
    $block_slugs = array();
    if ( is_object( $block_regions[0] ) || is_array( $block_regions[0] ) ) {
        // Term Objects: Extract the slug.
        foreach ( $block_regions as $term ) {
            $block_slugs[] = is_object( $term ) ? $term->slug : $term['slug'];
        }
    } elseif ( is_numeric( $block_regions[0] ) ) {
        // Term IDs: Fetch term objects to get slugs.
        foreach ( $block_regions as $term_id ) {
            $term = get_term( $term_id );
            if ( $term ) {
                $block_slugs[] = $term->slug;
            }
        }
    }

    if ( in_array( 'all-regions', $block_slugs, true ) ) {
        return true;
    }

    return in_array( $session_region, $block_slugs, true );
}

/**
 * Checks if the current user has permission to access the page based on their region.
 *
 * This function retrieves the user's region from the session and compares it
 * against the allowed regions assigned to the page or post. If the user's region
 * matches any of the allowed regions or if "All Regions" is assigned, access is granted.
 *
 * @return bool True if the user has permission, false otherwise.
 */
function check_page_permissions() {
    // Ensure the session is started.
    if ( session_status() === PHP_SESSION_NONE ) {
        session_start();
    }

    // retrieve region from the session.
    $user_region = isset( $_SESSION['region'] ) ? sanitize_text_field( $_SESSION['region'] ) : null;

    // Bail early if user_region is not set.
    if ( ! $user_region ) {
        return false;
    }

    // get list of allowed region IDs.
    $areas = get_field( 'region', get_the_ID() );

    // Bail early if no regions are assigned to the page/post.
    if ( empty( $areas ) ) {
        return false;
    }

    // Normalise the region data to ensure we always work with slugs.
    $allowed_regions = array();
    foreach ( $areas as $area ) {
        if ( is_object( $area ) && isset( $area->slug ) ) {
            // Term object with a slug property.
            $allowed_regions[] = $area->slug;
        } elseif ( is_array( $area ) && isset( $area['slug'] ) ) {
            // Associative array with a slug key.
            $allowed_regions[] = $area['slug'];
        } elseif ( is_numeric( $area ) ) {
            // Numeric term ID; retrieve the term to get its slug.
            $term = get_term( $area );
            if ( $term && ! is_wp_error( $term ) ) {
                $allowed_regions[] = $term->slug;
            }
        }
    }

    // Bail early if no valid regions were found.
    if ( empty( $allowed_regions ) ) {
        return false;
    }

    // If 'All Regions' is one of the assigned regions, grant access.
    if ( in_array( 'all-regions', $allowed_regions, true ) ) {
        return true;
    }

    // Check if the user's region matches any of the allowed regions.
    return in_array( $user_region, $allowed_regions, true );
}

/**
 * Handles the AJAX request to clear the session.
 *
 * This function clears the 'region' session variable and responds with a success message.
 */
function clear_session_ajax_handler() {
    if ( session_status() === PHP_SESSION_NONE ) {
        session_start();
    }

    // Clear the session variable.
    unset( $_SESSION['region'] );

    // Respond with success.
    wp_send_json_success( 'Session cleared.' );
}
add_action( 'wp_ajax_clear_session', 'clear_session_ajax_handler' );
add_action( 'wp_ajax_nopriv_clear_session', 'clear_session_ajax_handler' );


// set default region if none is selected.
add_filter(
	'acf/load_value/name=region',
	// ACF filter expects the callback to accept three parameters.
	function ( $value, $post_id, $field ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	    // If no value is set, return the default term.
    	if ( empty( $value ) ) {
        	$default_term = get_term_by( 'slug', 'all-regions', 'region' );
        	return $default_term ? array( $default_term->term_id ) : array(); // Ensure it returns an array if using multi-select.
    	}

    	return $value;
	},
	10,
	3
);

