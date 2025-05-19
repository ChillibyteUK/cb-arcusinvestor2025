<?php
/**
 * Blocks reference partial.
 *
 * Displays a preview and summary of all registered ACF blocks, including a cleaned-up
 * description extracted from each block template's file doc block.
 *
 * @package cb-arcusinvestor2025
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get all registered ACF block types.
$blocks = acf_get_block_types();

?>
<style>
	.blocks-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
		gap: 1.5rem;
		margin-top: 2rem;
	}
	.block-card {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		padding: 1rem;
		border: 1px solid #ccc;
		border-radius: 6px;
		background: #fff;
		font-family: monospace;
		/* Ensure cards stretch to equal height */
		height: 100%;
	}
	.block-preview {
		flex: 1;
		border: 1px dashed #ddd;
		padding: 1rem;
		margin-top: 1rem;
		max-height: 300px;
		overflow: hidden;
		font-family: initial;
	}
	.block-meta {
		margin-top: 1rem;
		font-size: 0.875rem;
	}
	.block-meta code {
		display: block;
	}
</style>

<div class="container-xl">
	<h2>Blocks</h2>
	<p>This section shows all registered ACF blocks with a preview of their default state and a summary of their expected fields and description.</p>

	<div class="blocks-grid">
		<?php
		if ( ! empty( $blocks ) ) {
			foreach ( $blocks as $block ) {
				// Start output buffering to capture the rendered preview.
				ob_start();
				$template = ! empty( $block['render_template'] ) ? locate_template( $block['render_template'] ) : '';
				if ( ! empty( $template ) && file_exists( $template ) ) {
					// For a more realistic preview, you could set default ACF field values here.
					include $template;
				} else {
					echo '<p>No template found.</p>';
				}
				$block_preview = ob_get_clean();

				// Retrieve the block description from the file doc block.
				$description = '';
				if ( ! empty( $template ) && file_exists( $template ) ) {
					$description = get_template_description( $template );
				}

				// Get the expected field names from ACF field groups.
				$field_groups  = acf_get_field_groups( array( 'block' => $block['name'] ) );
				$fields_output = '';
				if ( ! empty( $field_groups ) ) {
					foreach ( $field_groups as $group ) {
						$fields = acf_get_fields( $group['key'] );
						if ( $fields ) {
							foreach ( $fields as $field ) {
								$fields_output .= '<code>' . esc_html( $field['name'] ) . '</code> ';
							}
						}
					}
				}
				?>
				<div class="block-card">
					<h3><?php echo esc_html( $block['title'] ); ?> <small>(<?php echo esc_html( $block['name'] ); ?>)</small></h3>
					<?php
					if ( ! empty( $block['icon'] ) ) {
						echo '<p><strong>Icon:</strong> ' . esc_html( $block['icon'] ) . '</p>';
					} else {
						echo '<p>No icon defined.</p>';
					}
					if ( $description ) {
						echo '<p><strong>Description:</strong> ' . esc_html( $description ) . '</p>';
					} else {
						echo '<p>No description available.</p>';
					}
					?>
					<div class="block-preview">
						<?php echo wp_kses_post( $block_preview ); ?>
					</div>
					<div class="block-meta">
						<?php
						$clean_fields_output = trim( wp_strip_all_tags( $fields_output, false ) );
						if ( ! empty( $clean_fields_output ) ) {
							echo '<strong>Expected fields:</strong>';
							echo wp_kses_post( $fields_output );
						} else {
							echo '<p>No fields defined.</p>';
						}
						?>
					</div>
				</div>
				<?php
			}
		} else {
			echo '<p>No ACF blocks registered.</p>';
		}
		?>
	</div>
</div>
<?php
// End of blocks reference.
?>
