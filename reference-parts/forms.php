<?php
/**
 * Forms reference partial.
 *
 * Demonstrates standard Bootstrap 5 form elements as styled in the theme.
 *
 * @package cb-arcusinvestor2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<style>
	.form-card {
		display: flex;
		flex-direction: column;
		gap: 1rem;
		padding: 1.5rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
		max-width: 600px;
	}
</style>

<div class="container-xl">
	<h2>Forms</h2>
	<p>Standard Bootstrap 5 form components, styled as per the theme.</p>

	<form class="form-card" action="#" method="post">
		<div class="mb-3">
			<label for="inputText" class="form-label">Text Input</label>
			<input type="text" class="form-control" id="inputText" placeholder="Enter text">
		</div>

		<div class="mb-3">
			<label for="inputEmail" class="form-label">Email Input</label>
			<input type="email" class="form-control" id="inputEmail" placeholder="email@example.com">
		</div>

		<div class="mb-3">
			<label for="selectExample" class="form-label">Select Dropdown</label>
			<select class="form-select" id="selectExample">
				<option selected>Select an option</option>
				<option value="1">Option 1</option>
				<option value="2">Option 2</option>
			</select>
		</div>

		<div class="mb-3">
			<label for="textareaExample" class="form-label">Textarea</label>
			<textarea class="form-control" id="textareaExample" rows="3"></textarea>
		</div>

		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="checkboxExample">
			<label class="form-check-label" for="checkboxExample">
				Check me out
			</label>
		</div>

		<div class="form-check">
			<input class="form-check-input" type="radio" name="radioGroup" id="radio1" checked>
			<label class="form-check-label" for="radio1">Radio 1</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="radioGroup" id="radio2">
			<label class="form-check-label" for="radio2">Radio 2</label>
		</div>

		<button type="submit" class="button mt-3">Submit</button>
	</form>
</div>
