<?php
/**
 * Template for displaying single posts.
 *
 * @package cb-arcusinvestor2025
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="blog">
    <div class="page_hero">
        <?= get_the_post_thumbnail( get_the_ID(), 'full' ); ?>
    </div>
    <section class="translucent_text--light">
        <div class="container p-5">
            <div class="insights-title">Insights</div>
            <?php
            if ( function_exists( 'yoast_breadcrumb' ) ) {
                yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
            }
			?>
        </div>
    </section>
    <div class="container p-5 bg--white">
        <div class="row">
            <div class="col-md-8">
                <h1 class="h2"><?= esc_html( get_the_title() ); ?></h1>
                <?php
                if ( get_field( 'post_excerpt' ) ) {
                    ?>
                    <p class="fs-500"><?= wp_kses_post( get_field( 'post_excerpt' ) ); ?></p>
                    <?php
                }
				// phpcs:disable
				// no read time at the moment as the articles are very short
				// $count = estimate_reading_time_in_minutes(get_the_content(), 200, true, true) ?? null;
				// if ($count) {
				//     echo $count;
				// }
				// phpcs:enable
				?>
                <div class="post_meta">
                    <a class="post_meta__author" href="<?= esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?= get_the_author(); ?></a>
                    <span class="post_meta__date"><?= esc_html( get_the_date( 'jS F Y' ) ); ?></span>
                </div>
                <?php
				echo wp_kses_post( get_the_content() );
				?>
            </div>
            <div class="col-md-4">
                <?= get_the_post_thumbnail( get_the_ID(), 'full' ); ?>
            </div>
        </div>
    </div>
    <div class="container latest_insights has-grey-100-background-color p-5">
        <div class="h3 mb-5">Further reading</div>
        <div class="row w-100">
            <?php
            $r = new WP_Query(
				array(
					'posts_per_page' => 3,
					'post__not_in'   => array( get_the_ID() ),
            	)
			);
			while ( $r->have_posts() ) {
    			$r->the_post();
    			?>
            <div class="col-md-4">
            <a class="latest_insights__card" href="<?= esc_url( get_the_permalink() ); ?>">
                        <?= get_the_post_thumbnail( $r->ID, 'large', array( 'class' => 'latest_insights__image' ) ); ?>
                        <h3 class="latest_insights__post-title"><?= esc_html( get_the_title() ); ?></h3>
                        <div class="latest_insights__intro">
                            <?= wp_kses_post( get_field( 'post_excerpt', get_the_ID() ) ? get_field( 'post_excerpt', get_the_ID() ) : wp_trim_words( get_the_content(), 30 ) ); ?>
                        </div>
                    </a>
            </div>
                <?php
			}
			?>
        </div>
    </div>
</main>
<?php
get_footer();
?>