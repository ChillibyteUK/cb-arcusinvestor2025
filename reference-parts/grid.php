<?php
/**
 * Grid reference partial.
 *
 * Demonstrates Bootstrap 5 grid usage.
 *
 * @package cb-arcusinvestor2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<style>
	.grid-demo {
		border: 1px dashed #ccc;
		background: #f9f9f9;
		font-family: monospace;
		text-align: center;
		padding: 1rem;
	}
	.grid-box {
		background: #dee2e6;
		border: 1px solid #ccc;
		padding: 1rem;
		margin-bottom: 1rem;
	}
</style>

<div class="container-xl">
	<h2>Grid</h2>
	<p>This section demonstrates the default Bootstrap 5 grid system as used in the theme.</p>

	<h3>Basic 12-column Grid</h3>
	<div class="grid-demo container">
		<div class="row">
			<div class="col grid-box">.col</div>
			<div class="col grid-box">.col</div>
			<div class="col grid-box">.col</div>
		</div>
	</div>

	<h3>Responsive Columns</h3>
	<div class="grid-demo container">
		<div class="row">
			<div class="col-sm-6 col-lg-3 grid-box">.col-sm-6 .col-lg-3</div>
			<div class="col-sm-6 col-lg-3 grid-box">.col-sm-6 .col-lg-3</div>
			<div class="col-sm-6 col-lg-3 grid-box">.col-sm-6 .col-lg-3</div>
			<div class="col-sm-6 col-lg-3 grid-box">.col-sm-6 .col-lg-3</div>
		</div>
	</div>

	<h3>Auto Columns</h3>
	<div class="grid-demo container">
		<div class="row">
			<div class="col grid-box">.col</div>
			<div class="col-6 grid-box">.col-6</div>
			<div class="col grid-box">.col</div>
		</div>
	</div>

	<h3>Alignment and Justify</h3>
	<div class="grid-demo container">
		<div class="row justify-content-between">
			<div class="col-4 grid-box">.col-4</div>
			<div class="col-4 grid-box">.col-4</div>
		</div>
	</div>
</div>
