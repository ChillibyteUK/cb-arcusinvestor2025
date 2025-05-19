<?php
/**
 * Utility functions for the CB Saascada 2025 theme.
 *
 * This file contains various helper functions, shortcodes, and customizations
 * to enhance the functionality of the theme.
 *
 * @package CB_Saascada2025
 */

/**
 * Parses a phone number and formats it by removing unnecessary characters.
 *
 * @param string $phone The phone number to parse.
 * @return string The formatted phone number.
 */
function parse_phone( $phone ) {
    $phone = preg_replace( '/\s+/', '', $phone );
    $phone = preg_replace( '/\(0\)/', '', $phone );
    $phone = preg_replace( '/[\(\)\.]/', '', $phone );
    $phone = preg_replace( '/-/', '', $phone );
    $phone = preg_replace( '/^0/', '+44', $phone );
    return $phone;
}

/**
 * Splits lines in the given content by replacing <br /> tags with additional spacing.
 *
 * @param string $content The content to process.
 * @return string The processed content with modified line breaks.
 */
function split_lines( $content ) {
    $content = preg_replace( '/<br \/>/', '<br>&nbsp;<br>', $content );
    return $content;
}

add_shortcode(
	'contact_address',
	function () {
		$output = get_field( 'contact_address', 'option' );
		return $output;
	}
);

add_shortcode(
	'contact_phone',
	function () {
    	if ( get_field( 'contact_phone', 'option' ) ) {
    	    return '<a href="tel:' . parse_phone( get_field( 'contact_phone', 'option' ) ) . '">' . esc_html( get_field( 'contact_phone', 'option' ) ) . '</a>';
		}
	}
);
add_shortcode(
	'contact_email',
	function () {
		if ( get_field( 'contact_email', 'option' ) ) {
			return '<a href="mailto:' . get_field( 'contact_email', 'option' ) . '">' . esc_html( get_field( 'contact_email', 'option' ) ) . '</a>';
		}
	}
);

add_shortcode(
	'contact_email_icon',
	function () {
		if ( get_field( 'contact_email', 'option' ) ) {
			return '<a href="mailto:' . get_field( 'contact_email', 'option' ) . '"><i class="fas fa-envelope"></i></a>';
		}
	}
);


/**
 * Generates a social icon shortcode based on the provided type.
 *
 * @param array $atts Attributes passed to the shortcode.
 * @return string The HTML for the social icon or an empty string if the type is invalid.
 */
function lc_social_icon_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'type' => '' ), $atts );
    if ( ! $atts['type'] ) {
        return '';
    }

    $social = get_field( 'socials', 'option' );
	$urls   = array(
        'facebook'  => $social['facebook_url'] ?? '',
        'instagram' => $social['instagram_url'] ?? '',
        'twitter'   => $social['twitter_url'] ?? '',
        'pinterest' => $social['pinterest_url'] ?? '',
        'youtube'   => $social['youtube_url'] ?? '',
        'linkedin'  => $social['linkedin_url'] ?? '',
	);

    if ( ! isset( $urls[ $atts['type'] ] ) || empty( $urls[ $atts['type'] ] ) ) {
        return '';
    }

    $url  = esc_url( $urls[ $atts['type'] ] );
    $icon = esc_attr( $atts['type'] );

    return '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
}

// Register individual social icon shortcodes.
$social_types = array( 'facebook', 'instagram', 'twitter', 'pinterest', 'youtube', 'linkedin' );
foreach ( $social_types as $social_type ) {
    add_shortcode(
		'social_' . $social_type . '_icon',
		function () use ( $social_type ) {
            return lc_social_icon_shortcode( array( 'type' => $social_type ) );
    	}
	);
}

