<?php
/**
 * Simple custom product
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
do_action( 'sixbank_subscription_before_add_to_cart_form' );  ?>

<form class="sixbank_subscription_cart" method="post" enctype='multipart/form-data'>	
	<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
</form>

<?php do_action( 'sixbank_subscription_after_add_to_cart_form' ); ?>