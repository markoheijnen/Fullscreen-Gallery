<?php
/**
 * Plugin Name: Fullscreen Gallery
 * Plugin URI: 
 * Description: 
 * Version: 0.1
 * Author: Felipe Sere & Marko Heijnen
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: MIT
 */

class Fullscreen_gallery {

	public function __construct() {
		add_action( 'init', array( $this, 'makeplugins_add_fullscreen_endpoint' ) );
		add_filter( 'template_include', array( $this, 'makeplugins_fullscreen_template_redirect' ) );
	}


	public function makeplugins_add_fullscreen_endpoint() {
		add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
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

}

new Fullscreen_gallery();