// Generate a single shortcode to output all social icons.
add_shortcode(
	'social_icons',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts,
			'social_icons'
		);

		$social = get_field( 'social', 'option' );
		if ( ! $social ) {
			return '';
		}

		$icons      = array();
		$social_map = array(
			'facebook'  => 'facebook-f',
			'instagram' => 'instagram',
			'twitter'   => 'x-twitter',
			'pinterest' => 'pinterest',
			'youtube'   => 'youtube',
			'linkedin'  => 'linkedin',
		);

		foreach ( $social_map as $key => $icon ) {
			if ( ! empty( $social[ $key . '_url' ] ) ) {
				$url     = esc_url( $social[ $key . '_url' ] );
				$icons[] = '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
			}
		}

    	$class = esc_attr( trim( $atts['class'] ) );

	    return ! empty( $icons ) ? '<div class="social-icons ' . $class . '">' . implode( ' ', $icons ) . '</div>' : '';
	}
);


/**
 * Grab the specified data like Thumbnail URL of a publicly embeddable video hosted on Vimeo.
 *
 * @param  str $video_id The ID of a Vimeo video.
 * @param  str $data     Video data to be fetched.
 * @return str           The specified data
 */
function get_vimeo_data_from_id( $video_id, $data ) {
    // width can be 100, 200, 295, 640, 960 or 1280.
    $request = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/' . $video_id . '&width=960' );

    $response = wp_remote_retrieve_body( $request );

    $video_array = json_decode( $response, true );

    return $video_array[ $data ];
}

/**
 * Adds custom styles to the Gutenberg editor in the WordPress admin area.
 *
 * This function outputs CSS styles to adjust the width of blocks and prevent
 * horizontal overflow flicker in the block editor.
 */
function cb_gutenberg_admin_styles() {
    echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1040px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }

            /* stop horizontal overflow flicker */
            .block-editor-page #wpwrap {
                overflow-y: auto !important;
            }
        </style>
    ';
}
add_action( 'admin_head', 'cb_gutenberg_admin_styles' );


// disable full-screen editor view by default.
if ( is_admin() ) {
    /**
     * Disables the fullscreen editor mode by default in the WordPress block editor.
     *
     * This function checks if the fullscreen mode is active and toggles it off.
     */
    function cb_disable_editor_fullscreen_by_default() {
        $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
        wp_add_inline_script( 'wp-blocks', $script );
    }
    add_action( 'enqueue_block_editor_assets', 'cb_disable_editor_fullscreen_by_default' );
}


// God I hate Gravity Forms.
// Change textarea rows to 4 instead of 10.
add_filter(
	'gform_field_content',
	function ( $field_content, $field ) {
        if ( 'textarea' === $field->type ) {
			return str_replace( "rows='10'", "rows='4'", $field_content );
		}
    	return $field_content;
	},
	10,
	2
);


/**
 * Retrieves the ID of the top-level ancestor of the current post.
 *
 * This function checks if the current post has a parent. If it does, it retrieves
 * the top-level ancestor by traversing the post's ancestry. If the post has no parent,
 * it returns the current post's ID.
 *
 * @return int The ID of the top-level ancestor or the current post's ID if no parent exists.
 */
function get_the_top_ancestor_id() {
    global $post;
    if ( $post->post_parent ) {
        $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
        return $ancestors[0];
    } else {
        return $post->ID;
    }
}

/**
 * Encodes a string into JSON format with additional escaping for special characters.
 *
 * This function replaces specific characters in the input string with their escaped equivalents
 * before encoding it into JSON format.
 *
 * @param string $input_string The input string to encode.
 * @return string The JSON-encoded string with additional escaping.
 */
function cb_json_encode( $input_string ) {
    $escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
    $replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
    $result       = str_replace( $escapers, $replacements, $input_string );
    $result       = wp_json_encode( $result );
    return $result;
}

/**
 * Converts a time string in HH:MM:SS format to ISO 8601 duration format.
 *
 * @param string $input_string The input time string in HH:MM:SS format.
 * @return string The time string converted to ISO 8601 duration format.
 */
