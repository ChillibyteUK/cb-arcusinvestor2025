<?php
/**
 * Block template for Portal Dashboard.
 *
 * @package cb-arcusinvestor2025
 */

global $wpdb;

$allowed_folders = cb_get_user_rml_folder_ids();

?>
<div class="container py-5 my-5">
	<h2>Welcome, <?= esc_html( wp_get_current_user()->display_name ); ?></h2>

	<?php
	if ( ! empty( $allowed_folders ) ) {

		echo '<p>You have access to the following folders:</p>';
		echo '<ul class="nav nav-tabs" id="libraryTabs" role="tablist">';
		$first = true;

		foreach ( $allowed_folders as $folder_id ) {
			$name = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT name FROM {$wpdb->prefix}realmedialibrary WHERE id = %d",
					$folder_id
				)
			);
			if ( $name ) {
				$tab_id = 'folder-' . esc_attr( $folder_id );
				echo '<li class="nav-item" role="presentation">';
				echo '<button class="nav-link' . ( $first ? ' active' : '' ) . '" id="' . esc_attr( $tab_id ) . '-tab" data-bs-toggle="tab" data-bs-target="#' . esc_attr( $tab_id ) . '" type="button" role="tab" aria-controls="' . esc_attr( $tab_id ) . '" aria-selected="' . ( $first ? 'true' : 'false' ) . '">';
				echo esc_html( $name );
				echo '</button>';
				echo '</li>';
				$first = false;
			}
		}
		echo '</ul>';

		// Content per folder.
		echo '<div class="tab-content px-4 pb-4 bg-white" id="libraryTabsContent">';
		$first = true;

		foreach ( $allowed_folders as $folder_id ) {
			$name = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT name FROM {$wpdb->prefix}realmedialibrary WHERE id = %d",
					$folder_id
				)
			);

			$attachment_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT attachment FROM {$wpdb->prefix}realmedialibrary_posts WHERE fid = %d",
					$folder_id
				)
			);

			$tab_id = 'folder-' . esc_attr( $folder_id );
			echo '<div class="tab-pane fade' . ( $first ? ' show active' : '' ) . '" id="' . esc_attr( $tab_id ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $tab_id ) . '-tab">';
			echo '<h3 class="pt-4">' . esc_html( $name ) . '</h3>';

			// Filter + sort dropdowns.
			echo '<div class="d-flex justify-content-between align-items-end mt-4">
					<div>
						<label for="filter-' . esc_attr( $tab_id ) . '" class="form-label">Filter by file type</label>
						<select class="form-select form-select-sm file-filter" data-target="#' . esc_attr( $tab_id ) . '" id="filter-' . esc_attr( $tab_id ) . '">
							<option value="">All</option>
							<option value="PDF">PDF</option>
							<option value="XLSX">Excel</option>
							<option value="DOCX">Word</option>
							<option value="TXT">Text</option>
						</select>
					</div>
					<div>
						<label for="sort-' . esc_attr( $tab_id ) . '" class="form-label">Sort by</label>
						<select class="form-select form-select-sm file-sort" data-target="#' . esc_attr( $tab_id ) . '" id="sort-' . esc_attr( $tab_id ) . '">
							<option value="title-asc">File name A-Z</option>
							<option value="title-desc">File name Z-A</option>
							<option value="date-desc">Newest first</option>
							<option value="date-asc">Oldest first</option>
						</select>
					</div>
				</div>';

			if ( ! empty( $attachment_ids ) ) {
				$attachments = get_posts(
					array(
						'post_type'      => 'attachment',
						'post_status'    => 'inherit',
						'post__in'       => $attachment_ids,
						'posts_per_page' => -1,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				);

				if ( $attachments ) {
					echo '<ul class="list-group my-4 document-list" id="list-' . esc_attr( $tab_id ) . '">';
					foreach ( $attachments as $doc ) {
						$url         = wp_get_attachment_url( $doc->ID );
						$filename    = basename( get_attached_file( $doc->ID ) );
						$ext         = strtoupper( pathinfo( $filename, PATHINFO_EXTENSION ) );
						$size        = size_format( filesize( get_attached_file( $doc->ID ) ) );
						$upload_time = get_the_time( 'd M Y', $doc->ID );

						$icon       = 'fa-regular fa-file';
						$icon_class = 'text-secondary';
						if ( 'PDF' === $ext ) {
							$icon       = 'fa-regular fa-file-pdf';
							$icon_class = 'text-danger';
						} elseif ( in_array( $ext, array( 'XLS', 'XLSX', 'CSV' ), true ) ) {
							$icon       = 'fa-regular fa-file-excel';
							$icon_class = 'text-success';
						} elseif ( in_array( $ext, array( 'DOC', 'DOCX' ), true ) ) {
							$icon       = 'fa-regular fa-file-word';
							$icon_class = 'text-primary';
						}
						?>
						<li class="list-group-item d-flex justify-content-between align-items-center" 
							data-ext="<?= esc_attr( $ext ); ?>"
							data-title="<?= esc_attr( strtolower( $doc->post_title ) ); ?>"
							data-date="<?= esc_attr( get_post_time( 'U', false, $doc->ID ) ); ?>">
							<span>
								<i class="<?= esc_attr( $icon ); ?> me-2 <?= esc_attr( $icon_class ); ?>"></i>
								<strong><?= esc_html( $doc->post_title ); ?></strong>
								(<?= esc_html( $filename ); ?>)
							</span>
							<span class="text-muted small">
								<?= esc_html( $size ); ?>
								<?= esc_html( $upload_time ); ?>
								<a href="<?= esc_url( site_url( '/download/?file=' . $doc->ID . '&mode=view' ) ); ?>" class="btn btn-sm btn-outline-secondary ms-2" target="_blank" rel="noopener noreferrer">View</a>
								<a href="<?= esc_url( site_url( '/download/?file=' . $doc->ID . '&mode=download' ) ); ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank" rel="noopener noreferrer">Download</a>
							</span>
						</li>
						<?php
					}
					echo '</ul>';
					echo '<p class="no-results text-muted" style="display: none;">No matching documents.</p>';
				} else {
					echo '<p class="mt-4 text-muted">No documents found in this folder.</p>';
					echo '<p class="no-results text-muted" style="display: none;">No matching documents.</p>';
				}
			} else {
				echo '<p class="mt-4 text-muted">No files mapped to this folder.</p>';
			}

			echo '</div>'; // end tab.
			$first = false;
		}

		echo '</div>'; // end tab-content.
	} else {
		echo '<p class="mt-4 text-muted">No folders assigned or Real Media Library not available.</p>';
	}
	?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const filters = document.querySelectorAll('.file-filter');
	const sorters = document.querySelectorAll('.file-sort');

	filters.forEach(filter => {
		filter.addEventListener('change', function () {
			const list = document.querySelector(this.dataset.target + ' .document-list');
			const items = list.querySelectorAll('li');
			const filterVal = this.value.toUpperCase();

			let anyVisible = false;

			items.forEach(item => {
				if (!filterVal || item.dataset.ext === filterVal) {
					item.classList.remove('hidden');
					anyVisible = true;
				} else {
					item.classList.add('hidden');
				}
			});

			const noResults = document.querySelector(this.dataset.target + ' .no-results');
			if (noResults) {
				noResults.style.display = anyVisible ? 'none' : 'block';
			}
		});
	});

	sorters.forEach(sorter => {
		sorter.addEventListener('change', function () {
			const list = document.querySelector(this.dataset.target + ' .document-list');
			const items = Array.from(list.querySelectorAll('li')).filter(item => !item.classList.contains('hidden'));

			const [key, dir] = this.value.split('-');
			items.sort((a, b) => {
				const aVal = a.dataset[key];
				const bVal = b.dataset[key];
				if (key === 'date') {
					return dir === 'asc' ? aVal - bVal : bVal - aVal;
				}
				return dir === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
			});

			items.forEach(item => list.appendChild(item));
		});
	});
});
</script>

<style>
.hidden {
	display: none !important;
}
</style>
