<?php
/**
 * Template for displaying author archive pages.
 *
 * @package cb-arcusinvestor2025
 */

defined( 'ABSPATH' ) || exit;

$page_for_posts = get_option( 'page_for_posts' );
get_header();
?>
<main id="main">
	<div class="page_hero">
		<?= get_the_post_thumbnail( $page_for_posts, 'full' ); ?>
	</div>
	<section class="translucent_text--light">
		<div class="container p-5">
			<h1 class="insights-title">
				<?php
                if ( get_query_var( 'author_name' ) ) {
                    $curauth = get_user_by( 'slug', get_query_var( 'author_name' ) );
                } else {
                    $curauth = get_userdata( intval( $author ) );
                }
				echo esc_html( $curauth->display_name );
				?>
			</h1>
		</div>
	</section>
	<div class="container bg--white p-5">
		<div class="row w-100 g-5">
			<div class="col-md-8">
				<div class="row">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
						if ( ! $img ) {
							$img = get_stylesheet_directory_uri() . '/img/default-blog.jpg';
						}
						?>
				<div class="col-md-6">
					<a class="latest_insights__card" href="<?= esc_url( get_the_permalink() ); ?>">
						<?= get_the_post_thumbnail( $q->ID, 'large', array( 'class' => 'latest_insights__image' ) ); ?>
						<h3 class="latest_insights__post-title"><?= esc_html( get_the_title() ); ?></h3>
						<div class="latest_insights__intro">
							<?= wp_kses_post( get_field( 'post_excerpt', get_the_ID() ) ? get_field( 'post_excerpt', get_the_ID() ) : wp_trim_words( get_the_content(), 30 ) ); ?>
						</div>
					</a>
				</div>
						<?php
					}
				}
				?>
				</div>
			</div>
			<div class="col-md-4">
				<?php
                // Get the current author's user ID.
                $author_id = get_queried_object_id();

				// Query the 'people' post associated with this author.
				$people_query = new WP_Query(
					array(
						'post_type'      => 'people',
						'meta_key'       => 'author', // ACF field key.
						'meta_value'     => $author_id,
						'posts_per_page' => 1,
					)
				);

				// Check if we found a matching People post.
				if ( $people_query->have_posts() ) {
					$people_query->the_post();
					$people_post_id = get_the_ID();
					wp_reset_postdata();
				} else {
					$people_post_id = null; // No associated people post.
				}

				if ( $people_post_id ?? null ) {
					?>
				<div class="person_panel">
					<?= get_the_post_thumbnail( $people_post_id, 'large', array( 'class' => 'person_panel__image' ) ); ?>
					<div class="person_panel__name">About <?= esc_html( get_the_title( $people_post_id ) ); ?></div>
					<?= wp_kses_post( get_the_content( null, false, $people_post_id ) ); ?>
				</div>
					<?php
				}
				?>
			</div>	
		</div>
	</div>
</main>
<?php
// Display the pagination component.
understrap_pagination();

get_footer();
