<?php
/**
 * Theme colour reference output from _props.scss
 *
 * @package cb-arcusinvestor2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Path to _props.scss.
$file_path = get_stylesheet_directory() . '/src/sass/theme/_props.scss';
if ( ! file_exists( $file_path ) ) {
	echo '<p>Could not find _props.scss</p>';
	return;
}

$file_contents = file_get_contents( $file_path );

preg_match_all( '/--(?<name>[a-z0-9\-]+):\s*(?<value>[^;]+);/i', $file_contents, $matches, PREG_SET_ORDER );

$vars = array();

foreach ( $matches as $match ) {
	$name          = trim( $match['name'] );
	$value         = trim( $match['value'] );
	$vars[ $name ] = $value;
}

// Replace nested vars.
foreach ( $vars as $name => $value ) {
	if ( str_contains( $value, 'var(--' ) ) {
		preg_match( '/var\(--(?<var>[^)]+)\)/', $value, $submatch );
		$ref = $submatch['var'] ?? '';
		if ( isset( $vars[ $ref ] ) ) {
			$vars[ $name ] = str_replace( "var(--$ref)", $vars[ $ref ], $value );
		}
	}
}

$colours = array_filter(
	$vars,
	fn( $key ) => str_starts_with( $key, 'col-' ),
	ARRAY_FILTER_USE_KEY
);

ksort( $colours );

?>
<style>
	.colour-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
		gap: 1.5rem;
	}
	.colour-card {
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		font-family: monospace;
		background: #fff;
	}
	.colour-preview {
		width: 100%;
		height: 50px;
		border-radius: 4px;
		border: 1px solid #bbb;
	}
	.colour-meta code {
		color: #555;
		display: block;
		margin-top: 0.25rem;
	}
</style>
<div class="container-xl">
	<h1>Colours</h1>
	<p>From <code>src/sass/theme/_props.scss</code></p>

	<div class="colour-grid">
		<?php
		foreach ( $colours as $name => $value ) {
			$hex = '';
			$rgb = '';
			if ( str_starts_with( $value, 'hsl(' ) ) {
				$hsl           = trim( $value, 'hsl()' );
				[ $hex, $rgb ] = hsl_to_rgb_hex( $hsl );
			}
			?>
			<div class="colour-card">
				<div class="colour-preview" style="background-color: var(--<?= esc_attr( $name ); ?>);"></div>
				<strong>--<?= esc_html( $name ); ?></strong>
				<code><?= esc_html( $value ); ?></code>
				<?php if ( $hex && $rgb ) : ?>
					<code><?= esc_html( $hex ); ?></code>
					<code><?= esc_html( $rgb ); ?></code>
				<?php endif; ?>
			</div>
			<?php
		}
		?>
	</div>
</div>