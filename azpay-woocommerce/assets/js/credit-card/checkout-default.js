(function( $ ) {
	'use strict';

	$( function() {
		// Store the installment options.
		$.data( document.body, 'sixbank_credit_installments', $( '#azpay-credit-payment-form #azpay-installments' ).html() );

		/**
		 * Set the installment fields.
		 *
		 * @param {string} card
		 */
		function setInstallmentsFields( card ) {
			var installments = $( '#azpay-credit-payment-form #azpay-installments' );

			$( '#azpay-credit-payment-form #azpay-installments' ).empty();
			$( '#azpay-credit-payment-form #azpay-installments' ).prepend( $.data( document.body, 'sixbank_credit_installments' ) );

			if ( 'discover' === card ) {
				$( 'option', installments ).not( '.azpay-at-sight' ).remove();
			}
		}

		// Set on update the checkout fields.
		$( 'body' ).on( 'ajaxComplete', function() {
			$.data( document.body, 'sixbank_credit_installments', $( '#azpay-credit-payment-form #azpay-installments' ).html() );
			setInstallmentsFields( $( 'body #azpay-credit-payment-form #azpay-card-brand option' ).first().val() );
		});

		// Set on change the card brand.
		$( 'body' ).on( 'change', '#azpay-credit-payment-form #azpay-card-brand', function() {
			setInstallmentsFields( $( ':selected', $( this ) ).val() );
		});
	});

}( jQuery ));
