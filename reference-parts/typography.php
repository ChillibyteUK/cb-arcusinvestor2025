<?php
/**
 * Theme typography reference output from _props.scss
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

$families = array();
$weights  = array();
$sizes    = array();

foreach ( $matches as $match ) {
	$name  = trim( $match['name'] );
	$value = trim( $match['value'] );

	if ( str_starts_with( $name, 'ff-' ) ) {
		$families[ $name ] = $value;
	} elseif ( str_starts_with( $name, 'fw-' ) ) {
		$weights[ $name ] = $value;
	} elseif ( str_starts_with( $name, 'fs-' ) ) {
		$sizes[ $name ] = $value;
	}
}

// Sort font sizes by descending numeric value.
uksort(
	$sizes,
	function ( $a, $b ) {
		return intval( explode( '-', $b )[1] ) <=> intval( explode( '-', $a )[1] );
	}
);
?>

<style>
	.typography-grid {
		display: grid;
		gap: 1.5rem;
		margin-bottom: 2rem;
	}
	.two-col {
		grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	}
	.four-col {
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	}
	.single-col {
		grid-template-columns: 1fr;
	}
	.card {
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		font-family: monospace;
		background: #fff;
	}
	.demo {
		margin-top: 0.5rem;
	}
</style>

<div class="container-xl">
	<h1>Typography</h1>
	<p>From <code>src/sass/theme/_props.scss</code></p>

	<h2>Font Families</h2>
	<div class="typography-grid two-col">
		<?php foreach ( $families as $name => $value ) : ?>
			<div class="card">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code><?= esc_html( $value ); ?></code>
				<p class="demo" style="font-family: var(--<?= esc_attr( $name ); ?>);">The quick brown fox jumps over the lazy dog.</p>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Font Weights</h2>
	<div class="typography-grid four-col">
		<?php foreach ( $weights as $name => $value ) : ?>
			<div class="card">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code><?= esc_html( $value ); ?></code>
				<p class="demo" style="font-weight: var(--<?= esc_attr( $name ); ?>); font-family: var(--ff-body);">The quick brown fox</p>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Font Sizes</h2>
	<div class="typography-grid single-col">
		<?php foreach ( $sizes as $name => $value ) : ?>
			<div class="card">
				<strong>--<?= esc_html( $name ); ?></strong>
				<code><?= esc_html( $value ); ?></code>
				<p class="demo" style="font-size: var(--<?= esc_attr( $name ); ?>); font-family: var(--ff-body);">The quick brown fox jumps over the lazy dog.</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
