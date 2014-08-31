<?php
	Fullscreen_gallery::get_header();

	$post      = get_post();
	$images    = get_post_galleries( $post, false );
	$image_ids = wp_list_pluck( $images,'ids' );
	$image_ids = explode(",", $image_ids[0]);
?>

		<div id="slides">
			<div class="slides-container">

				<?php
				foreach ( $image_ids as $image_id ) {
					echo wp_get_attachment_image( $image_id, 'large' ) . PHP_EOL . PHP_EOL;
				}
				?>
			</div>
		</div>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#slides').superslides({
					animation: "slide",
					scrollable: false,
					hashchange: true
				})
			});
		</script>

<?php
Fullscreen_gallery::get_footer();
