<?php
namespace azpay\helper;
use \WC_Payment_Gateway as WC_Payment_Gateway;
use \WC_Logger as WC_Logger;
use \Exception as Exception;
/**
 * WC Azpay Helper Class.
 */
abstract class WC_Sixbank_Helper extends WC_Payment_Gateway {

	/**
	 * Get payment methods.
	 *
	 * @return array
	 */
	public function get_payment_methods() {
		return array(
			// Credit.
			'visa'         => __( 'Visa', 'azpay-woocommerce' ),
			'mastercard'   => __( 'MasterCard', 'azpay-woocommerce' ),
			'diners'       => __( 'Diners', 'azpay-woocommerce' ),
			'discover'     => __( 'Discover', 'azpay-woocommerce' ),
			'elo'          => __( 'Elo', 'azpay-woocommerce' ),
			'amex'         => __( 'American Express', 'azpay-woocommerce' ),
			'jcb'          => __( 'JCB', 'azpay-woocommerce' ),
			'aura'         => __( 'Aura', 'azpay-woocommerce' ),

			// Debit
			'visaelectron' => __( 'Visa Electron', 'azpay-woocommerce' ),
			'maestro'      => __( 'Maestro', 'azpay-woocommerce' ),
		);
	}

	/**
	 * Get payment method name.
	 *
	 * @param  string $slug Payment method slug.
	 *
	 * @return string       Payment method name.
	 */
	public function get_payment_method_name( $slug ) {
		$methods = $this->get_payment_methods();

		if ( isset( $methods[ $slug ] ) ) {
			return $methods[ $slug ];
		}

		return $slug;
	}

	/**
	 * Get available methods options.
	 *
	 * @return array
	 */
	public function get_available_methods_options() {
		$methods = array();
		
		return $methods;
	}

	/**
	 * Get the accepted brands in a text list.
	 *
	 * @param  array $methods
	 *
	 * @return string
	 */
	public function get_accepted_brands_list( $methods ) {
		$total = count( $methods );
		$count = 1;
		$list  = '';

		foreach ( $methods as $method ) {
			$name = $this->get_payment_method_name( $method );

			if ( 1 == $total ) {
				$list .= $name;
			} else if ( $count == ( $total - 1 ) ) {
				$list .= $name . ' ';
			} else if ( $count == $total ) {
				$list .= sprintf( __( 'and %s', 'azpay-woocommerce' ), $name );
			} else {
				$list .= $name . ', ';
			}

			$count++;
		}

		return $list;
	}

	/**
	 * Get methods who accepts authorization.
	 *
	 * @return array
	 */
	public function get_accept_authorization() {
		return array( 'visa', 'mastercard' );
	}

	/**
	 * Get valid value.
	 * Prevents users from making shit!
	 *
	 * @param  string|int|float $value
	 *
	 * @return int|float
	 */
	public function get_valid_value( $value ) {
		$value = str_replace( '%', '', $value );
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Get the order API return URL.
	 *
	 * @param  WC_Order $order Order data.
	 *
	 * @return string
	 */
	public function get_api_return_url( $order ) {
		global $woocommerce;

		// Backwards compatibility with WooCommerce version prior to 2.1.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$url = WC()->api_request_url( substr(strrchr(get_class( $this ), "\\"), 1 ));
		} else {
			$url = $woocommerce->api_request_url( substr(strrchr(get_class( $this ), "\\"), 1 ));
		}

		return urlencode( add_query_arg( array( 'key' => $order->get_order_key(), 'order' => $order->get_id() ), $url ) );
	}

