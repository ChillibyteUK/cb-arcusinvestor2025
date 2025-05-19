<?php
/**
 * Button reference partial.
 *
 * Scans compiled CSS and lists all `.button` variations in a visual format.
 *
 * @package cb-arcusinvestor2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$css_file    = get_stylesheet_directory() . '/css/child-theme.css';
$css_content = file_get_contents( $css_file );

// Match any class that starts with `.button` or is a modifier like `.button--wo`.
preg_match_all( '/\.button(?:[a-zA-Z0-9\-_]*)?\b/', $css_content, $matches );

$buttons = array_unique( $matches[0] );
sort( $buttons );
?>

<style>
	.button-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 1.5rem;
		margin-top: 2rem;
	}
	.button-card {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
		font-family: monospace;
		align-items: start;
	}
</style>

<div class="container-xl">
	<h2>Buttons</h2>
	<p>Scanned from <code>css/child-theme.css</code>. All variants of the <code>.button</code> class (and its modifiers) are shown below.</p>

	<div class="button-grid">
		<?php
		foreach ( $buttons as $button_class ) {
			// Build class attribute from the selector.
			$class_attr = implode( ' ', array_filter( explode( '.', $button_class ) ) );
			?>
			<div class="button-card">
				<a class="<?= esc_attr( $class_attr ); ?>" href="#">Sample Button</a>
				<code><?= esc_html( $class_attr ); ?></code>
			</div>
			<?php
		}
		?>
	</div>
</div>
