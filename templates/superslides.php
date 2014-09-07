<?php
if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_gallery_Template_Superslides extends Fullscreen_Gallery_Template {

	public function __construct() {
		parent::__construct( 'superslides', 'Superslides' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( Fullscreen_gallery::$config['mobile'] ) {
			wp_enqueue_script( 'hammer' );
		}

		wp_enqueue_style( 'superslides', plugins_url( 'superslides/superslides.css', __FILE__ ), array(), '0.6.2' );
		wp_enqueue_script( 'superslides', plugins_url( 'superslides/jquery.superslides' . $suffix . '.js', __FILE__ ), array( 'jquery', 'animate-enhanced' ), '0.6.2' );
	}

	protected function slider_html( $image_ids, $slider_args  ) {
		?>

		<div id="slides">
			<div class="slides-container">

				<?php
				foreach ( $image_ids as $image_id ) {
					echo wp_get_attachment_image( $image_id, Fullscreen_gallery::$config['image_size'] ) . PHP_EOL . PHP_EOL;
				}
				?>
			</div>

			<?php if ( Fullscreen_gallery::$config['arrows'] ) { ?>
			<nav class="slides-navigation">
				<a href="#" class="next">&gt;</a>
				<a href="#" class="prev">&lt;</a>
			</nav>
			<?php } ?>
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
					<?php echo json_encode( $slider_args ); ?>

				)
			});
		</script>

		<?php
	}

}

new Fullscreen_gallery_Template_Superslides;