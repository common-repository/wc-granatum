<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WC_Granatum_Plugin
{
	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_wc-granatum/wc-granatum.php', array( &$this, 'plugin_action_links' ), 10, 5 );
	}

	/**
	 * Add the settings page.
	 */
	function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'wc-granatum.php' ) !== false ) {
			$new_links = array(
				'<a href="http://wordpress.org/support/plugin/wc-granatum/" target="_blank" title="'. __( 'Official Forum', 'wc-granatum' ) .'">' . __( 'Get Help', 'wc-granatum' ) . '</a>',
				'<a href="https://github.com/miguelsmuller/wc-granatum/" target="_blank" title="'. __( 'Official Repository', 'wc-granatum' ) .'">' . __( 'Get Involved', 'wc-granatum' ) . '</a>',
				'<a href="https://wordpress.org/support/view/plugin-reviews/wc-granatum?rate=5#postform" target="_blank" title="'. __( 'Rate WooCommerce Granatum', 'wc-granatum' ) .'">' . __( 'Rate WooCommerce Granatum', 'wc-granatum' ) . '</a>'
			);
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Add the settings page.
	*/
	function plugin_action_links( $actions ) {
		$new_actions = array(
			'<a href="' . admin_url( 'admin.php?page=wc-granatum' ) . '">'. __( 'Settings', 'wc-granatum' ) .'</a>',
		);
		return array_merge( $new_actions, $actions );
	}
}
new WC_Granatum_Plugin();