	/**
	 * Get the status name.
	 *
	 * @param  int $id Status ID.
	 *
	 * @return string
	 */
	public function get_status_name( $id ) {
		$status = array(
			0  => _x( 'Transaction created', 'Transaction Status', 'azpay-woocommerce' ),
			1  => _x( 'Transaction authenticated', 'Transaction Status', 'azpay-woocommerce' ),
			2  => _x( 'Transaction not authenticated', 'Transaction Status', 'azpay-woocommerce' ),
			3  => _x( 'Transaction authorized', 'Transaction Status', 'azpay-woocommerce' ),
			4  => _x( 'Transaction not authorized', 'Transaction Status', 'azpay-woocommerce' ),
			5  => _x( 'Transaction in cancellation', 'Transaction Status', 'azpay-woocommerce' ),
			6  => _x( 'Transaction cancelled', 'Transaction Status', 'azpay-woocommerce' ),
			7  => _x( 'Transaction in capture', 'Transaction Status', 'azpay-woocommerce' ),
			8  => _x( 'Transaction captured', 'Transaction Status', 'azpay-woocommerce' ),
			9  => _x( 'Transaction not captured', 'Transaction Status', 'azpay-woocommerce' ),
			10 => _x( 'Recurrent payment', 'Transaction Status', 'azpay-woocommerce' ),
			11 => _x( 'Slip generate', 'Transaction Status', 'azpay-woocommerce' ),
			
		);

		if ( isset( $status[ $id ] ) ) {
			return $status[ $id ];
		}

		return _x( "Transaction failed $id", 'Transaction Status', 'azpay-woocommerce' );
	}

	/**
	 * Get order total.
	 *
	 * @return float
	 */
	public function get_order_total() {
		global $woocommerce;

		$order_total = 0;
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$order_id = absint( get_query_var( 'order-pay' ) );
		} else {
			$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
		}

		// Gets order total from "pay for order" page.
		if ( 0 < $order_id ) {
			$order      = new \WC_Order( $order_id );
			$order_total = (float) $order->get_total();

			// Gets order total from cart/checkout.
		} elseif ( 0 < $woocommerce->cart->total ) {
			$order_total = (float) $woocommerce->cart->total;
		}

