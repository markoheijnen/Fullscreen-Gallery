<?php
/**
 * Plugin Name: Fullscreen Gallery
 * Plugin URI: https://github.com/markoheijnen/Fullscreen-Gallery
 * Description: A simple plugin that adds a fullscreen endpoint to display the gallery images
 * Version: 0.1
 * Author: Marko Heijnen & Felipe Sere
 * Author URI: http://markoheijnen.com
 * License: GPL
 */

if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_gallery {
	public static $config = array(
		'fullscreen'  => true,
		'mobile'      => true,
		'back_button' => true,
		'template'    => 'default',
	);

	public function __construct() {
		add_action( 'init', array( $this, 'add_fullscreen_endpoint' ) );

		add_filter( 'template_redirect', array( $this, 'add_hooks' ), -1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}


	public function add_fullscreen_endpoint() {
		add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
	}


	public function add_hooks() {
		// if this is not a request for fullscreen
		if ( ! isset( $GLOBALS['wp_query']->query_vars['fullscreen'] ) || ! is_singular() ) {
			return;
		}

		add_filter( 'show_admin_bar', '__return_false' );

		$folder = dirname( __FILE__ ) . '/templates/';

		if ( is_file( $folder . self::$config['template'] . '/index.php' ) ) {
			include $folder . self::$config['template'] . '/index.php';
		}
		else {
			include $folder . 'default/index.php';
		}
	}

	public function register_scripts() {
		wp_register_script( 'hammer', plugins_url( 'js/hammer.min.js', __FILE__ ), array(), '2.0.2' );
	}


	public static function get_header() {
		if ( ! self::$config['fullscreen'] ) {
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

		if ( self::$config['back_button'] ) {
			echo '<a href="' . get_permalink() . '" class="fullscreen-button">' . __( 'Back', 'fullscreen-gallery' ) . '</a>';
		}
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


	public static function get_images() {
		$post      = get_post();
		$images    = get_post_galleries( $post, false );
		$image_ids = wp_list_pluck( $images, 'ids' );

		return explode( ',', $image_ids[0] );
	}

	public static function get_slider_args() {
		$slider_args = array(
			'animation'  => 'slide',
			'scrollable' => false,
			'hashchange' => true
		);

		return apply_filters( 'fullscreen_gallery_slider_args', $slider_args );
	}

}

$GLOBALS['fullscreen_gallery'] = new Fullscreen_gallery();