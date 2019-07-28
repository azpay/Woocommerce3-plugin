<?php
/**
 * Credit Card - Icons checkout form.
 *
 * @version 4.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$first_method = current( $methods );

?>

<fieldset id="azpay-credit-payment-form" class="azpay-payment-form">
	<ul id="azpay-card-brand">
		<?php foreach ( $methods as $method_key => $method_name ): ?>
			<li><label title="<?php echo esc_attr( $method_name ); ?>"><i id="azpay-icon-<?php echo esc_attr( $method_key ); ?>"></i><input type="radio" name="azpay_credit_card" value="<?php echo esc_attr( $method_key ); ?>" <?php echo ( $first_method == $method_name ) ? 'checked="checked"' : ''; ?>/><span><?php echo esc_attr( $method_name ); ?></span></label></li>
		<?php endforeach ?>
	</ul>

	<div class="clear"></div>

	<?php if ( ! empty( $installments ) ) : ?>
		<p id="azpay-select-name"><?php _e( 'Pay with', 'azpay-woocommerce' ); ?> <strong><?php echo esc_attr( $first_method ); ?></strong></p>

		<div id="azpay-installments">
			<p class="form-row">
				<?php echo $installments; ?>
			</p>
		</div>
	<?php endif; ?>

	<div class="clear"></div>
</fieldset>