function cb_time_to_8601( $input_string ) {
    $time   = explode( ':', $input_string );
    $output = 'PT' . $time[0] . 'H' . $time[1] . 'M' . $time[2] . 'S';
    return $output;
}


/**
 * Dumps a variable in a readable format for debugging purposes.
 *
 * @param mixed $data The variable to be dumped.
 */
function cbdump( $data ) {
    echo '<pre>';
    print_r( $data, true );
    echo '</pre>';
}

/**
 * Converts a string into a slug format.
 *
 * This function replaces non-alphanumeric characters with a specified divider,
 * transliterates the text to ASCII, removes unwanted characters, trims, and
 * converts the text to lowercase.
 *
 * @param string $text    The input string to be slugified.
 * @param string $divider The character to replace non-alphanumeric characters with.
 * @return string The slugified version of the input string.
 */
function cbslugify( $text, string $divider = '-' ) {
    // replace non letter or digits by divider.
    $text = preg_replace( '~[^\pL\d]+~u', $divider, $text );

    // transliterate.
    $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

    // remove unwanted characters.
    $text = preg_replace( '~[^-\w]+~', '', $text );

    // trim.
    $text = trim( $text, $divider );

    // remove duplicate divider.
    $text = preg_replace( '~-+~', $divider, $text );

    // lowercase.
    $text = strtolower( $text );

    if ( empty( $text ) ) {
        return 'n-a';
    }

    return $text;
}

/**
 * Generates a random string of a specified length using a given keyspace.
 *
 * @param int    $length   The length of the random string to generate. Default is 64.
 * @param string $keyspace The set of characters to use for generating the string. Default is alphanumeric.
 * @return string          The generated random string.
 * @throws \RangeException  If the length is less than 1.
 */
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ( $length < 1 ) {
        throw new \RangeException( 'Length must be a positive integer' );
    }
    $pieces = array();
    $max    = mb_strlen( $keyspace, '8bit' ) - 1;
    for ( $i = 0; $i < $length; ++$i ) {
        $pieces[] = $keyspace[ random_int( 0, $max ) ];
    }
    return implode( '', $pieces );
}

/**
 * Generates social share links for a given post ID.
 *
 * This function outputs social media share links for Twitter, LinkedIn, and Facebook
 * for the specified post ID.
 *
 * @param int $id The ID of the post to generate share links for.
 * @return string The HTML output of the social share links.
 */
function cb_social_share( $id ) {
    ob_start();
    $url = get_the_permalink( $id );

	?>
    <div class="text-larger text--yellow mb-5">
        <div class="h4 text-dark">Share</div>
        <a target='_blank' href='<?= esc_url( 'https://twitter.com/share?url=' . $url ); ?>' class="mr-2"><i class='fab fa-twitter'></i></a>
        <a target='_blank' href='<?= esc_url( 'http://www.linkedin.com/shareArticle?url=' . $url ); ?>' class="mr-2"><i class='fab fa-linkedin-in'></i></a>
        <a target='_blank' href='<?= esc_url( 'http://www.facebook.com/sharer.php?u=' . $url ); ?>'><i class='fab fa-facebook-f'></i></a>
    </div>
    <?php

    $out = ob_get_clean();
    return $out;
}


/**
 * Enables the Strict-Transport-Security (HSTS) header.
 *
 * This function sends the HSTS header to enforce HTTPS connections
 * for the site, with a max-age of 1 year.
 */
function enable_strict_transport_security_hsts_header() {
    header( 'Strict-Transport-Security: max-age=31536000' );
}
add_action( 'send_headers', 'enable_strict_transport_security_hsts_header' );


/**
 * Converts a string with line breaks into an HTML list.
 *
 * This function takes a string with line breaks and converts each line into
 * a list item (<li>) within an unordered list (<ul>).
 *
 * @param string $field The input string containing line breaks.
 * @return string The HTML output of the unordered list.
 */
