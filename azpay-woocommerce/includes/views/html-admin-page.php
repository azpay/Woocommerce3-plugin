<?php
/**
 * Admin options screen.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h3><?php echo $this->method_title; ?></h3>

<?php
if ( 'yes' == $this->get_option( 'enabled' ) ) {
	if ( ! 'BRL' == get_woocommerce_currency() && ! class_exists( 'woocommerce_wpml' ) ) {
		include dirname( __FILE__ ) . '/notices/html-notice-currency-not-supported.php';
	}
	
	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
		include dirname( __FILE__ ) . '/notices/html-notice-need-update-woocommerce.php';
	}
	
}
?>

<?php echo wpautop( $this->method_description ); ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