		return $order_total;
	}

	/**
	 * Get logger.
	 *
	 * @return WC_Logger instance.
	 */
	public function get_logger() {
		if ( class_exists( 'WC_Logger' ) ) {
			return new WC_Logger();
		} else {
			global $woocommerce;
			return $woocommerce->logger();
		}
	}

	/**
	 * Get log file path
	 *
	 * @return string
	 */
	public function get_log_file_path() {
		if ( function_exists( 'wc_get_log_file_path' ) ) {
			return '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'azpay-woocommerce' ) . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>';
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency() {
		return ( 'BRL' == get_woocommerce_currency() );
	}

	/**
	 * Check the environment.
	 *
	 * @return bool
	 */
	public function check_environment() {
		if ( 'test' == $this->environment ) {
			return true;
		}

		// For production.
		return ( ! empty( $this->merchant_id ) && ! empty( $this->merchant_key ) );
	}

	/**
	 * Check settings for webservice solution.
	 *
	 * @return bool
	 */
	public function checks_for_webservice() {
		
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2.11', '<=' ) ) {
			return false;
		}

		if ( 'test' == $this->environment ) {
			return true;
		}

		return 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) && is_ssl();
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available() {
		// Test if is valid for use.
		$available = parent::is_available() &&
					$this->check_environment() &&
					$this->using_supported_currency();					

		return $available;
	}

	/**
	 * Admin page.
	 */
	public function admin_options() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix = '';
		wp_enqueue_script( 'wc-azpay-admin', plugins_url( 'assets/js/admin/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), \azpay\WC_Sixbank::VERSION, true );

		include dirname( __FILE__ ) . '/views/html-admin-page.php';
	}

	/**
	 * Add error messages in checkout.
	 *
	 * @param string $message Error message.
	 */
	public function add_error( $message ) {
		global $woocommerce;

		$title = '<strong>' . esc_attr( $this->title ) . ':</strong> ';

		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $title . $message, 'error' );
		} else {
			$woocommerce->add_error( $title . $message );
		}
	}

	/**
	 * Get debit discount.
	 *
	 * @param  float $order_total Order total.
	 *
	 * @return float
	 */
	public function get_debit_discount( $order_total = 0 ) {
		$debit_total = $order_total * ( ( 100 - $this->get_valid_value( $this->debit_discount ) ) / 100 );

		return $debit_total;
	}

	public function get_slip_discount( $order_total = 0 ) {
		$debit_total = $order_total * ( ( 100 - $this->get_valid_value( $this->slip_discount ) ) / 100 );

		return $debit_total;
	}

	public function get_transfer_discount( $order_total = 0 ) {
		$debit_total = $order_total * ( ( 100 - $this->get_valid_value( $this->transfer_discount ) ) / 100 );

		return $debit_total;
	}

	/**
	 * Get installments HTML.
	 *
	 * @param  float  $order_total Order total.
	 * @param  string $type        'select' or 'radio'.
	 *
	 * @return string
	 */
	public function get_installments_html( $order_total = 0, $type = 'select' ) {
		$html         = '';
		$installments = apply_filters( 'WC_Sixbank_max_installments', $this->installments, $order_total );

		if ( '1' == $installments ) {
			return $html;
		}

		if ( 'select' == $type ) {
			$html .= '<select id="azpay-installments" name="sixbank_credit_installments" style="font-size: 1.5em; padding: 4px; width: 100%;">';
		}

		$interest_rate = $this->get_valid_value( $this->interest_rate ) / 100;

		for ( $i = 1; $i <= $installments; $i++ ) {
			$credit_total    = $order_total / $i;
			$credit_interest = sprintf( __( 'no interest. Total: %s', 'azpay-woocommerce' ), sanitize_text_field( wc_price( $order_total ) ) );
			$smallest_value  = ( 5 <= $this->smallest_installment ) ? $this->smallest_installment : 5;

			if ( $i >= $this->interest && 0 < $interest_rate ) {
				$interest_total = $order_total * ( $interest_rate / ( 1 - ( 1 / pow( 1 + $interest_rate, $i ) ) ) );
				$interest_order_total = $interest_total * $i;

				if ( $credit_total < $interest_total ) {
					$credit_total    = $interest_total;
					$credit_interest = sprintf( __( 'with interest of %s%% a.m. Total: %s', 'azpay-woocommerce' ), $this->get_valid_value( $this->interest_rate ), sanitize_text_field( wc_price( $interest_order_total ) ) );
				}
			}

			if ( 1 != $i && $credit_total < $smallest_value ) {
				continue;
			}

			$at_sight = ( 1 == $i ) ? 'azpay-at-sight' : '';

			if ( 'select' == $type ) {
				if ($i == 1)
				$html .= '<option value="' . $i . '" class="' . $at_sight . '">' . sprintf( __( 'at sight. Total: %s', 'azpay-woocommerce' ), sanitize_text_field( wc_price( $credit_total ) ) ) . '</option>';
				else
				$html .= '<option value="' . $i . '" class="' . $at_sight . '">' . sprintf( __( '%sx of %s %s', 'azpay-woocommerce' ), $i, sanitize_text_field( wc_price( $credit_total ) ), $credit_interest ) . '</option>';
			} else {
				$html .= '<label class="' . $at_sight . '"><input type="radio" name="sixbank_credit_installments" value="' . $i . '" /> ' . sprintf( __( '%sx of %s %s', 'azpay-woocommerce' ), $i, '<strong>' . sanitize_text_field( wc_price( $credit_total ) ) . '</strong>', $credit_interest ) . '</label>';
			}
		}

		if ( 'select' == $type ) {
			$html .= '</select>';
		}

		return $html;
	}

	/**
	 * Get single installment text.
	 *
	 * @param  int   $quantity
	 * @param  float $order_total
	 *
	 * @return string
	 */
	public function get_installment_text( $quantity, $order_total ) {
		$credit_total    = $order_total / $quantity;
		$credit_interest = sprintf( __( 'no interest. Total: %s', 'azpay-woocommerce' ), sanitize_text_field( wc_price( $order_total ) ) );
		$interest_rate   = $this->get_valid_value( $this->interest_rate ) / 100;

		if ( $quantity >= $this->interest && 0 < $interest_rate ) {
			$interest_total       = $order_total * ( $interest_rate / ( 1 - ( 1 / pow( 1 + $interest_rate, $quantity ) ) ) );
			$interest_order_total = $interest_total * $quantity;

			if ( $credit_total < $interest_total ) {
				$credit_total    = $interest_total;
				$credit_interest = sprintf( __( 'with interest of %s%% a.m. Total: %s', 'azpay-woocommerce' ), $this->get_valid_value( $this->interest_rate ), sanitize_text_field( wc_price( $interest_order_total ) ) );
			}
		}

		return sprintf( __( '%sx of %s %s', 'azpay-woocommerce' ), $quantity, sanitize_text_field( wc_price( $credit_total ) ), $credit_interest );
	}

	/**
	 * Get Checkout form field.
	 *
	 * @param  string $model
	 * @param  float  $order_total
	 */
	protected function get_checkout_form( $model = 'default', $order_total = 0 ) {

	}

	/**
	 * Payment fields.
	 *
	 * @return string
	 */
	public function payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}	

		wp_enqueue_script( 'wc-credit-card-form' );

		$model = 'webservice';		

		// Get order total.
		if ( method_exists( $this, 'get_order_total' ) ) {
			$order_total = $this->get_order_total();
		} else {
			$order_total = $this->get_order_total();
		}
		
		$this->get_checkout_form( $model, $order_total );
	}
	

	protected function validate_rg_cpf_fields( $posted, $validate_rg, $validate_cpf, $validate_valid_cpf ){
		try {
			// Validate name typed for the card.
			
			if ( ! isset( $posted[ 'billing_rg'] ) || '' === $posted[ 'billing_rg' ] ) {
				throw new Exception( $validate_rg );
			}

			if ( ! isset( $posted[ 'billing_cpf'] ) || '' === $posted[ 'billing_cpf' ] ) {
				throw new Exception( $validate_cpf );
			}			
			
			if ( isset( $posted[ 'billing_cpf'] ) && '' !== $posted[ 'billing_cpf' ] && !$this->validaCPF($posted[ 'billing_cpf'] )){
				throw new Exception( $validate_valid_cpf );
			}
		
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	protected function validate_slip_fields( $posted, $validate_rg_cpf, $validate_valid_cpf ){
		try {
			// Validate name typed for the card.
			$count = 0;
			if ( ! isset( $posted[ 'billing_rg'] ) || '' === $posted[ 'billing_rg' ] ) {
				$count++;
			}

			if ( ! isset( $posted[ 'billing_cpf'] ) || '' === $posted[ 'billing_cpf' ] ) {
				$count++;
			}
			
			if ($count >= 2){
				throw new Exception( $validate_rg_cpf );
			}

			if ( isset( $posted[ 'billing_cpf'] ) && '' !== $posted[ 'billing_cpf' ] && !$this->validaCPF($posted[ 'billing_cpf'] )){
				throw new Exception( $validate_valid_cpf );
			}
		
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	protected function validate_cpf_fields( $posted, $validate_valid_cpf ){
		try {
			// Validate name typed for the card.

			if ( isset( $posted[ 'billing_cpf'] ) && '' !== $posted[ 'billing_cpf' ] && !$this->validaCPF($posted[ 'billing_cpf'] )){
				throw new Exception( $validate_valid_cpf );
			}
		
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	
	function validaCPF($cpf) {
 
		// Extrai somente os números
		$cpf = preg_replace( '/[^0-9]/is', '', $cpf );
		 
		// Verifica se foi informado todos os digitos corretamente
		if (strlen($cpf) != 11) {
			return false;
		}
		// Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
		if (preg_match('/(\d)\1{10}/', $cpf)) {
			return false;
		}
		// Faz o calculo para validar o CPF
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) {
				return false;
			}
		}
		return true;
	}
	

	/**
	 * Validate credit brand.
	 *
	 * @param  string $card_brand
	 *
	 * @return bool
	 */
	protected function validate_credit_brand( $card_brand ) {
		try {
			// Validate the card brand.
			if ( ! isset($card_brand) || empty( $card_brand ) ) {
				throw new Exception( __( 'Please enter with a valid card brand.', 'azpay-woocommerce' ) );
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Validate card fields.
	 *
	 * @param  array $posted
	 *
	 * @return bool
	 */
	protected function validate_card_fields( $posted, $validate_name_holder, $validate_card_date, $validate_cvv ) {
		try {
			// Validate name typed for the card.
			if ( ! isset( $posted[ $this->id . '_holder_name' ] ) || '' === $posted[ $this->id . '_holder_name' ] ) {
				throw new Exception( $validate_name_holder );
			}

			// Validate the expiration date.
			if ( ! isset( $posted[ $this->id . '_expiry' ] ) || '' === $posted[ $this->id . '_expiry' ] ) {
				throw new Exception( $validate_card_date );
			}

			// Validate the cvv for the card.
			if ( ! isset( $posted[ $this->id . '_cvv' ] ) || '' === $posted[ $this->id . '_cvv' ] ) {
				throw new Exception( $validate_cvv );
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}	

	protected function validate_expiration_date( $posted, $validate_expired_date ) {
		try {	
			$expiration_date = $posted[ $this->id . '_expiry' ];
			$expiry_date = explode( '/', sanitize_text_field( $expiration_date ) );
			$expiry_date = trim( $expiry_date[1] ) . trim( $expiry_date[0] );
			$expiry_date = ( 4 == strlen( $expiry_date ) ) ? '20' . $expiry_date : $expiry_date;
			

			$expires = \DateTime::createFromFormat('Ym', $expiry_date);
			$now     = new \DateTime();
			
			if ($expires < $now) {
				throw new Exception( $validate_expired_date );
			}		
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Validate installments.
	 *
	 * @param  array $posted
	 * @param  float $order_total
	 *
	 * @return bool
	 */
	protected function validate_installments( $posted, $order_total ) {
		// Stop if don't have installments.
		if ( ! isset( $posted['sixbank_credit_installments'] ) && 1 == $this->installments ) {
			return true;
		}

		try {

			$installments      = ! isset( $posted['sixbank_credit_installments'] ) ? absint( $posted['sixbank_credit_installments'] ) : 1;
			$installment_total = $order_total / $installments;
			$_installments     = apply_filters( 'WC_Sixbank_max_installments', $this->installments, $order_total );
			$interest_rate     = $this->get_valid_value( $this->interest_rate ) / 100;

			if ( $installments >= $this->interest && 0 < $interest_rate ) {
				$interest_total    = $order_total * ( $interest_rate / ( 1 - ( 1 / pow( 1 + $interest_rate, $installments ) ) ) );
				$installment_total = ( $installment_total < $interest_total ) ? $interest_total : $installment_total;
			}
			$smallest_value = ( 5 <= $this->smallest_installment ) ? $this->smallest_installment : 5;

			if ( $installments > $_installments || 1 != $installments && $installment_total < $smallest_value ) {
			 	throw new Exception( __( 'Invalid number of installments!', 'azpay-woocommerce' ) );
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Process webservice payment.
	 *
	 * @param  WC_Order $order
	 *
	 * @return array
	 */
	protected function process_webservice_payment( $order ) {
		return array();
	}

	/**
	 * Process buy page azpay payment.
	 *
	 * @param  WC_Order $order
	 *
	 * @return array
	 */
	protected function process_buypage_sixbank_payment( $order ) {
		return array();
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array           Redirect.
	 */
	public function process_payment( $order_id ) {
		$order = new \WC_Order( $order_id );

		$order_email = $order->get_billing_email();
		$user = email_exists( $order_email );  
		//Se não encontrou por email, tenta pelo username
		if ($user == false)
			$user = username_exists( $order_email );
		
		// if the UID is null, then it's a guest checkout
		if( $user == false ){			
			$random_password = wp_generate_password();
			// create new user with email as username & newly created pw
			$user_id = wp_create_user( $order_email, $random_password, $order_email );

			update_user_meta( $user_id, 'billing_address_1', $order->billing_address_1 );
			update_user_meta( $user_id, 'billing_address_2', $order->billing_address_2 );
			update_user_meta( $user_id, 'billing_city', $order->billing_city );
			update_user_meta( $user_id, 'billing_company', $order->billing_company );
			update_user_meta( $user_id, 'billing_country', $order->billing_country );
			update_user_meta( $user_id, 'billing_email', $order->billing_email );
			update_user_meta( $user_id, 'billing_first_name', $order->billing_first_name );
			update_user_meta( $user_id, 'billing_last_name', $order->billing_last_name );
			update_user_meta( $user_id, 'billing_phone', $order->billing_phone );
			update_user_meta( $user_id, 'billing_postcode', $order->billing_postcode );
			update_user_meta( $user_id, 'billing_state', $order->billing_state );
			update_user_meta( $user_id, 'billing_cpf', $order->billing_cpf );
			update_user_meta( $user_id, 'billing_rg', $order->billing_rg );
			
			// user's shipping data
			update_user_meta( $user_id, 'shipping_address_1', $order->shipping_address_1 );
			update_user_meta( $user_id, 'shipping_address_2', $order->shipping_address_2 );
			update_user_meta( $user_id, 'shipping_city', $order->shipping_city );
			update_user_meta( $user_id, 'shipping_company', $order->shipping_company );
			update_user_meta( $user_id, 'shipping_country', $order->shipping_country );
			update_user_meta( $user_id, 'shipping_first_name', $order->shipping_first_name );
			update_user_meta( $user_id, 'shipping_last_name', $order->shipping_last_name );
			update_user_meta( $user_id, 'shipping_method', $order->shipping_method );
			update_user_meta( $user_id, 'shipping_postcode', $order->shipping_postcode );
			update_user_meta( $user_id, 'shipping_state', $order->shipping_state );

			$order->set_customer_id($user_id);
			wc_update_new_customer_past_orders($user_id);
		}else{
			$order->set_customer_id($user);
		}
		$order->save();
		return $this->process_webservice_payment( $order );
		
	}

	/**
	 * Process the order status.
	 *
	 * @param WC_Order $Order  Order data.
	 * @param int      $status Status ID.
	 * @param string   $note   Custom order note.
	 */
	public function process_order_status( $order, $status, $note = '' ) {
		$status_note = $status . " | " . __( 'Azpay', 'azpay-woocommerce' ) . ': ' . $this->get_status_name( $status );

		// Order cancelled.
		if ( 6 == $status ) {
			$order->update_status( 'cancelled', $status_note );

			// Order failed.
		} elseif ( 2 == $status || 4 == $status ) {
			$order->update_status( 'failed', $status_note );

			// Order paid.
		} elseif ( 8 == $status ) {
			$order->add_order_note( $status_note . '. ' . $note );

			// Complete the payment and reduce stock levels.
			$order->payment_complete();
			$order->update_status( 'processing', $status_note );
			
		} elseif ( 3 == $status ) {
			$order->update_status( 'authorized', $status_note );

			// Order paid.
		} elseif (10 == $status ){
			$order->add_order_note( __('Recurring payment created', 'azpay-woocommerce') );
		}else {
			$order->update_status( 'on-hold', $status_note );
		}
		$order->save();
	}

	/**
	 * Check return.
	 */
	public function check_return() {
		@ob_clean();

		if ( isset( $_GET['key'] ) && isset( $_GET['order'] ) ) {
			header( 'HTTP/1.1 200 OK' );

			$order_id = absint( $_GET['order'] );
			$order    = new \WC_Order( $order_id );

			if ( $order->order_key == $_GET['key'] ) {
				do_action( 'woocommerce_' . $this->id . '_return', $order );
			}
		}

		wp_die( __( 'Invalid request', 'azpay-woocommerce' ) );
	}

	/**
	 * Return handler.
	 *
	 * @param WC_Order $order Order data.
	 */
	public function return_handler( $order ) {
		global $woocommerce;

		$tid = get_post_meta( $order->get_id(), '_sixbank_tid', true );
		
		if ( '' != $tid ) {
			$response = $this->api->get_transaction_data( $order, $tid, $order->get_id() . '-' . time() );
			
			// Set the error alert.
			$response = $response->getResponse();
			if ( ! empty( $response['errorCode'] ) ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Azpay payment error: ' . print_r( $response, true ) );
				}

				$this->helper->add_error( (string) $response['message'] );
			}

			// Update the order status.
			$status     = ! empty( $response['status'] ) ? intval( $response['status'] ) : -1;
			$order_note = "\n";
			
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Azpay payment status: ' . $status );
			}

			// For backward compatibility!
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1.12', '<=' ) ) {
				$order_note = "\n" . 'TID: ' . $tid . '.';
			}
			
			$this->process_order_status( $order, $status, $order_note );

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$return_url = $this->get_return_url( $order );
			} else {
				$return_url = add_query_arg( 'order', $order->get_id(), add_query_arg( 'key', $order->order_key, get_permalink( woocommerce_get_page_id( 'thanks' ) ) ) );
			}
			
			// Order cancelled.
			if ( 9 == $status ) {
				$message = __( 'Order canceled successfully.', 'azpay-woocommerce' );
				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( $message );
				} else {
					$woocommerce->add_message( $message );
				}

				if ( function_exists( 'wc_get_page_id' ) ) {
					$return_url = get_permalink( wc_get_page_id( 'shop' ) );
				} else {
					$return_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
				}
			}

			wp_redirect( esc_url_raw( $return_url ) );
			exit;
		} else {
			if ( function_exists( 'wc_get_page_id' ) ) {
				$cart_url = get_permalink( wc_get_page_id( 'cart' ) );
			} else {
				$cart_url = get_permalink( woocommerce_get_page_id( 'cart' ) );
			}

			wp_redirect( esc_url_raw( $cart_url ) );
			exit;
		}
	}

	/**
	 * Process a refund in WooCommerce 2.2 or later.
	 *
	 * @param  int    $order_id
	 * @param  float  $amount
	 * @param  string $reason
	 *
	 * @return bool|WP_Error True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = new \WC_Order( $order_id );

		$tid = get_post_meta( $order_id, '_sixbank_tid', true );
		if ( ! $order || ! $tid ) {
			return new WP_Error( 'sixbank_refund_error',  __( 'Purchase or transaction not found!', 'azpay-woocommerce' ) );
			return false;
		}

		if ( $order->get_date_created() === NULL ){
			return new WP_Error( 'sixbank_refund_error',  __( 'Created date not registered!', 'azpay-woocommerce' ) );
			return false;
		}

		$diff  = ( strtotime( $order->get_date_created() ) - strtotime( current_time( 'mysql' ) ) );
		$days  = absint( $diff / ( 60 * 60 * 24 ) );
		$limit = 120;

		if ( $limit > $days ) {	
			$amount   = wc_format_decimal( $amount );
			try{
				$response = $this->api->do_transaction_cancellation( $order, $tid, $order->get_id(), $amount );

				$response = $response->getResponse();			
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Cancelamento: ' . print_r($response, true) );
				}
				// Already canceled.
				if ( ! empty( $response['errorCode'] ) ) {
					$order->add_order_note( __( 'Azpay', 'azpay-woocommerce' ) . ': ' . sanitize_text_field( $response['message'] ) );

					return new WP_Error( 'sixbank_refund_error', sanitize_text_field( $response['errorCode'] ) );
				} else {
					//if ( isset( $response->cancelamentos->cancelamento ) ) {
					$order->add_order_note( sprintf( __( 'Azpay: %s - Refunded amount: %s.', 'azpay-woocommerce' ), sanitize_text_field( $response['processors'][0]['processor']['acquirer'] ), wc_price( $response['processors'][0]['processor']['amount'] / 100 ) ) );
					//}
					$order->update_status('refunded');
					$order->save();

					return true;
				}
			}catch(Exception $e){
				return new WP_Error( 'sixbank_refund_error', __( 'Purchat cannot be refunded. ' . html_entity_decode( $e->getMessage() ), 'azpay-woocommerce' ) );
			}
		} else {
			return new WP_Error( 'sixbank_refund_error', sprintf( __( 'This transaction has been made ​​more than %s days and therefore it can not be canceled', 'azpay-woocommerce' ), $limit ) );
		}

		return false;
	}

	/**
	 * Thank you page message.
	 *
	 * @return string
	 */
	public function thankyou_page( $order_id ) {
		global $woocommerce;

		$order = new \WC_Order( $order_id );
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$order_url = $order->get_view_order_url();
		} else {
			$order_url = add_query_arg( 'order', $order_id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) );
		}

		if ( $order->status == 'processing' || $order->status == 'completed' ) {
			echo '<div class="woocommerce-message"><a href="' . esc_url( $order_url ) . '" class="button" style="display: block !important; visibility: visible !important;">' . __( 'View order details', 'azpay-woocommerce' ) . '</a>' . sprintf( __( 'Your payment has been received successfully.', 'azpay-woocommerce' ), wc_price( $order->order_total ) ) . '<br />' . __( 'The authorization code was generated.', 'azpay-woocommerce' ) . '</div>';
		} else if ($order->status == 'on-hold' && $order->get_payment_method() == 'sixbank_slip'){		
			$html = '<div class="woocommerce-info">';
			$html .= sprintf( '<a class="button" href="%s" target="_blank">%s</a>', get_post_meta( $order->get_id(), '_slip_url', true ), __( 'Imprimir boleto', 'boletosimples-woocommerce' ) );
			$gateway = wc_get_payment_gateway_by_order($order);
			$message = property_exists( $gateway , 'slip_text' ) ? $gateway->slip_text : '';
			$html .= apply_filters( 'woocommerce_boletosimples_pending_payment_instructions', $message, $order );
			$html .= '</div>';
			echo $html;		
		}else {
			echo '<div class="woocommerce-info">' . sprintf( __( 'For more information or questions regarding your order, go to the %s.', 'azpay-woocommerce' ), '<a href="' . esc_url( $order_url ) . '">' . __( 'order details page', 'azpay-woocommerce' ) . '</a>' ) . '</div>';
		}
	}
}
