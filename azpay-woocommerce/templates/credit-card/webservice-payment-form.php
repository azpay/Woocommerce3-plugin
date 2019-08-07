<?php
/**
 * Credit Card - Webservice checkout form.
 *
 * @version 4.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset id="azpay-credit-payment-form" class="azpay-payment-form">	
	<div class="clear"></div>	
	<p class="form-row form-row-first">
		<label for="azpay-card-number"><?php _e( 'Card Number', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-number" name="sixbank_credit_number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="22" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="azpay-card-holder-name"><?php _e( 'Name Printed on the Card', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-holder-name" name="sixbank_credit_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="azpay-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-expiry" name="sixbank_credit_expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'azpay-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="azpay-card-cvv"><?php _e( 'Security Code', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="azpay-card-cvv" name="sixbank_credit_cvv" class="input-text wc-credit-card-form-card-cvv" type="tel" maxlength="4" autocomplete="off" placeholder="<?php _e( 'CVV', 'azpay-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	
	<?php if ( ! empty( $installments ) && !$_is_sub ) : ?>
		<p class="form-row form-row-wide">
			<label for="azpay-installments"><?php _e( 'Installments', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
			<?php echo $installments; ?>
		</p>
	<?php endif; 
	if ($_is_sub): ?>
		
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>
