<?php
/**
 * Footer template for the CB Arcus 2025 theme.
 *
 * This file contains the footer section of the theme, including navigation menus,
 * office addresses, and colophon information.
 *
 * @package cb-arcusinvestor2025
 */

defined( 'ABSPATH' ) || exit;
define( 'CB_RENDERING_FOOTER', true );
?>
<div id="footer-top"></div>
<footer class="footer py-5">
    <div class="container-xl">
        <img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/arcus-logo--wo.svg' ); ?>" alt="Arcus Invest" class="footer__logo">
        <div class="row pb-5">
            <div class="col-lg-3">
            </div>
            <div class="col-lg-9">
                <div class="footer__addresses--label">
                    Offices
                </div>
                <div class="row footer__addresses">
                    <div class="col-md-4 footer__office mb-4">
                        <?php
                        $uk = get_field( 'uk_address', 'option' );
                        if ( $uk ) {
                        	?>
                            <div class="footer__title"><?= esc_html( $uk['office_name'] ); ?></div>
                            <div class="footer__address"><?= wp_kses_post( $uk['office_address'] ); ?></div>
                            <div class="footer__phone"><?= esc_html( $uk['office_phone'] ); ?></div>
                        	<?php
                        }
                        ?>
                    </div>
                    <div class="col-md-4 footer__office mb-4">
						<?php
                        $jp = get_field( 'jp_address', 'option' );
                        if ( $jp ) {
                        	?>
                            <div class="footer__title"><?= esc_html( $jp['office_name'] ); ?></div>
                            <div class="footer__address"><?= wp_kses_post( $jp['office_address'] ); ?></div>
                            <div class="footer__phone"><?= esc_html( $jp['office_phone'] ); ?></div>
                        	<?php
                        }
                        ?>
                    </div>
					<div class="col-md-4 footer__office mb-4">
						<?php
                        $my = get_field( 'my_address', 'option' );
                        if ( $my ) {
	                        ?>
                            <div class="footer__title"><?= esc_html( $my['office_name'] ); ?></div>
                            <div class="footer__address"><?= wp_kses_post( $my['office_address'] ); ?></div>
                            <div class="footer__phone"><?= esc_html( $my['office_phone'] ); ?></div>
    	                    <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="colophon d-flex justify-content-between align-items-center flex-wrap">
            <div>
                &copy; <?= esc_html( gmdate( 'Y' ) ); ?> Arcus Investment Limited. All rights reserved. Authorised and regulated by the <a href="https://www.fca.org.uk/" target="_blank">Financial Conduct Authority</a> in the United Kingdom.
            </div>
            <div>
                <a href="https://www.chillibyte.co.uk/" rel="nofollow noopener" target="_blank" class="cb"
                title="Digital Marketing by Chillibyte"></a>
            </div>
        </div>
</footer>
<?php wp_footer(); ?>
</body>

</html>