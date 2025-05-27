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


if ( isset( $_GET['reset'] ) && 'success' === $_GET['reset'] ) {
	$error_msg = '<div class="alert alert-success" role="alert">Your password has been reset. Please log in.</div>';
} elseif ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
	$error_msg = '<div class="alert alert-danger" role="alert">Invalid username or password.</div>';
} elseif ( isset( $_GET['login'] ) && 'expired' === $_GET['login'] ) {
	$error_msg = '<div class="alert alert-warning" role="alert">Your account has expired. Please contact support.</div>';
}

?>

<div class="container py-5 mt-5">
	<h1 class="text-center">Client Portal Login</h1>

	<form method="post" class="login-form">
		<?php
		if ( $error_msg ) {
			echo wp_kses_post( $error_msg );
		}
		?>
		<?php wp_nonce_field( 'portal_login_form', 'portal_login_nonce' ); ?>

		<div class="mb-3">
			<label for="user_login" class="form-label">Username or Email</label>
			<input type="text" name="log" id="user_login" class="form-control" required value="<?php echo esc_attr( sanitize_user( wp_unslash( $_POST['log'] ?? '' ) ) ); ?>">
		</div>

		<div class="mb-3">
			<label for="user_pass" class="form-label">Password</label>
			<input type="password" name="pwd" id="user_pass" class="form-control" required>
		</div>

		<input type="hidden" name="redirect_to" value="<?php echo esc_url( isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/portal-dashboard' ) ); ?>">

		<div class="text-end">
			<button type="submit" class="button">Login</button>
		</div>
	</form>
	<div class="text-center my-4">
		<a href="/request-access/">Request Access</a> | 
		<a href="/forgot-password/">Forgotten Password</a>
	</div>
</div>



<?php get_footer(); ?>