function cb_list( $field ) {
    ob_start();
    $field   = strip_tags( $field, '<br />' );
    $bullets = preg_split( "/\r\n|\n|\r/", $field );
    foreach ( $bullets as $b ) {
        if ( '' === $b ) {
            continue;
        }
    	?>
        <li><?= wp_kses_post( $b ); ?></li>
		<?php
    }
    return ob_get_clean();
}


/**
 * Converts a file size in bytes to a human-readable format.
 *
 * @param int $size      The size in bytes.
 * @param int $precision The number of decimal places to round to. Default is 2.
 * @return string        The formatted size with appropriate unit (e.g., KB, MB).
 */
function format_bytes( $size, $precision = 2 ) {
    if ( $size <= 0 ) {
        return '0 B'; // Return 0 bytes as a default value.
    }

    $base     = log( $size, 1024 );
    $suffixes = array( '', 'K', 'M', 'G', 'T' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) . ' ' . $suffixes[ floor( $base ) ];
}




// REMOVE TAG AND COMMENT SUPPORT.

/**
 * Removes the Tags submenu from the WordPress Dashboard.
 *
 * This function disables the Tags submenu under the Posts menu
 * in the WordPress admin area.
 */
function my_remove_sub_menus() {
    remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
}
add_action( 'admin_menu', 'my_remove_sub_menus' );

/**
 * Removes tags support from posts.
 *
 * This function unregisters the 'post_tag' taxonomy for the 'post' post type,
 * effectively disabling tags for posts.
 */
function myprefix_unregister_tags() {
    unregister_taxonomy_for_object_type( 'post_tag', 'post' );
}
add_action( 'init', 'myprefix_unregister_tags' );

add_action(
	'admin_init',
	function () {
		// Redirect any user trying to access comments page.
		global $pagenow;

        if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

    	// Remove comments metabox from dashboard.
    	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

	    // Disable support for comments and trackbacks in post types.
	    foreach ( get_post_types() as $post_type ) {
    	    if ( post_type_supports( $post_type, 'comments' ) ) {
        	    remove_post_type_support( $post_type, 'comments' );
            	remove_post_type_support( $post_type, 'trackbacks' );
	        }
    	}
	}
);

// Close comments on the front-end.
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments.
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments page in menu.
add_action(
	'admin_menu',
	function () {
    	remove_menu_page( 'edit-comments.php' );
	}
);

// Remove comments links from admin bar.
add_action(
	'init',
	function () {
    	if ( is_admin_bar_showing() ) {
        	remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
    	}
	}
);

/**
 * Removes the comments menu from the WordPress admin bar.
 *
 * This function ensures that the comments menu is not displayed
 * in the WordPress admin bar for all users.
 */
function remove_comments() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'remove_comments' );


// phpcs:disable
// // HIDE POSTS.
// function remove_posts_admin_menu() {
//     remove_menu_page('edit.php'); // Removes the "Posts" menu
// }
// add_action('admin_menu', 'remove_posts_admin_menu');
// phpcs:enable

/**
 * Removes specific widgets from the WordPress dashboard.
 *
 * This function removes core WordPress widgets like "At a Glance," "Activity,"
 * "Quick Draft," and "WordPress Events and News," as well as Yoast SEO widgets.
 */
function lc_remove_dashboard_widgets() {
    // Core WordPress widgets.
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // "At a Glance"
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // "Activity"
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // "Quick Draft"
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // "WordPress Events and News"

    // Yoast SEO Widgets.
    remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' ); // "Yoast SEO Posts Overview"
    remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'normal' ); // "Yoast SEO / Wincher: Top Keyphrases"
}
add_action( 'wp_dashboard_setup', 'lc_remove_dashboard_widgets' );


/**
 * Estimates the reading time for a given content in minutes.
 *
 * @param string $content          The content to estimate reading time for.
 * @param int    $words_per_minute The average words per minute for reading. Default is 300.
 * @param bool   $with_gutenberg   Whether the content is built with Gutenberg blocks. Default is false.
 * @param bool   $formatted        Whether to return the result as formatted HTML. Default is false.
 * @return int|string              The estimated reading time in minutes, or formatted HTML if $formatted is true.
 */
