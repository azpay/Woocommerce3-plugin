<?php
add_filter( 'product_type_selector', 'azpay_add_custom_product_type' );
add_filter( 'init', 'azpay_create_custom_product_type' );
add_filter( 'woocommerce_product_class', 'azpay_woocommerce_product_class', 10, 2 );
add_action( 'admin_footer', 'simple_subscription_custom_js' );
add_action( 'admin_footer', 'admin_options' );
add_filter( 'woocommerce_add_to_cart_validation', 'is_product_the_same_type',10,3);
add_action( 'woocommerce_single_product_summary', 'azpay_subscription_template', 60 );
add_action( 'woocommerce_azpay_subscription_to_cart', 'azpay_subscription_add_to_cart', 30 );
add_action( 'woocommerce_product_options_general_product_data', 'azpay_product_fields' );
add_action( 'woocommerce_process_product_meta', 'azpay_product_fields_save' );
add_filter( 'woocommerce_cart_item_quantity', 'azpay_product_change_quantity', 10, 3);
add_action('woocommerce_check_cart_items', 'validate_all_cart_contents');
add_filter( 'pre_option_woocommerce_default_gateway' . '__return_false', 99 );
add_action('woocommerce_pay_order_before_submit', 'teste');
function teste(){
    global $woocoomerce;

    $order_total = 0;
    if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
        $order_id = absint( get_query_var( 'order-pay' ) );
    } else {
        $order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
    }

    $order = wc_get_order($order_id);
    $cpf = get_post_meta($order->get_id(), '_billing_cpf', true);
    $rg = get_post_meta($order->get_id(), '_billing_rg', true);
    echo "<script>
    jQuery(document).ready(function($){
        $('#azpay_data').prependTo($('#payment'));
    });
    </script>";
    echo '<div id="azpay_data">
    <p class="form-row rg" id="billing_rg_field" data-priority="">
    <label for="billing_rg" class="">RG&nbsp;<span class="optional">(opcional)</span></label>
    <span class="woocommerce-input-wrapper">
    <input type="number" class="input-text " name="billing_rg" id="billing_rg" placeholder="RG" value="'.$rg.'">
    </span>
    </p>

    <p class="form-row cpf" id="billing_cpf_field" data-priority="">
    <label for="billing_cpf" class="">CPF&nbsp;<span class="optional">(opcional)</span></label>
    <span class="woocommerce-input-wrapper">
    <input type="number" class="input-text " name="billing_cpf" id="billing_cpf" placeholder="CPF" value="'.$cpf.'">
    </span></p>
    
    </div>';
}
function validate_all_cart_contents(){        
    if(WC()->cart->cart_contents_count == 0){
         return true;
    }
    
    $count = 0;
    $othertype = false;
    foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data']; 
        if ($_product->is_type('azpay_subscription')){            
            $count++;            
        }else{
            $othertype = true;
        }
    }   
        
    if($count > 0 && $othertype)  {
        wc_add_notice( __('Seu carrinho possui produto de outro tipo, é possível apenas um tipo de produto / um produto recorrente', 'azpay-woocommerce'), 'error' );
        return false;
    }else{
        return true;
    }
}
function wpb_hook_javascript() {
    ?>
        <script>
            jQuery(document).ready(function($){
                $("#rigid-account-holder .woocommerce-notices-wrapper").appendTo("#products-wrapper .woocommerce-notices-wrapper");
            });
          
        </script>
    <?php
}
add_action('wp_head', 'wpb_hook_javascript');

function admin_options() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $suffix = '';
    wp_enqueue_script( 'wc-azpay-admin', plugins_url( 'assets/js/admin/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Azpay::VERSION, true );
    
}

function azpay_subscription_add_to_cart() {
    $template_path = WP_PLUGIN_DIR . '/azpay-woocommerce/templates/';
		// Load the template
    wc_get_template( 'single-product/add-to-cart/azpay_subscription.php',
        '',
        '',
        trailingslashit( $template_path ) );
}

function azpay_create_custom_product_type(){
    class WC_Product_Custom extends WC_Product {
        public function __construct( $product ){
            parent::__construct( $product );
        }
        
        public function get_type() {
            return 'azpay_subscription';
        }
    }
}

function azpay_add_custom_product_type( $types ){
    $types[ 'azpay_subscription' ] = 'Azpay Recorrência';
    return $types;
}
            
// --------------------------
// #3 Load New Product Type Class

function azpay_woocommerce_product_class( $classname, $product_type ) {
    if ( $product_type == 'azpay_subscription' ) { 
        $classname = 'WC_Product_Custom';
    }
    return $classname;
}

function simple_subscription_custom_js() {

	if ( 'product' != get_post_type() ) :
		return;
	endif;

	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
            jQuery('.product_data_tabs .general_tab').addClass('show_if_simple show_if_azpay_subscription').show();
			jQuery('.options_group.pricing').addClass( 'show_if_azpay_subscription' ).show();
		});
	</script><?php
}


