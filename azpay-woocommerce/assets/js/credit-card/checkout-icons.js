(function( $ ) {
	'use strict';

	$( function() {
		// Store the installment options.
		$.data( document.body, 'azpay_credit_installments', $( '#azpay-credit-payment-form #azpay-installments' ).html() );

		/**
		 * Set the installment fields.
		 *
		 * @param {string} card
		 */
		function setInstallmentsFields( card ) {
			var installments = $( '#azpay-credit-payment-form #azpay-installments' );

			$( '#azpay-credit-payment-form #azpay-installments' ).empty();
			$( '#azpay-credit-payment-form #azpay-installments' ).prepend( $.data( document.body, 'azpay_credit_installments' ) );

			if ( 'discover' === card ) {
				$( 'label', installments ).not( '.azpay-at-sight' ).remove();
			}

			$( 'input:eq(0)', installments ).attr( 'checked', 'checked' );
		}

		// Set on update the checkout fields.
		$( 'body' ).on( 'ajaxComplete', function() {
			$.data( document.body, 'azpay_credit_installments', $( '#azpay-credit-payment-form #azpay-installments' ).html() );
			setInstallmentsFields( $( 'body #azpay-credit-payment-form #azpay-card-brand input' ).first().val() );
		});

		// Set on change the card brand.
		$( 'body' ).on( 'click', '#azpay-credit-payment-form #azpay-card-brand input', function() {
			$( '#azpay-credit-payment-form #azpay-select-name strong' ).html( '<strong>' + $( this ).parent( 'label' ).attr( 'title' ) + '</strong>' );
			setInstallmentsFields( $( this ).val() );
		});
	});

}( jQuery ));