function estimate_reading_time_in_minutes( $content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false ) {
    // In case if content is build with gutenberg parse blocks.
    if ( $with_gutenberg ) {
        $blocks       = parse_blocks( $content );
        $content_html = '';

        foreach ( $blocks as $block ) {
            $content_html .= render_block( $block );
        }

        $content = $content_html;
    }

    // Remove HTML tags from string.
    $content = wp_strip_all_tags( $content );

    // When content is empty return 0.
    if ( ! $content ) {
        return 0;
    }

    // Count words containing string.
    $words_count = str_word_count( $content );

    // Calculate time for read all words and round.
    $minutes = ceil( $words_count / $words_per_minute );

    if ( $formatted ) {
        $minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise( $minutes, 'minute' ) . '</p>';
    }

    return $minutes;
}

/**
 * Returns the plural or singular form of a word based on the quantity.
 *
 * @param int    $quantity The quantity to determine singular or plural form.
 * @param string $singular The singular form of the word.
 * @param string $plural   Optional. The plural form of the word. If not provided, it will be auto-generated.
 * @return string The appropriate singular or plural form of the word.
 */
function pluralise( $quantity, $singular, $plural = null ) {
    if ( 1 === $quantity || ! strlen( $singular ) ) {
        return $singular;
    }
    if ( null !== $plural ) {
        return $plural;
    }

    $last_letter = strtolower( $singular[ strlen( $singular ) - 1 ] );
    switch ( $last_letter ) {
        case 'y':
            return substr( $singular, 0, -1 ) . 'ies';
        case 's':
            return $singular . 'es';
        default:
            return $singular . 's';
    }
}

/**
 * Retrieves all block names from the content of a given post.
 *
 * This function parses the blocks in the post content and extracts the names
 * of all blocks, including nested ones, ensuring no duplicates.
 *
 * @param int $id The ID of the post whose content is to be parsed.
 * @return array An array of unique block names found in the post content.
 */
function get_all_block_names_from_content( $id ) {
    // Parse blocks from the content.
    $content     = get_post_field( 'post_content', $id );
    $blocks      = parse_blocks( $content );
    $block_names = array();

    // Recursively find all block names.
    foreach ( $blocks as $block ) {
        if ( isset( $block['blockName'] ) && ! empty( $block['blockName'] ) ) {
            $block_names[] = $block['blockName'];
        }
        if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {
            $inner_block_names = get_all_block_names_from_content( serialize_blocks( $block['innerBlocks'] ) );
            $block_names       = array_merge( $block_names, $inner_block_names );
        }
    }

    // Remove duplicates and reindex.
    return array_values( array_unique( $block_names ) );
}

/**
 * Displays pages in a hierarchical list.
 *
 * This function retrieves and displays pages in a hierarchical structure,
 * excluding the posts page and sorting them alphabetically by title.
 *
 * @param int $parent_id The ID of the parent page. Default is 0 (top-level pages).
 * @return string The HTML output of the hierarchical page list.
 */
function display_page_hierarchy( $parent_id = 0 ) {

    $posts_page_id = get_option( 'page_for_posts' );

    // Get the pages with the specified parent, sorted by title.
    $args = array(
        'post_type'   => 'page',
        'post_status' => 'publish',
        'parent'      => $parent_id,
        'sort_column' => 'post_title',  // Sort by post title for alphabetical order.
        'sort_order'  => 'ASC',         // Sort ascending (A-Z).
        'exclude'     => $posts_page_id, // Exclude the posts page (blog index).
    );

    $pages = get_pages( $args );

    $output = '';

    if ( ! empty( $pages ) ) {
        $output .= '<ul>';
        foreach ( $pages as $page ) {
            // check index status.
            $noindex = get_post_meta( $page->ID, '_yoast_wpseo_meta-robots-noindex', true );

            if ( '1' !== $noindex ) {
                $output .= '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( $page->post_title ) . '</a>';

                // Recursively display child pages, also sorted by title.
                $output .= display_page_hierarchy( $page->ID ); // Get nested child pages.

                $output .= '</li>';
            }
        }
        $output .= '</ul>';
    }

    return $output;
}

