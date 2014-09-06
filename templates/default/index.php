<?php
if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_gallery_Template_Superslides {

	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function template_include( $template ) {
		// include custom template
		return dirname( __FILE__ ) . '/template.php';
	}

	public function enqueue_scripts() {
		if ( Fullscreen_gallery::$config['mobile'] ) {
			wp_enqueue_script( 'hammer' );
		}

		wp_enqueue_style( 'superslides', plugins_url( 'assets/superslides.css', __FILE__ ), array(), '0.6.2' );
		wp_enqueue_script( 'superslides', plugins_url( 'assets/jquery.superslides.min.js', __FILE__ ), array( 'jquery' ), '0.6.2' );
	}

}

new Fullscreen_gallery_Template_Superslides;