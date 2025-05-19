<?php
/**
 * Template Name: Page
 *
 * @package cb-arcusinvestor2025
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main">
    <?php
    the_post();
    the_content();
	// phpcs:disable
    // $block_names = get_all_block_names_from_content(get_the_ID());
    // print_r($block_names);
	// phpcs:enable
    ?>
</main>
<?php
get_footer();
