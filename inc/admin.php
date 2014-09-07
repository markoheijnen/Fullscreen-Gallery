<?php
if ( ! defined( 'ABSPATH' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

class Fullscreen_Gallery_Admin {

	/**
	 * Load hooks
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_style' ), 1 );
	}

	/**
	 * Adds a option page to manage all the tabs
	 *
	 * @since 0.2.0
	 */
	public function admin_menu() {
		add_options_page( __( 'Fullscreen Gallery', 'fullscreen-gallery' ), __( 'Fullscreen Gallery', 'fullscreen-gallery' ), 'manage_options', 'fullscreen-gallery', array( $this, 'settings_page' ) );
	}

	/**
	 * Register styles
	 *
	 * @since 0.2.0
	 */
	public function register_style() {
		wp_register_style( 'fullscreen_gallery', plugins_url( 'css/admin.css', dirname( __FILE__ ) ), array(), Fullscreen_Gallery::version );
	}

	/**
	 * Settings page
	 *
	 * @since 0.2.0
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.', 'fullscreen-gallery' ) );
		}

		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';


		$main_config = array(
			'fullscreen'  => array( 'title' => __( 'Fullscreen', 'fullscreen-gallery' ), 'type' => 'switch_on_off' ),
			'mobile'      => array( 'title' => __( 'Mobile support', 'fullscreen-gallery' ), 'type' => 'switch_on_off' ),
			'arrows'      => array( 'title' => __( 'Show arrows', 'fullscreen-gallery' ), 'type' => 'switch_on_off' ),
			'back_button' => array( 'title' => __( 'Back button', 'fullscreen-gallery' ), 'type' => 'switch_on_off' ),
			'template'    => array( 'title' => __( 'Template', 'fullscreen-gallery' ), 'type' => 'select', 'options' => Fullscreen_Gallery::get_templates() ),
			'image_size'  => array( 'title' => __( 'Image size', 'fullscreen-gallery' ), 'type' => 'select', 'options' => get_intermediate_image_sizes() ),
		);


		if ( isset( $_POST['fullscreen-gallery'] ) ) {
			$options = array_map( 'sanitize_text_field', $_POST['fullscreen-gallery'] );

			foreach ( $main_config as $name => $option ) {
				if ( 'switch_on_off' == $option['type'] ) {
					if ( isset( $options[ $name ] ) ) {
						 $options[ $name ] = true;
					}
					else {
						$options[ $name ] = false;
					}
				}
			}

			update_option( 'fullscreen_gallery', $options );

			echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>' . __( 'Settings saved.', 'fullscreen-gallery' ) . '</strong></p></div>';
		}

		$options = Fullscreen_Gallery::get_config();

		echo '<form method="post">';

		echo '<table class="form-table"><tbody>';

		foreach ( $main_config as $name => $option ) {
			if ( method_exists( $this, $option['type'] ) ) {
				$option['value'] = $options[ $name ];

				echo '<tr>';
				echo '<th scope="row">' . $option['title'] . '</th><td>';

				call_user_func( array( $this, $option['type'] ), $name, $option );

				echo '</td></tr>';
			}
		}

		echo '</tbody></table>';

		submit_button();

		echo '</form>';

		wp_enqueue_style('fullscreen_gallery');
		$this->set_labels_and_colors();
	}


	private function select( $option, $args ) {
		if ( ! isset( $args['name'] ) ) {
			$args['name'] = 'fullscreen-gallery';
		}

		if ( ! isset( $args['value'] ) ) {
			$args['value'] = '';
		}

		echo '<select name="' . $args['name'] . '[' . $option . ']">';

		foreach ( $args['options'] as $option ) {
			if ( is_array( $option ) ) {
				echo '<option></option>';
			}
			else {
				echo '<option value="' . $option . '"' . selected( $option, $args['value'], false ) . '>' . ucfirst( $option ) . '</option>';
			}
		}

		echo '</select>';
	}

	private function switch_on_off( $option, $args ) {
		if ( ! isset( $args['value'] ) || $args['value'] ) {
			$args['value'] = 'on';
		}

		if ( ! isset( $args['name'] ) ) {
			$args['name'] = 'fullscreen-gallery';
		}

		?>

		<div class="onoffswitch">
			<input type="checkbox" name="<?php echo $args['name']; ?>[<?php echo $option; ?>]" value="on" class="onoffswitch-checkbox" id="switch-<?php echo $option; ?>" <?php checked( 'on', $args['value'] ); ?>>

			<label class="onoffswitch-label" for="switch-<?php echo $option; ?>">
				<div class="onoffswitch-inner"></div>
				<div class="onoffswitch-switch"></div>
			</label>
		</div>

		<?php

		if ( isset( $args['description'] ) ) {
			echo '<p class="description">' . $args['description'] . '</p>';
		}
	}


	private function set_labels_and_colors() {
		global $_wp_admin_css_colors;

		$color = get_user_option('admin_color');

		if ( empty( $color ) || ! isset($_wp_admin_css_colors[ $color ] ) ) {
			$color = 'fresh';
		}

		echo '<style>';
		echo '.onoffswitch-inner:before { content: "' . __( 'On', 'fullscreen-gallery' ) . '"; }';
		echo '.onoffswitch-inner:after { content: "' . __( 'Off', 'fullscreen-gallery' ) . '"; }';
		echo '.form-table .onoffswitch-label { border-color: ' . $_wp_admin_css_colors[$color]->colors[2] . '; }';
		echo '.form-table .onoffswitch-inner:before { background-color: ' . $_wp_admin_css_colors[$color]->colors[3] . '; }';
		echo '.form-table .onoffswitch-switch { background-color: ' . $_wp_admin_css_colors[$color]->colors[2] . '; }';
		echo '</style>';
	}

}