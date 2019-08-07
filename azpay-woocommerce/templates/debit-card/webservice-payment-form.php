<?php
/**
 * Debit Card - Webservice checkout form.
 *
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset id="azpay-debit-payment-form" class="azpay-payment-form">
	<p class="form-row form-row-first">
		<label for="azpay-card-number"><?php _e( 'Card Number', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-number" name="azpay_debit_number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="22" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="azpay-card-holder-name"><?php _e( 'Name Printed on the Card', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-holder-name" name="azpay_debit_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="azpay-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-expiry" name="azpay_debit_expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'azpay-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="azpay-card-cvv"><?php _e( 'Security Code', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-cvv" name="azpay_debit_cvv" class="input-text wc-credit-card-form-card-cvv" type="tel" maxlength="4" autocomplete="off" placeholder="<?php _e( 'CVV', 'azpay-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>	
	<?php if ( 0 < $discount ) : ?>		
		<p class="form-row form-row-wide discount">
			<?php printf( __( 'Payment by debit have discount of %s. Order Total: %s.', 'azpay-woocommerce' ), $discount . '%', sanitize_text_field( wc_price( $discount_total ) ) ); ?>
		</p>
		<p style="display: none" class="discount-text"><?php printf( '%s', sanitize_text_field( wc_price( $discount_total ) ) ); ?><p>
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>
