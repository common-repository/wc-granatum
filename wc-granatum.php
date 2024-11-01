<?php
/*
 * Plugin Name: WooCommerce Granatum
 * Plugin URI: https://github.com/miguelsmuller/wc-granatum
 * Description: Add integration with Granatum Financeiro
 * Version: 0.1.5
 * Author: Devim - Agência Web
 * Author URI: http://www.devim.com.br/
 * License: GPLv3 License
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc-granatum
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WC_Granatum' ) ) :

/**
 * WooCommerce Granatum main class
 */
class WC_Granatum
{
	/**
	 * @var string
	 */
	const VERSION = '0.1.1';

	/**
	 * @var object
	 */
	private static $instance = null;

	/**
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( class_exists( 'WooCommerce' ) ) {
			$this->includes();
		} else {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wc-granatum', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Includes.
	 */
	private function includes() {
		// Basic to any other class use
		include_once 'includes/class-wc-granatum-api.php';
		include_once 'includes/class-wc-granatum-helpful.php';

		// Plugin Configuration
		include_once 'includes/admin/class-wc-granatum-plugin.php';
		include_once 'includes/admin/class-wc-granatum-settings.php';

		// Used to integrate
		include_once 'includes/class-wc-granatum-customer.php';
		include_once 'includes/class-wc-granatum-payments.php';
	}

	/**
	 * WooCommerce fallback notice.
	 */
	public function admin_notices() {
		$message = sprintf( __( '%s depends on %s to work!', 'wc-granatum' ), __( 'WooCommerce Granatum', 'wc-granatum' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', 'wc-granatum' ) . '</a>' );

		echo '<div class="error"><p>' . $message . '</p></div>';
	}
}
add_action( 'plugins_loaded', array( 'WC_Granatum', 'get_instance' ), 0 );

endif;
