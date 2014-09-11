<?php
if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_Gallery_Filter_Shortcode {
	private static $original_callback = array();

	public static function add( $tag ) {
		global $shortcode_tags;

		if ( ! shortcode_exists( $tag ) ) {
			return false;
		}

		self::$original_callback[ $tag ] = $shortcode_tags[ $tag ];

		add_shortcode( $tag, array( __CLASS__, 'run_shortcode' ) );
	}

	public static function restore( $tag ) {
		if ( ! isset( self::$original_callback[ $tag ] ) ) {
			return false;
		}

		add_shortcode( $tag, self::$original_callback[ $tag ] );

		unset( self::$original_callback[ $tag ] );

		return true;
	}


	public static function run_shortcode( $attr ) {
		if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) ) {
			$trace = debug_backtrace( false );
		}
		else {
			$trace = debug_backtrace();
		}

		if ( isset( $trace[0]['args'][2] ) && $tag = $trace[0]['args'][2] ) {
			$output = call_user_func( self::$original_callback[ $tag ], $attr, null, $tag );
			$output = apply_filters( 'fullscreen_gallery_filter_shortcode_' . $tag , $output, $attr );

			return apply_filters( 'fullscreen_gallery_filter_shortcode', $output, $tag, $attr );
		}
	}

}