/**
 * 
 * Permitir apenas produto do mesmo tipo (Recorrencia) no carrinho
 * 
 * **/
function is_product_the_same_type($valid, $product_id, $quantity) {
    global $woocommerce;
    
    if($woocommerce->cart->cart_contents_count == 0){
         return true;
    }
    
    $count = 0;
    $othertype = false;
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data']; 
        if ($_product->is_type('azpay_subscription')){            
            $count++;            
        }else{
            $othertype = true;
        }
    }
    $_is_sub = false;
    $_product = wc_get_product( $product_id );
    if ($_product->is_type('azpay_subscription')){        
        $_is_sub = true;            
    }
        
    if($othertype && $_is_sub || $count > 0)  {
        wc_add_notice( __('Seu carrinho possui produto de outro tipo, é possível apenas um tipo de produto / um produto recorrente', 'azpay-woocommerce'), 'error' );
        return false;
    }else{
        return $valid;
    }
}

function azpay_subscription_template () {
	global $product;
	if ( 'azpay_subscription' == $product->get_type() ) {
		$template_path = WP_PLUGIN_DIR . '/azpay-woocommerce/templates/';
		// Load the template
		wc_get_template( 'single-product/add-to-cart/azpay_subscription.php',
			'',
			'',
			trailingslashit( $template_path ) );
	}
}

function azpay_product_fields() {
    echo "<div class='options_group show_if_azpay_subscription'>";

        $select_field = array(
            'id' => 'azpay_subscription_period',
            'label' => __( 'Every', 'azpay-woocommerce' ),
            'data_type' => 'number',
            'options' => array(
                'day' => __('Day', 'azpay-woocommerce'),
                'week' => __('Week', 'azpay-woocommerce'),
                'month' => __('Month', 'azpay-woocommerce'),
                'year' => __('Year', 'azpay-woocommerce')
            ),
            'desc_tip' => __('Period that charges will be made', 'azpay-woocommerce')
        );
        woocommerce_wp_select( $select_field );

        $select_field = array(
        'id' => 'azpay_subscription_days',
        'label' => __( 'Expire after (in days)', 'azpay-woocommerce' ),
        'data_type' => 'number',
        'placeholder' => '30',
        'value' => '30',
        'custom_attributes' => array( 'min' => '1' ),
        'desc_tip' => __('Duration of recurrence in days', 'azpay-woocommerce'),
        );
        woocommerce_wp_text_input( $select_field );

        $select_field = array(
            'id' => 'azpay_subscription_frequency',
            'label' => __( 'Times', 'azpay-woocommerce' ),
            'data_type' => 'number',            
            'desc_tip' => __('Amount of charges', 'azpay-woocommerce')
        );
        woocommerce_wp_text_input( $select_field );

        
    echo "</div>";
}

function azpay_product_fields_save( $post_id ){    
    // Number Field
    $azpay_subscription_days = $_POST['azpay_subscription_days'];
    update_post_meta( $post_id, 'azpay_subscription_days', esc_attr( $azpay_subscription_days ) );
    // Textarea
    $azpay_subscription_frequency = $_POST['azpay_subscription_frequency'];
    update_post_meta( $post_id, 'azpay_subscription_frequency', esc_html( $azpay_subscription_frequency ) );
    // Select
    $azpay_subscription_period = $_POST['azpay_subscription_period'];
    update_post_meta( $post_id, 'azpay_subscription_period', esc_attr( $azpay_subscription_period ) );
}

function azpay_product_change_quantity( $product_quantity, $cart_item_key, $cart_item ) {
    $product_id = $cart_item['product_id'];
    $product = wc_get_product($product_id);
    // whatever logic you want to determine whether or not to alter the input
    if ( $product->is_type('azpay_subscription') ) {
        return '<span>1</span>';
    }

    return $product_quantity;
}

?>