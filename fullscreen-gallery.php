<?php
/**
 * Plugin Name: Fullscreen Gallery
 * Plugin URI: https://github.com/markoheijnen/Fullscreen-Gallery
 * Description: A simple plugin that adds a fullscreen endpoint to display the gallery images
 * Version: 0.1
 * Author: Felipe Sere & Marko Heijnen
 * Author URI: http://markoheijnen.com
 * License: MIT
 */

class Fullscreen_gallery {
	private static $config = array(
		'fullscreen' => true,
	);

	public function __construct() {
		add_action( 'init', array( $this, 'makeplugins_add_fullscreen_endpoint' ) );

		add_filter( 'template_redirect', array( $this, 'hide_admin_bar' ), -1 );
		add_filter( 'template_include', array( $this, 'makeplugins_fullscreen_template_redirect' ), 0 );
	}


	public function makeplugins_add_fullscreen_endpoint() {
		add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
	}


	public function hide_admin_bar() {
		global $wp_query;
 
		// if this is not a request for json or a singular object then bail
		if ( ! isset( $wp_query->query_vars['fullscreen'] ) || ! is_singular() ) {
			return $template;
		}

		add_filter( 'show_admin_bar', '__return_false' );
	}

	public function makeplugins_fullscreen_template_redirect( $template ) {
		global $wp_query;
 
		// if this is not a request for json or a singular object then bail
		if ( ! isset( $wp_query->query_vars['fullscreen'] ) || ! is_singular() ) {
			return $template;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'add_superslides' ) );

		//remove_all_filters('post_gallery');
		add_filter( 'post_gallery', array( $this, 'unset_gallery_filters' ), 0 );
 
		// include custom template
		return dirname( __FILE__ ) . '/templates/fullscreen.php';
	}


	public function add_superslides() {
		wp_enqueue_style( 'superslides', plugins_url( 'css/superslides.css', __FILE__ ), array(), '0.6.2' );
		wp_enqueue_script( 'superslides', plugins_url( 'js/jquery.superslides.min.js', __FILE__ ), array( 'jquery' ), '0.6.2' );
	}

	public function unset_gallery_filters( $gallery ) {
		if ( class_exists('Avid_Gallery') ) {
			$avid_gallery = Avid_Gallery::instance();
			remove_filter('post_gallery', array( $avid_gallery, 'hide_first_gallery_shortcode_instance' ) );
		}

		return $gallery;
	}


	public static function get_header() {
		if ( ! self::$config['fullscreen'] ) {
			get_header();
			return;
		}

		ob_start();
		get_header();
		$header = ob_get_contents();

		ob_end_clean();

		$elements = explode( '</head>', $header );

		$header  = $elements[0];
		$header .= '<style>html { border: 0px;}</style/>';
		$header .= '</head>';

		echo $header;
	}

	public static function get_footer() {
		if ( ! self::$config['fullscreen'] ) {
			get_footer();
			return;
		}

		add_filter( 'wp_footer', array( __CLASS__, 'add_explode_comment' ), 0 );

		ob_start();
		get_footer();
		$footer = ob_get_contents();

		ob_end_clean();

		$elements = explode('<!--fullscreen-gallery-->', $footer );

		echo $elements[1];
	}

	public static function add_explode_comment() {
		echo '<!--fullscreen-gallery-->';
	}

}

$_GLOBALS['fullscreen_gallery'] = new Fullscreen_gallery();