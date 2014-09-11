<?php
if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

abstract class Fullscreen_Gallery_Template {
	private $key;
	private $title;


	protected abstract function slider_html( $image_ids, $slider_args );


	protected function __construct( $key, $title ) {
		$this->title = $title;
		Fullscreen_Gallery::$templates[ $key ] = $this;
	}


	public function show_slider() {
		$image_ids   = Fullscreen_gallery::get_images();
		$slider_args = Fullscreen_gallery::get_slider_args();

		$this->get_header();

		$this->slider_html( $image_ids, $slider_args );

		$this->get_footer();
	}


	private function get_header() {
		if ( ! Fullscreen_Gallery::$config['fullscreen'] ) {
			get_header();
		}
		else {
			ob_start();
			get_header();
			$header = ob_get_contents();

			ob_end_clean();

			$elements = explode( '</head>', $header );

			echo $elements[0] . PHP_EOL . '</head>' . PHP_EOL;

			echo '<body class="' . join( ' ', get_body_class() ) . '">' . PHP_EOL;
		}

		if ( Fullscreen_Gallery::$config['back_button'] && Fullscreen_Gallery::$config['fullscreen']  ) {
			echo '<a href="' . get_permalink() . '" class="fullscreen-button">' . __( 'Back', 'fullscreen-gallery' ) . '</a>';
		}
	}

	private function get_footer() {
		if ( ! Fullscreen_Gallery::$config['fullscreen'] ) {
			get_footer();
			return;
		}

		add_filter( 'wp_footer', array( $this, 'add_explode_comment' ), 0 );

		ob_start();
		get_footer();
		$footer = ob_get_contents();

		ob_end_clean();

		$elements = explode('<!--fullscreen-gallery-->', $footer );

		echo $elements[1];
	}

	public function add_explode_comment() {
		echo '<!--fullscreen-gallery-->';
	}
	
}