/**
 * Registers a shortcode to display a hierarchical page list.
 *
 * This function outputs a hierarchical list of pages using the `display_page_hierarchy` function.
 *
 * @return string The HTML output of the hierarchical page list.
 */
function register_page_list_shortcode() {
    // Start output buffering.
    ob_start();

    // Display the hierarchical list.
    echo wp_kses_post( display_page_hierarchy() );

    // Return the buffered content.
    return ob_get_clean();
}
add_shortcode( 'page_list', 'register_page_list_shortcode' );


/**
 * Retrieves all H2 headings from the Gutenberg content of the current page.
 *
 * This function parses the Gutenberg blocks of the current post's content,
 * extracts H2 headings, and returns them along with their IDs if available.
 *
 * @return array An array of H2 headings, each containing 'content' and 'id'.
 */
function get_gutenberg_h2_headings_from_page() {
    global $post;

    // Get the raw post content.
    $content = $post->post_content;

    // Parse the blocks in the content.
    $blocks = parse_blocks( $content );

    $h2_headings = array();
    $stack       = $blocks; // Use a stack to process blocks iteratively.

    while ( ! empty( $stack ) ) {
        $block = array_shift( $stack ); // Get the blocks in order.

        // Check if it's a heading block.
        if ( 'core/heading' === $block['blockName'] ) {
            // Check the level attribute (assume default is 2 if not set).
            $level = $block['attrs']['level'] ?? 2;

            if ( 2 === $level ) {
                // Extract the rendered HTML.
                $html = $block['innerHTML'] ?? '';

                // Extract the ID using regex.
                if ( preg_match( '/<h2[^>]*id=["\']([^"\']+)["\']/', $html, $matches ) ) {
                    $id = $matches[1];
                } else {
                    $id = null;
                }

                // Extract the content.
                $content = wp_strip_all_tags( $html );

                // Add the H2 heading to the array.
                $h2_headings[] = array(
                    'content' => $content,
                    'id'      => $id,
				);
            }
        }

        // Add inner blocks to the stack.
        if ( ! empty( $block['innerBlocks'] ) ) {
            $stack = array_merge( $stack, $block['innerBlocks'] );
        }
    }

    return $h2_headings;
}


/**
 * Disables TinyMCE cleanup to prevent stripping of JavaScript.
 *
 * @param array $options The TinyMCE options array.
 * @return array The modified TinyMCE options array.
 */
function disable_tinymce_cleanup( $options ) {
    $options['verify_html'] = false;
    return $options;
}
add_filter( 'tiny_mce_before_init', 'disable_tinymce_cleanup' );


// phpcs:disable
// CUSTOM LOGIN URL.
/*
add_action('init', 'custom_login_url');
function custom_login_url() {
    $custom_slug = 'my-custom-login'; // Change this to your desired slug

    // Check if the current request is for the default wp-login.php
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        wp_redirect(home_url('/' . $custom_slug));
        exit;
    }

    // Load the default login functionality for the custom slug
    if (strpos($_SERVER['REQUEST_URI'], $custom_slug) !== false) {
        require_once ABSPATH . 'wp-login.php';
        exit;
    }
}

// Prevent direct access to wp-admin for non-logged-in users
add_action('template_redirect', 'redirect_non_logged_users');
function redirect_non_logged_users() {
    if (is_admin() && !is_user_logged_in()) {
        wp_redirect(home_url('/my-custom-login'));
        exit;
    }
}
*/
// phpcs:enable