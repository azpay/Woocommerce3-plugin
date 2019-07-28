<?php
/**
 * Admin View: Notice - Currency not supported.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error inline">
	<p><strong><?php _e( 'Azpay WooCommerce Disabled', 'azpay-woocommerce' ); ?></strong>: <?php printf( __( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'azpay-woocommerce' ), get_woocommerce_currency() ); ?>
	</p>
</div>
