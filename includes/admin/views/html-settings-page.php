<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div class="updated woocommerce-message">
		<p><?php printf( __( 'Help us keep the %s plugin free making a rate %s on %s. Thank you in advance!', 'wc-granatum' ), '<strong>' . __( 'WooCommerce Granatum', 'wc-granatum' ) . '</strong>', '<a href="https://wordpress.org/support/view/plugin-reviews/wc-granatum?rate=5#postform" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/wc-granatum?rate=5#postform" target="_blank">' . __( 'WordPress.org', 'wc-granatum' ) . '</a>' ); ?></p>
	</div>

	<?php settings_errors(); ?>
	<form method="post" action="options.php">

		<?php
			settings_fields( 'wc_granatum_settings' );
			do_settings_sections( 'wc_granatum_settings' );

			submit_button();
		?>

	</form>

</div>
