(function( $ ) {
    'use strict';
    
	$( function() {
        $(document).ready(function(){
            /*$("#billing_cpf").on('change keyup paste', function(e){
                let cpf_length = $(this).attr('maxlength');
                if (typeof attr === typeof undefined && attr === false)
                    cpf_length = 11;
                    
                if ($('#billing_cpf').val().length > cpf_length) {
                    $('#billing_cpf').val($('#billing_cpf').val().substr(0, cpf_length));
                }                
            })*/
            $( document.body ).bind( 'update_checkout', function(){                
                $(".woocommerce-checkout-payment").empty();
            } );
            if ($(".product-total small").length > 0){
                let totalText = $(".product-total small").html();
                $(".product-total small").html(totalText.substr(0, totalText.indexOf("Total: ")));
            }
            
            $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                let id= this.value;
                
                let discount = $(".payment_method_"+id+" .discount-text").html();
                if (discount != undefined){
                    $(".total-discount").remove();
                    $("<tr class='total-discount order-total'><th>Total</th><td><strong>"+discount+"</strong></td></tr>").insertAfter('tr.order-total');
                    $("tr[class=order-total]").hide();
                }else{
                    $(".total-discount").remove();
                    $("tr[class=order-total]").show();
                }
            });
            var existCondition = setInterval(function() {
                if ( $(".wc_payment_methods [type=radio]:checked").length > 0){                                      
                    setTimeout(function(){                              
                        $("[type=radio]").prop("checked", false);
                        $(".payment_box").hide();                        
                    }, 100);                    
                }else{
                    if ($(".wc_payment_methods [type=radio]:checked").length <= 0)
                    clearInterval(existCondition);
                }                
            }, 500);
            
        });
        
    });    
}( jQuery ));