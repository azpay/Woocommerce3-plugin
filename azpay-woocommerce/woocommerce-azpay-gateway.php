<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Update the main file.
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/woocommerce-azpay-gateway.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/woocommerce-azpay-gateway.php', '/azpay-woocommerce.php', $active_plugin );
	}
}

update_option( 'active_plugins', $active_plugins );
