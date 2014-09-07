<?php
/**
 * Plugin Name: Fullscreen Gallery
 * Plugin URI: https://github.com/markoheijnen/Fullscreen-Gallery
 * Description: A simple plugin that adds a fullscreen endpoint to display the gallery images
 * Version: 0.2-dev
 * Author: Marko Heijnen & Felipe Sere
 * Author URI: http://markoheijnen.com
 * License: GPL
 */

if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_Gallery {
	const version = '0.2-dev';

	public static $config = array(
		'fullscreen'  => true,
		'mobile'      => true,
		'back_button' => true,
		'template'    => 'default',
		'image_size'  => 'large',
	);

	public function __construct() {
		add_action( 'init', array( $this, 'add_fullscreen_endpoint' ) );
		add_action( 'admin_menu', array( $this, 'load_admin' ), 1 );

		add_filter( 'template_redirect', array( $this, 'load' ), -1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}


	public function add_fullscreen_endpoint() {
		add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
	}

	/**
	 * Load the admin class
	 *
	 * @since 0.2.0
	 */
	public function load_admin() {
		include 'inc/admin.php';
		new Fullscreen_Gallery_Admin;
	}


	public function load() {
		// if this is not a request for fullscreen
		if ( ! isset( $GLOBALS['wp_query']->query_vars['fullscreen'] ) || ! is_singular() ) {
			return;
		}

		self::$config = apply_filters( 'fullscreen_gallery_args', self::get_config(), get_the_ID() );

		if ( self::$config['fullscreen'] ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}

		$folder = dirname( __FILE__ ) . '/templates/';

		if ( is_file( $folder . self::$config['template'] . '/index.php' ) ) {
			include $folder . self::$config['template'] . '/index.php';
		}
		else {
			include $folder . 'default/index.php';
		}
	}

	public function register_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'animate-enhanced', plugins_url( 'js/jquery.animate-enhanced' . $suffix . '.js', __FILE__ ), array('jquery'), '1.11' );
		wp_register_script( 'hammer', plugins_url( 'js/hammer' . $suffix . '.js', __FILE__ ), array(), '2.0.2' );
	}




	public static function get_templates() {
		return array_map( 'basename', glob( dirname( __FILE__ ) . '/templates/*', GLOB_ONLYDIR ) );
	}

	public static function get_config() {
		$config = get_option( 'fullscreen_gallery', self::$config );
		$config = array_merge( self::$config, $config );

		return $config;
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
			'hashchange' => true,
			'pagination' => true,
			'arrows'     => false,
			'play'       => false, // Miliseconds
		);

		return apply_filters( 'fullscreen_gallery_slider_args', $slider_args, get_the_ID() );
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

		if ( self::$config['back_button'] && self::$config['fullscreen']  ) {
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

}

$GLOBALS['fullscreen_gallery'] = new Fullscreen_Gallery();