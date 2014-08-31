<?php
	Fullscreen_gallery::get_header();

	$image_ids = Fullscreen_gallery::get_images();
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
				var $slides = $('#slides');
				<?php if ( Fullscreen_gallery::$config['mobile'] ) { ?>

				Hammer($slides[0]).on("swipeleft", function(e) {
					$slides.data('superslides').animate('next');
				});

				Hammer($slides[0]).on("swiperight", function(e) {
					$slides.data('superslides').animate('prev');
				});
				<?php } ?>

				$slides.superslides(
					<?php echo json_encode( Fullscreen_gallery::get_slider_args() ); ?>

				)
			});
		</script>

<?php
Fullscreen_gallery::get_footer();
