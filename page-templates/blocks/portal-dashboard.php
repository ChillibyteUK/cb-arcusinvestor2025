<?php
/**
 * Block template for Portal Dashboard.
 *
 * @package cb-arcusinvestor2025
 */

global $wpdb;

// Fetch only parent folders from the allowed folders.
$allowed_folders = cb_get_user_rml_folder_ids();

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
$parent_folders = $wpdb->get_results(
	"SELECT id, name
	FROM {$wpdb->prefix}realmedialibrary
	WHERE id IN (" . implode( ',', array_map( 'intval', $allowed_folders ) ) . ')
	AND parent = -1'
);

?>
<div class="container py-5 my-5">
    <h2>Welcome, <?= esc_html( wp_get_current_user()->display_name ); ?></h2>

    <?php
    if ( ! empty( $parent_folders ) ) {

        echo '<p>You have access to the following folders:</p>';
        echo '<ul class="nav nav-tabs" id="libraryTabs" role="tablist">';
        $first = true;

        // Render tabs for parent folders.
        foreach ( $parent_folders as $folder ) {
            $tab_id = 'folder-' . esc_attr( $folder->id );
            echo '<li class="nav-item" role="presentation">';
            echo '<button class="nav-link' . ( $first ? ' active' : '' ) . '" id="' . esc_attr( $tab_id ) . '-tab" data-bs-toggle="tab" data-bs-target="#' . esc_attr( $tab_id ) . '" type="button" role="tab" aria-controls="' . esc_attr( $tab_id ) . '" aria-selected="' . ( $first ? 'true' : 'false' ) . '">';
            echo esc_html( $folder->name );
            echo '</button>';
            echo '</li>';
            $first = false;
        }
        echo '</ul>';

        // Render tab content for each parent folder.
        echo '<div class="tab-content px-4 pb-4 bg-white" id="libraryTabsContent">';
        $first = true;

        foreach ( $parent_folders as $folder ) {
            $tab_id = 'folder-' . esc_attr( $folder->id );
            echo '<div class="tab-pane fade' . ( $first ? ' show active' : '' ) . '" id="' . esc_attr( $tab_id ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $tab_id ) . '-tab">';
            echo '<h3 class="pt-4">' . esc_html( $folder->name ) . '</h3>';

            // Fetch files in the parent folder.
            $attachment_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT attachment FROM {$wpdb->prefix}realmedialibrary_posts WHERE fid = %d",
                    $folder->id
                )
            );

			// Render files in the parent folder.
			if ( ! empty( $attachment_ids ) ) {
				// Filter + sort dropdowns.
				?>
				<div class="d-flex justify-content-between align-items-end mt-4 mb-2">
					<?php
					/*
					<div>
						<label for="filter-<?= esc_attr( $tab_id ); ?>" class="form-label">Filter by file type</label>
						<select class="form-select form-select-sm file-filter" data-target="#list-<?= esc_attr( $tab_id ); ?>" id="filter-<?= esc_attr( $tab_id ); ?>">
							<option value="">All</option>
							<option value="PDF">PDF</option>
							<option value="XLSX">Excel</option>
							<option value="DOCX">Word</option>
							<option value="TXT">Text</option>
						</select>
					</div>
					*/
					?>
					<div>
						<label for="sort-<?= esc_attr( $tab_id ); ?>" class="form-label">Sort by</label>
						<select class="form-select form-select-sm file-sort" data-target="#list-<?= esc_attr( $tab_id ); ?>" id="sort-<?= esc_attr( $tab_id ); ?>">
							<option value="title-asc">File name A-Z</option>
							<option value="title-desc">File name Z-A</option>
							<option value="date-desc">Newest first</option>
							<option value="date-asc">Oldest first</option>
						</select>
					</div>
				</div>
				<ul class="list-group mb-4 document-list" id="list-<?= esc_attr( $tab_id ); ?>">
					<?php
					cb_render_files_list( $attachment_ids, '' );
					?>
				</ul>
				<p class="no-results text-muted" style="display: none;">No matching documents.</p>
				<?php
			} else {
				echo '<p class="mt-4 text-muted" id="stat_' . esc_attr( $folder->id ) . '">No files in this folder.</p>';
				echo '<p class="no-results text-muted" style="display: none;">No matching documents.</p>';
			}

            // Fetch and render sub-folder files.
            $sub_folders = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT id, name FROM {$wpdb->prefix}realmedialibrary WHERE parent = %d",
                    $folder->id
                )
            );

            if ( ! empty( $sub_folders ) ) {
				?>
			<style>
				#stat_<?= esc_attr( $folder->id ); ?> {
					display: none;
				}
			</style>
				<?php

                foreach ( $sub_folders as $sub_folder ) {

					// check if the user has access to this sub-folder.
					if ( ! in_array( (int) $sub_folder->id, $allowed_folders, true ) ) {
						continue;
					}

                    $sub_folder_attachments = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT attachment FROM {$wpdb->prefix}realmedialibrary_posts WHERE fid = %d",
                            $sub_folder->id
                        )
                    );

					// Render files in the sub-folder.
					if ( ! empty( $sub_folder_attachments ) ) {
						?>
						<h4 class="mt-5 fs-550 d-flex align-items-center collapsed" role="button" data-bs-toggle="collapse" href="#sf_<?= esc_attr( $sub_folder->id ); ?>">
							<i class="far fa-folder me-2"></i>
							<?= esc_html( $sub_folder->name ); ?>
							<span class="ms-auto collapse-icon has-red-400-color" id="icon-sf_<?= esc_attr( $sub_folder->id ); ?>"><i class="fas fa-angle-up"></i></span>
						</h4>
						<div class="collapse" id="sf_<?= esc_attr( $sub_folder->id ); ?>">
							<div class="d-flex justify-content-between align-items-end mb-3">
								<?php
								/*
								<div>
									<label for="filter-subfolder-<?= esc_attr( $sub_folder->id ); ?>" class="form-label">Filter by file type</label>
									<select class="form-select form-select-sm file-filter" data-target="#list-subfolder-<?= esc_attr( $sub_folder->id ); ?>" id="filter-subfolder-<?= esc_attr( $sub_folder->id ); ?>">
										<option value="">All</option>
										<option value="PDF">PDF</option>
										<option value="XLSX">Excel</option>
										<option value="DOCX">Word</option>
										<option value="TXT">Text</option>
									</select>
								</div>
								*/
								?>
								<div>
									<label for="sort-subfolder-<?= esc_attr( $sub_folder->id ); ?>" class="form-label">Sort by</label>
									<select class="form-select form-select-sm file-sort" data-target="#list-subfolder-<?= esc_attr( $sub_folder->id ); ?>" id="sort-subfolder-<?= esc_attr( $sub_folder->id ); ?>">
										<option value="title-asc">File name A-Z</option>
										<option value="title-desc">File name Z-A</option>
										<option value="date-desc">Newest first</option>
										<option value="date-asc">Oldest first</option>
									</select>
								</div>
							</div>
							<ul class="list-group document-list" id="list-subfolder-<?= esc_attr( $sub_folder->id ); ?>">
								<?php
								cb_render_files_list( $sub_folder_attachments, '' );
								?>
							</ul>
							<p class="no-results text-muted" style="display: none;">No matching documents.</p>
						</div>
						<hr class="mt-4">
						<?php
					} else {
						echo '<p class="no-results text-muted" style="display: none;">No matching documents.</p>';
					}
                }
            }

            echo '</div>'; // End tab-pane.
            $first = false;
        }

        echo '</div>'; // End tab-content.
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
            const list = document.querySelector(this.dataset.target);
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

            // Toggle the "no-results" message based on visibility.
            const noResults = list.nextElementSibling; // Assuming <p class="no-results"> is directly after the list.
            if (noResults && noResults.classList.contains('no-results')) {
                noResults.style.display = anyVisible ? 'none' : 'block';
            }
        });
    });

    sorters.forEach(sorter => {
        sorter.addEventListener('change', function () {
            const list = document.querySelector(this.dataset.target);
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

	const collapsibleHeaders = document.querySelectorAll('[data-bs-toggle="collapse"]');

	collapsibleHeaders.forEach(header => {
		const targetId = header.getAttribute('href').substring(1);
		const icon = document.querySelector(`#icon-${targetId} i`);

		// Update icon on collapse show
		document.getElementById(targetId).addEventListener('show.bs.collapse', function () {
			if (icon) {
				icon.style.transform = 'rotate(0deg)';
			}
		});

		// Update icon on collapse hide
		document.getElementById(targetId).addEventListener('hide.bs.collapse', function () {
			console.log('Collapse is collapsing:', targetId);
			if (icon) {
				icon.style.transform = 'rotate(180deg)';
			}
		});
	});
});
</script>

<style>
.hidden {
	display: none !important;
}
.collapse-icon i {
	transition: transform 0.3s ease;
}
h4 i {
	font-size: 1.5rem;;
}
</style>
