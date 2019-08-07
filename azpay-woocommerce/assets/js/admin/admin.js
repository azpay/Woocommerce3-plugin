( function( $ ) {
	'use strict';

	function numberToReal(numero) {
		var numero = numero.toFixed(2).split('.');
		numero[0] = "R$ " + numero[0].split(/(?=(?:...)*$)/).join('.');
		return numero.join(',');
	}

	function numberToRealWithoutCurrency(numero) {
		var numero = numero.toFixed(2).split('.');
		numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
		return numero.join(',');
	}
	
	function addErrorMsg(element, msg){
		var offset = element.position();

		if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
			element.after( '<div class="wc_error_tip">' + msg );
			element.parent().find( '.wc_error_tip' )
				.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
				.css( 'top', offset.top + element.height() )
				.fadeIn( '100' );
		}	
	}
	function captureOrder(){
		let valor = parseFloat($('#amount_capture').val());		
		if (!isNaN(valor)){								
			let valorCompra = $("#order_total").val();				
			if (valor > parseFloat(valorCompra)){
				let element = $('#amount_capture');
				var offset = element.position();
				
				if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
					element.after( '<div class="wc_error_tip">Valor de captura maior que o da compra</div>' );
					element.parent().find( '.wc_error_tip' )
						.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
						.css( 'top', offset.top + element.height() )
						.fadeIn( '100' );
				}			
				return;
			}
			document.getElementById("capture").value=1;
			document.post.submit();
		}else if ($('#amount_capture').val() == ''){
			document.getElementById("capture").value=1;
			document.post.submit();
		}
	}

	$( function() {

		$('#woocommerce_sixbank_credit_soft_descriptor').attr('maxlength', 10);
		$('#woocommerce_sixbank_debit_soft_descriptor').attr('maxlength', 10);
		$('#woocommerce_sixbank_debit_debit_discount').attr('max', 100);
		$('#woocommerce_sixbank_slip_slip_discount').attr('max', 100);
		$('#woocommerce_sixbank_transfer_transfer_discount').attr('max', 100);
		$('#woocommerce_sixbank_credit_interest_rate').attr('max', 100);
		$('#woocommerce_sixbank_slip_instructions').attr('maxlength', 255);
		$('#woocommerce_sixbank_credit_merchant_key').attr('maxlength', 40);
		$('#woocommerce_sixbank_credit_merchant_key').attr('minlength', 30);
		$('#woocommerce_sixbank_debit_merchant_key').attr('maxlength', 40);
		$('#woocommerce_sixbank_debit_merchant_key').attr('minlength', 30);
		$('#woocommerce_sixbank_slip_merchant_key').attr('maxlength', 40);
		$('#woocommerce_sixbank_slip_merchant_key').attr('minlength', 30);
		$('#woocommerce_sixbank_transfer_merchant_key').attr('maxlength', 40);
		$('#woocommerce_sixbank_transfer_merchant_key').attr('minlength', 30);
		$('#woocommerce_sixbank_slip_slip_expire').attr('min', 1);
		$('#woocommerce_sixbank_slip_min_value').attr('min', 3);
		$('#woocommerce_sixbank_credit_min_value').attr('min', 2);
		$('#woocommerce_sixbank_debit_min_value').attr('min', 2);
		$('#woocommerce_sixbank_transfer_min_value').attr('min', 1);
		

		$('#woocommerce_sixbank_debit_soft_descriptor').bind('keyup blur',function(){ 
			var node = $(this);
			node.val(node.val().replace(/[^a-zA-Z]/g,'') ); }
		);
		$('#woocommerce_sixbank_credit_soft_descriptor').bind('keyup blur',function(){ 
			var node = $(this);
			node.val(node.val().replace(/[^a-zA-Z]/g,'') ); }
		);

		$("#mainform").submit(function(e){
            
		});
		
		$(".onlynumber").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
    		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
		});

		$(".onlycurrency").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
			$(this).val($(this).val().replace(/[^\d\,]/g,''));
			console.log(event.which);
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 44 || event.which > 57)) {
                event.preventDefault();
            }
		});

		$("#product-type").on('change', function(){
			if ( this.value== 'sixbank_subscription'){
				$("#sixbank_subscription_days").attr('required', 'required');
				$("#sixbank_subscription_frequency").attr('required', 'required');
			}else{
				$("#sixbank_subscription_days").removeAttr('required');
				$("#sixbank_subscription_frequency").removeAttr('required');				
			}
		})
		
		$('#sixbank_subscription_period').on('change', function(){
			if (this.value == 'day'){
				$("[for=sixbank_subscription_days]").html('Expira após (em dias)');
			}else if (this.value == 'week'){
				$("[for=sixbank_subscription_days]").html('Expira após (em semanas)');
			}else if (this.value == 'month'){
				$("[for=sixbank_subscription_days]").html('Expira após (em meses)');
			}else if (this.value == 'year'){
				$("[for=sixbank_subscription_days]").html('Expira após (em anos)');
			}
		});

		$('#capture_button').click(function(){
			if (confirm("Você esta certo que deseja proceder com esta captura? Esta operação não pode ser desfeita.")){
				captureOrder();
			}
		});
			
		$('#amount_capture').on('keyup', function(e){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){								
				let valorCompra = $("#order_total").val();				
				if (valor > parseFloat(valorCompra)){
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor de reembolso maior que o da compra</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}		
					e.preventDefault();			
					return;
				}
				valor = numberToReal(valor);
				$('button[name=capture]').html(`Capturar ${valor}`);
			}else{
				$('button[name=capture]').html(`Capturar`);
			}
		});

		$('#refund_amount').on('keyup', function(){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){								
				let valorCompra = $("#order_total").val();				
				if (valor > parseFloat(valorCompra)){
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor de captura maior que o da compra</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}					
					return;
				}				
			}
		});

		$("#refund_amount").val(numberToReal(parseFloat($("#order_total").val()))).change();
		$("#amount_capture").val($("#order_total").val()).keyup();
		
		/**
		 * Switch the options based on the store contract.
		 */
		$( '[id^="woocommerce_sixbank"][id$="store_contract"]' ).on( 'change', function() {
			var design      = $( '[id^="woocommerce_sixbank"][id$="_design"]' ).closest( 'tr' ),
				designTitle = design.closest( 'table' ).prev( 'h3' );

			if ( 'webservice' === $( this ).val() ) {
				design.hide();
				designTitle.hide();
			} else {
				design.show();
				designTitle.show();
			}
		}).change();

		$("#mainform").submit(function(e){
			let slipValue = $("#woocommerce_sixbank_slip_min_value").val();
			let slipMinValue = $("#woocommerce_sixbank_slip_min_value").attr('min');
			let creditValue = $("#woocommerce_sixbank_credit_min_value").val();
			let creditMinValue = $("#woocommerce_sixbank_credit_min_value").attr('min');
			let debitValue = $("#woocommerce_sixbank_debit_min_value").val();
			let debitMinValue = $("#woocommerce_sixbank_debit_min_value").attr('min');
			let transferValue = $("#woocommerce_sixbank_transfer_min_value").val();
			let transferMinValue = $("#woocommerce_sixbank_transfer_min_value").attr('min');

			if (slipValue != undefined){
				if (parseFloat(slipValue) < parseFloat(slipMinValue)){
					addErrorMsg($("#woocommerce_sixbank_slip_min_value"), 'Valor mínimo não pode ser menor que R$ 3,00');
					e.preventDefault();
				}
			}
			if (creditValue != undefined){
				if (parseFloat(creditValue) < parseFloat(creditMinValue)){
					addErrorMsg($("#woocommerce_sixbank_credit_min_value"), 'Valor mínimo não pode ser menor que R$ 2,00');
					e.preventDefault();
				}
			}
			if (debitValue != undefined){
				if (parseFloat(debitValue) < parseFloat(debitMinValue)){
					addErrorMsg($("#woocommerce_sixbank_debit_min_value"), 'Valor mínimo não pode ser menor que R$ 2,00');
					e.preventDefault();
				}
			}
			if (transferValue != undefined){
				if (parseFloat(transferValue) < parseFloat(transferMinValue)){
					addErrorMsg($("#woocommerce_sixbank_transfer_min_value"), 'Valor mínimo não pode ser menor que R$ 1,00');
					e.preventDefault();
				}
			}
			
		});

		$("#woocommerce_sixbank_slip_min_value").on('keyup', function(e){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){			
				if (valor < parseFloat(3)){
					//$("#woocommerce_sixbank_slip_min_value").val(numberToRealWithoutCurrency(valor));
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor mínimo não pode ser menor que R$ 3,00</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}		
					e.preventDefault();			
					return;
				}								
			}
		});

		$("#woocommerce_sixbank_credit_min_value").on('keyup', function(e){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){				
				if (valor < parseFloat(2)){
					//$("#woocommerce_sixbank_credit_min_value").val("2,00");
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor mínimo não pode ser menor que R$ 2,00</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}		
					e.preventDefault();			
					return;
				}								
			}
		});

		$("#woocommerce_sixbank_debit_min_value").on('keyup', function(e){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){				
				if (valor < parseFloat(2)){
					//$("#woocommerce_sixbank_debit_min_value").val("2,00");
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor mínimo não pode ser menor que R$ 2,00</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}		
					e.preventDefault();			
					return;
				}								
			}
		});

		$("#woocommerce_sixbank_transfer_min_value").on('keyup', function(e){
			let valor = parseFloat(this.value);
			if (!isNaN(valor)){				
				if (valor < parseFloat(1)){
					//$("#woocommerce_sixbank_debit_min_value").val();
					//$(this).after('<div class="wc_error_tip">Valor maior que o da compra</div>');
					let element = $(this);
					var offset = element.position();

					if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
						element.after( '<div class="wc_error_tip">Valor mínimo não pode ser menor que R$ 1,00</div>' );
						element.parent().find( '.wc_error_tip' )
							.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + element.height() )
							.fadeIn( '100' );
					}		
					e.preventDefault();			
					return;
				}								
			}
		});

	});

}( jQuery ) );
