<?php
/**
 * Debit Card - Default checkout form.
 *
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset id="azpay-debit-payment-form" class="azpay-payment-form">
	<p class="form-row form-row-wide">
		<?php if ( 1 < count( $methods ) ) : ?>
			<label for="azpay-card-brand"><?php _e( 'Debit Card', 'azpay-woocommerce' ); ?> <span class="required">*</span></label>
			<select id="azpay-card-brand" name="azpay_debit_card" style="font-size: 1.5em; padding: 4px; width: 100%;">
				<?php foreach ( $methods as $method_key => $method_name ) : ?>
					<option value="<?php echo esc_attr( $method_key ); ?>"><?php echo esc_attr( $method_name ); ?></option>
				<?php endforeach ?>
			</select>
		<?php else : ?>
			<span><?php printf( __( 'Pay with %s.', 'azpay-woocommerce' ), current( $methods ) ); ?></span>
			<input type="hidden" name="azpay_debit_card" value="<?php echo esc_attr( key( $methods ) ); ?>" />
		<?php endif; ?>
	</p>
	<?php if ( 0 < $discount ) : ?>
		<p class="form-row form-row-wide">
			<?php printf( __( 'Payment by debit have discount of %s. Order Total: %s.', 'azpay-woocommerce' ), $discount . '%', sanitize_text_field( wc_price( $discount_total ) ) ); ?>
		</p>
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>
