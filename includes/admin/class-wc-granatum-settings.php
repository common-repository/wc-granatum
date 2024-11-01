<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WC_Granatum_Settings
{
	/**
	 * @var string
	 */
	protected $plugins_option;


	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		$this->helpful = new WC_Granatum_Helpful;

		$this->plugins_options = get_option( 'wc_granatum_settings' );;

		add_action( 'admin_menu', array( $this, 'settings_menu' ), 59 );
		add_action( 'admin_init', array( $this, 'plugin_settings' ) );
	}

	/**
	 * Add the settings page.
	*/
	public function settings_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Granatum', 'wc-granatum' ),
			__( 'Granatum', 'wc-granatum' ),
			'manage_options',
			'wc-granatum',
			array( $this, 'html_settings_page' )
		);
	}

	/**
	 * Render the settings page for this plugin
	*/
	public function html_settings_page() {
		include_once 'views/html-settings-page.php';
	}

	/**
	 * Plugin settings form fields
	 */
	public function plugin_settings() {
		global $plugin_page;

		$option = 'wc_granatum_settings';

		// Set Custom Fields section.
		add_settings_section(
			'api_section',
			__( 'API Options:', 'wc-granatum' ),
			array( $this, 'section_options_callback' ),
			$option
		);

		// Granatum API Access Token
		add_settings_field(
			'access_token',
			__( 'Granatum API Access Token', 'wc-granatum' ),
			array( $this, 'input_element_callback' ),
			$option,
			'api_section',
			array(
				'id'              => 'access_token',
				'name'            => $option,
				'description'     => __( 'This token is available in Granatum in Settings -> My Company -> Sidebar (API).', 'wc-granatum' ),
				'class'           => 'regular-text',
				'element_disable' => '',
			)
		);

		// Checks if necessary to block the fields
		$element_disable = '';
		if ( ! $this->plugins_options['access_token'] ) {
			$element_disable = 'disabled';
		}

		// Set Custom Fields section.
		add_settings_section(
			'payments_section',
			__( 'Payments Options:', 'wc-granatum' ),
			array( $this, 'section_options_callback' ),
			$option
		);

		add_settings_field(
			'payment_prefix',
			__( 'Payment prefix', 'wc-granatum' ),
			array( $this, 'input_element_callback' ),
			$option,
			'payments_section',
			array(
				'id'              => 'payment_prefix',
				'name'            => $option,
				'class'           => 'regular-text',
				'element_disable' => $element_disable,
			)
		);

		add_settings_field(
			'bank_account',
			__( 'Bank account', 'wc-granatum' ),
			array( $this, 'select_element_callback' ),
			$option,
			'payments_section',
			array(
				'id'              => 'bank_account',
				'name'            => $option,
				'description'     => __( 'By requirement of Granatum API this field is required', 'wc-granatum' ),
				'options'         => $this->helpful->get_bank_accounts(),
				'required'        => TRUE,
				'element_disable' => $element_disable,
			)
		);

		add_settings_field(
			'category',
			__( 'Category', 'wc-granatum' ),
			array( $this, 'select_element_callback' ),
			$option,
			'payments_section',
			array(
				'id'              => 'category',
				'name'            => $option,
				'description'     => __( 'By requirement of Granatum API this field is required', 'wc-granatum' ),
				'options'         => $this->helpful->get_categorys(),
				'required'        => TRUE,
				'element_disable' => $element_disable,
			)
		);

		add_settings_field(
			'profit_center',
			__( 'Profit center', 'wc-granatum' ),
			array( $this, 'select_element_callback' ),
			$option,
			'payments_section',
			array(
				'id'              => 'profit_center',
				'name'            => $option,
				'options'         => $this->helpful->get_profit_centers(),
				'required'        => FALSE,
				'element_disable' => $element_disable
			)
		);

		add_settings_field(
			'payment_method',
			__( 'Payment method', 'wc-granatum' ),
			array( $this, 'select_element_callback' ),
			$option,
			'payments_section',
			array(
				'id'              => 'payment_method',
				'name'            => $option,
				'options'         => $this->helpful->get_payment_methods(),
				'required'        => FALSE,
				'element_disable' => $element_disable
			)
		);

		// Register settings.
		register_setting( $option, $option, array( $this, 'validate_options' ) );
	}

	/**
	 * Section null fallback
	 */
	public function section_options_callback() {

	}

	/**
	 * Select element fallback
	 *
	 * @return string Select field
	 */
	public function select_element_callback( $args ) {
		$name            = $args['name'];
		$id              = $args['id'];
		$element_disable = $args['element_disable'];
		$required        = $args['required'];

		$plugin_options  = get_option( $name );

		if ( isset( $plugin_options[ $id ] ) ) {
			$current = $plugin_options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : 0;
		}

		$html = '<select '. $element_disable .' id="' . $id . '" name="' . $name . '[' . $id . ']">';
			if ( !is_array($args['options'])) {
				$html .= sprintf( '<option>%s</option>', $args['options'] );
			}else{
				if (! $required) {
					$html .= sprintf( '<option value="%s" %s>%s</option>', '0', selected( $current, '0', false ), __( '> Not Set', 'wc-granatum' ) );
				}
				foreach ( $args['options'] as $key => $value ) {
					$html .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $current, $key, false ), $value );
				}
			}
		$html .= '</select>';

		if ( isset( $args['description'] ) ) {
			$html .= '<p class="description">' . $args['description'] . '</p>';
		}

		echo $html;
	}

	/**
	 * Input element fallback.
	 *
	 * @return string Input field
	 */
	public function input_element_callback( $args ) {
		$id              = $args['id'];
		$name            = $args['name'];
		$class           = $args['class'];
		$element_disable = $args['element_disable'];

		$plugin_options = get_option( $name );

		if ( isset( $plugin_options[ $id ] ) ) {
			$current = $plugin_options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '<input '. $element_disable .' type="text" class="'. $class .'" id="' . $id . '" name="' . $name . '[' . $id . ']" value="'. $current .'"/>';

		if ( isset( $args['description'] ) ) {
			$html .= '<p class="description">' . $args['description'] . '</p>';
		}

		echo $html;
	}

	/**
	 * Checkbox element fallback.
	 *
	 * @return string Checkbox field
	 */
	public function checkbox_element_callback( $args ) {
		$name    = $args['name'];
		$id      = $args['id'];
		$plugin_options = get_option( $name );

		if ( isset( $plugin_options[ $id ] ) ) {
			$current = $plugin_options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '0';
		}

		$html = '<input type="checkbox" id="' . $id . '" name="' . $name . '[' . $id . ']" value="1"' . checked( 1, $current, false ) . '/>';

		if ( isset( $args['label'] ) ) {
			$html .= ' <label for="' . $id . '">' . $args['label'] . '</label>';
		}

		if ( isset( $args['description'] ) ) {
			$html .= '<p class="description">' . $args['description'] . '</p>';
		}

		echo $html;
	}

	/**
	 * Valid options
	 *
	 * @param  array $input options to valid
	 *
	 * @return array validated options
	 */
	public function validate_options( $input ) {
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {
				$output[ $key ] = woocommerce_clean( $input[ $key ] );
			}
		}

		return $output;
	}
}
new WC_Granatum_Settings();
