<?php
/**
 * Slip - Webservice checkout form.
 *
 * @version 4.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( 0 < $discount ) : ?>		
	<p class="form-row form-row-wide discount">
		<?php printf( __( 'Payment by slip have discount of %s. Order Total: %s.', 'azpay-woocommerce' ), $discount . '%', sanitize_text_field( woocommerce_price( $discount_total ) ) ); ?>
	</p>
	<p style="display: none" class="discount-text"><?php printf( '%s', sanitize_text_field( woocommerce_price( $discount_total ) ) ); ?><p>
<?php endif; ?>