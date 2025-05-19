<?php
/**
 * Template Name: Portal Login
 *
 * Custom login form for portal users.
 *
 * @package cb-arcusportal2025
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/portal-dashboard/' ) ); // Redirect logged-in users.
	exit;
}

$error_msg = '';

if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	check_admin_referer( 'portal_login_form', 'portal_login_nonce' );

	$username = sanitize_user( wp_unslash( $_POST['log'] ?? '' ) );
	$password = sanitize_text_field( wp_unslash( $_POST['pwd'] ?? '' ) );
	$remember = ! empty( $_POST['rememberme'] );
	$redirect = esc_url_raw( wp_unslash( isset( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '' ) ? wp_unslash( $_POST['redirect_to'] ) : home_url() );

	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => $remember,
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		$error_msg = $user->get_error_message();
	} else {
		wp_safe_redirect( $redirect );
		exit;
	}
}

get_header();

?>

<div class="container py-5 mt-5">
	<h1 class="text-center">Investor Portal Login</h1>

	<?php if ( $error_msg ) : ?>
		<div class="alert alert-danger" role="alert">
			<?php
			echo 'Invalid username or password.';
			?>
		</div>
	<?php endif; ?>

	<form method="post" class="login-form">
		<?php wp_nonce_field( 'portal_login_form', 'portal_login_nonce' ); ?>

		<div class="mb-3">
			<label for="user_login" class="form-label"><?php esc_html_e( 'Username or Email', 'your-textdomain' ); ?></label>
			<input type="text" name="log" id="user_login" class="form-control" required value="<?php echo esc_attr( sanitize_user( wp_unslash( $_POST['log'] ?? '' ) ) ); ?>">
		</div>

		<div class="mb-3">
			<label for="user_pass" class="form-label"><?php esc_html_e( 'Password', 'your-textdomain' ); ?></label>
			<input type="password" name="pwd" id="user_pass" class="form-control" required>
		</div>

		<input type="hidden" name="redirect_to" value="<?php echo esc_url( isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/portal-dashboard' ) ); ?>">

		<div class="text-end">
			<button type="submit" class="button"><?php esc_html_e( 'Login', 'your-textdomain' ); ?></button>
		</div>
	</form>
</div>

<?php get_footer(); ?>