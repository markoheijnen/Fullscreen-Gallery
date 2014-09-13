<?php
/**
 * Plugin Name: Fullscreen Gallery
 * Plugin URI: https://github.com/markoheijnen/Fullscreen-Gallery
 * Description: A simple plugin that adds a fullscreen endpoint to display the gallery images
 * Version: 0.2
 * Author: Marko Heijnen & Felipe Sere
 * Author URI: http://markoheijnen.com
 * License: GPL
 */

if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

include 'inc/filter-shortcode.php';
include 'inc/template-interface.php';

class Fullscreen_Gallery {
	const version = '0.2';

	public static $config = array(
		'fullscreen'  => true,
		'mobile'      => true,
		'arrows'      => false,
		'back_button' => true,
		'template'    => 'superslides',
		'image_size'  => 'large',
	);

	public static $templates = array();

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
		Fullscreen_Gallery_Filter_Shortcode::add('gallery');
		add_filter( 'fullscreen_gallery_filter_shortcode_gallery', array( $this, 'add_link_to_gallery_shortcode' ) );

		// if this is not a request for fullscreen
		if ( ! isset( $GLOBALS['wp_query']->query_vars['fullscreen'] ) || ! is_singular() ) {
			return;
		}

		if ( self::$config['fullscreen'] ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}

		self::$config = apply_filters( 'fullscreen_gallery_args', self::get_config(), get_the_ID() );

		add_filter( 'template_include', array( $this, 'template_include' ) );
	}

	public function add_link_to_gallery_shortcode( $output ) {
		if ( is_singular() && $output ) {
			$output .= '<a href="' . get_permalink() . '/fullscreen/">' . __( 'Go Fullscreen', 'fullscreen-gallery' ) . '<a/>';
		}

		return $output;
	}

	public function template_include( $template ) {
		// include custom template
		return dirname( __FILE__ ) . '/inc/template.php';
	}

	public function register_scripts() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'animate-enhanced', plugins_url( 'js/jquery.animate-enhanced' . $suffix . '.js', __FILE__ ), array('jquery'), '1.11' );
		wp_register_script( 'hammer', plugins_url( 'js/hammer' . $suffix . '.js', __FILE__ ), array(), '2.0.2' );
	}




	public static function get_templates() {
		$templates = glob( dirname( __FILE__ ) . '/templates/*.php' );

		return array_map(
			'basename',
			$templates,
			array_fill( 0 , count( $templates ) , '.php' )
		);
	}

	public static function get_template( $template = false ) {
		if ( ! $template ) {
			$template = self::$config['template'];
		}

		if ( 'default' == $template ) {
			$template = 'superslides';
		}

		if ( ! isset( self::$templates[ $template ] ) ) {
			$folder = dirname( __FILE__ ) . '/templates/';

			if ( is_file( $folder . $template . '.php' ) ) {
				include $folder . $template . '.php';
			}
		}

		if ( ! isset( self::$templates[ $template ] ) ) {
			self::$templates[ $template ] = false;
		}

		return self::$templates[ $template ];
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
			'play'       => false, // Miliseconds
		);

		return apply_filters( 'fullscreen_gallery_slider_args', $slider_args, get_the_ID() );
	}

}

$GLOBALS['fullscreen_gallery'] = new Fullscreen_Gallery();