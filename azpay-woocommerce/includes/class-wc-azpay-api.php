<?php

foreach (glob(plugin_dir_path( __FILE__ ) . "/gateway/API/*.php") as $filename)
{	
	//echo $filename . "<BR>";
    require_once $filename;
}
/**
 * WC Azpay API Class.
 */
class WC_Azpay_API {

	/**
	 * API version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Currency.
	 */
	const CURRENCY = '986';

	/**
	 * Gateway class.
	 *
	 * @var WC_Azpay_Gateway
	 */
	protected $gateway;

	/**
	 * Charset.
	 *
	 * @var string
	 */
	protected $charset = 'ISO-8859-1';

	/**
	 * Test Environment URL.
	 *
	 * @var string
	 */
	protected $test_url = 'https://sandbox-api.gateway.azpaygroup.com/v1/receiver';

	/**
	 * Production Environment URL.
	 *
	 * @var string
	 */
	protected $production_url = 'https://api.gateway.azpaygroup.com/v1/receiver';

	/**
	 * Test Store Number.
	 *
	 * @var string
	 */
	protected $test_store_number = '1006993069';

	/**
	 * Test Store Key.
	 *
	 * @var string
	 */
	protected $test_store_key = '25fbb99741c739dd84d7b06ec78c9bac718838630f30b112d033ce2e621b34f3';

	/**
	 * Test Azpay Number.
	 *
	 * @var string
	 */
	protected $test_azpay_number = '1001734898';

	/**
	 * Test Azpay Key.
	 *
	 * @var string
	 */
	protected $test_azpay_key = 'e84827130b9837473681c2787007da5914d6359947015a5cdb2b8843db0fa832';

	/**
	 * Constructor.
	 *
	 * @param WC_Azpay_Gateway $gateway
	 */
	public function __construct( $gateway = null ) {
		$this->gateway = $gateway;
		$this->charset = get_bloginfo( 'charset' );
		
	}

	public function register_payment_db($transaction_id, $data){
		global $wpdb;

		$table_name = $wpdb->prefix . 'azpay_subscription';

		$wpdb->insert( 
			$table_name, 
			array( 
				'transaction_id' 	 => $transaction_id,
				'date' 	 => $data['date'],
				'tid' 	 => $data['tid'],
				'ticket' => $data['ticket'],
				'amount' => $data['amount'],
				'status' => $data['status'],
			) 
		);
	}

	/**
	 * Set cURL custom settings for Azpay.
	 *
	 * @param  resource $handle The cURL handle returned by curl_init().
	 * @param  array    $r      The HTTP request arguments.
	 * @param  string   $url    The destination URL.
	 */
	public function curl_settings( $handle, $r, $url ) {
		if ( isset( $r['sslcertificates'] ) && $this->get_certificate() === $r['sslcertificates'] && $this->get_api_url() === $url ) {
			curl_setopt( $handle, CURLOPT_SSLVERSION, 4 );
		}
	}

	/**
	 * Get API URL.
	 *
	 * @return string
	 */
	public function get_api_url() {
		if ( 'production' == $this->gateway->environment ) {
			return $this->production_url;
		} else {
			return $this->test_url;
		}
	}

	/**
	 * Get certificate.
	 *
	 * @return string
	 */
	protected function get_certificate() {
		return plugin_dir_path( __FILE__ ) . 'certificates/VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt';
	}

	/**
	 * Get credit card brand.
	 *
	 * @param  string $number
	 *
	 * @return string
	 */
	public function get_card_brand( $number ) {
		$number = preg_replace( '([^0-9])', '', $number );
		$brand  = 'visa';

		// https://gist.github.com/arlm/ceb14a05efd076b4fae5
		$supported_brands = array(
			'visa'       => '/^4\d{12}(\d{3})?$/',
			'mastercard' => '/^(5[1-5]\d{4}|677189)\d{10}$/',
			'diners'     => '/^3(0[0-5]|[68]\d)\d{11}$/',
			'discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
			'elo'        => '/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/',
			'amex'       => '/^3[47]\d{13}$/',
			'jcb'        => '/^(?:2131|1800|35\d{3})\d{11}$/',
			'aura'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
			'hipercard'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
			'maestro'    => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
		);

		foreach ( $supported_brands as $key => $value ) {
			if ( preg_match( $value, $number ) ) {
				$brand = $key;
				break;
			}
		}

		return $brand;
	}

	public function get_debit_card_brand( $number ) {
		$number = preg_replace( '([^0-9])', '', $number );
		
		$brand = "visa";
		$json = file_get_contents("https://lookup.binlist.net/45717360");
		$result = json_decode($json);
	
		if (isset( $result ) && isset( $result->scheme )){
			$brand = $result->scheme;
		}
	
		return $brand;
	}

	/**
	 * Get language.
	 *
	 * @return string
	 */
	protected function get_language() {
		$language = strtoupper( substr( get_locale(), 0, 2 ) );

		if ( ! in_array( $language, array( 'PT', 'EN', 'ES' ) ) ) {
			$language = 'EN';
		}

		return $language;
	}

	/**
	 * Get the secure XML data for debug.
	 *
	 * @param  WC_Azpay_XML $xml
	 *
	 * @return WC_Azpay_XML
	 */
	protected function get_secure_xml_data( $xml ) {
		// Remove API data.
		if ( isset( $xml->{'dados-ec'} ) ) {
			unset( $xml->{'dados-ec'} );
		}

		// Remove card data.
		if ( isset( $xml->{'dados-portador'} ) ) {
			unset( $xml->{'dados-portador'} );
		}

		return $xml;
	}

	/**
	 * Get default error message.
	 *
	 * @return StdClass
	 */
	protected function get_default_error_message() {
		$error = new StdClass;
		$error->mensagem = __( 'An error has occurred while processing your payment, please try again or contact us for assistance.', 'azpay-woocommerce' );

		return $error;
	}

	/**
	 * Safe load XML.
	 *
	 * @param  string $source  XML source.
	 * @param  int    $options DOMDocument options.
	 *
	 * @return SimpleXMLElement|bool
	 */
	protected function safe_load_xml( $source, $options = 0 ) {
		$old    = null;
		$source = trim( $source );

		if ( '<' !== substr( $source, 0, 1 ) ) {
			return false;
		}

		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			$old = libxml_disable_entity_loader( true );
		}

		$dom    = new DOMDocument();
		$return = $dom->loadXML( $source, $options );

		if ( ! is_null( $old ) ) {
			libxml_disable_entity_loader( $old );
		}

		if ( ! $return ) {
			return false;
		}

		if ( isset( $dom->doctype ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'Unsafe DOCTYPE Detected while XML parsing' );
			}

			return false;
		}

		return simplexml_import_dom( $dom );
	}

	/**
	 * Do remote requests.
	 *
	 * @param  string $data Post data.
	 *
	 * @return array        Remote response data.
	 */
	protected function do_request( $data ) {
		$params = array(
			'body'            => 'mensagem=' . $data,
			'sslverify'       => true,
			'timeout'         => 40,
			'sslcertificates' => $this->get_certificate(),
			'headers'         => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
		);

		add_action( 'http_api_curl', array( $this, 'curl_settings' ), 10, 3 );
		$response = wp_remote_post( $this->get_api_url(), $params );
		remove_action( 'http_api_curl', array( $this, 'curl_settings' ), 10 );

		return $response;
	}

	public function do_capture($order, $tid, $amount = NULL){
		if ( 'production' == $this->gateway->environment ) {
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::PRODUCTION);
		}else
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::SANDBOX);
				
		$gateway = new Gateway($credential);
		$response = $gateway->Report($tid);
		
		if ($response->canCapture()){
			$response = $gateway->Capture($tid, $amount);	
			$order->add_order_note( "Capturando transação. " );			
			update_post_meta($order->get_id(), '_payment_captured', true);	
					
			$response = $this->gateway->report($order, $tid);
			$status = $response->getResponse()['status'];
			
			//Atualizar de acordo com status
			switch($status){
				case 0: //Criado
					$order->add_order_note( "Criada" );
					break;
				case 1: //Autenticada
					$order->add_order_note( "Autenticada" );
					break;
				case 2: //Não-autenticada
					$order->add_order_note( "Não-autenticada" );
					break;
				case 3: //Autorizada pela operadora
					$order->add_order_note( "Autorizada pela operadora" );
					$order->update_status( 'authorized' );
					break;
				case 4: //Não-autorizada pela operadora
					$order->add_order_note( "Não-autorizada pela operadora" );
					$order->update_status( 'failed' );
					break;
				case 5: //Em cancelamento
					$order->add_order_note( "Em cancelamento" );
					break;
				case 6: //Cancelado
					$order->add_order_note( "Cancelado" );
					$order->update_status( 'cancelled' );
					break;
				case 7: //Em captura
					$order->add_order_note( "Em captura" );
					break;
				case 8: //Capturada / Finalizada
					if ($amount != NULL)
					$order->add_order_note( "Capturada / Finalizada R$ " . $amount / 100  );
					else
					$order->add_order_note( "Capturada / Finalizada" );
					
					$order->payment_complete();
					$order->update_status( 'processing' );
					break;
				case 9: //Não-capturada
					$order->add_order_note( "Não-capturada" );
					break;
				case 10: //Pagamento Recorrente - Agendada
					$order->add_order_note( "Pagamento Recorrente - Agendada" );
					break;
				case 11: //Boleto Gerado
					$order->add_order_note( "Boleto gerado" );
					break;
			}

			$order->save();
		}
	}

	public function do_report($order, $tid){
		if ( 'production' == $this->gateway->environment ) {
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::PRODUCTION);
		}else
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::SANDBOX);
				
		$gateway = new Gateway($credential);
		$response = $gateway->Report($tid);		
		return $response;
	}

	/**
	 * Do transaction.
	 *
	 * @param  WC_Order $order            Order data.
	 * @param  string   $id               Request ID.
	 * @param  string   $card_brand       Card brand slug.
	 * @param  int      $installments     Number of installments (use 0 for debit).
	 * @param  array    $credit_card_data Credit card data for the webservice.
	 * @param  int     $payment_type (1 = debit, 2 = credit, 3 = slip, 4 = transfer)   Check if is debit or credit.
	 *
	 * @return SimpleXmlElement|StdClass Transaction data.
	 */
	public function do_transaction( $order, $id, $card_brand, $installments = 0, $credit_card_data = array(), $payment_type = 1) {		
		$order_total     = (float) $order->get_total();

		$billing_rg = $order->get_meta( '_billing_rg' );
		$billing_cpf = $order->get_meta( '_billing_cpf' );

		if ( isset( $billing_rg ) || isset( $billing_cpf) ) {			
			// Set the session data
			WC()->session->set( 'custom_data', array( 'billing_rg' => $billing_rg, 'billing_cpf' => $billing_cpf ) );
		}

		// Set the order total with interest.
		if ( $installments >= $this->gateway->interest && isset($this->gateway->interest)  ) {
			$interest_rate        = $this->gateway->get_valid_value( $this->gateway->interest_rate ) / 100;
			$interest_total       = $order_total * ( $interest_rate / ( 1 - ( 1 / pow( 1 + $interest_rate, $installments ) ) ) );
			$interest_order_total = $interest_total * $installments;

			if ( $order_total < $interest_order_total ) {
				$order_total = round( $interest_order_total, 2 );
			}
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, "log credit $payment_type - total: $order_total | " .  $this->gateway->installment_type . " installments $installments - " . $this->gateway->interest);
		}

		// Set the debit values.
		if ( $payment_type == 1 ) {
			$order_total     = $order_total * ( ( 100 - $this->gateway->get_valid_value( $this->gateway->debit_discount ) ) / 100 );			
			$installments    = 1;
		}else if ($payment_type == 3){
			$order_total     = $order_total * ( ( 100 - $this->gateway->get_valid_value( $this->gateway->slip_discount ) ) / 100 );
		}else if ($payment_type == 4){
			$order_total     = $order_total * ( ( 100 - $this->gateway->get_valid_value( $this->gateway->transfer_discount ) ) / 100 );
		}
		//Atualiza total com disconto
		$order->set_total($order_total);
		$order->save();
		$order_total = $order_total * 100;
		

		try{
			if ( 'production' == $this->gateway->environment ) {
				$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::PRODUCTION);
			}else
				$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::SANDBOX);
					
			
			$gateway = new Gateway($credential);

			$expiry_date = explode( '/', sanitize_text_field( $credit_card_data['card_expiration'] ) );
			$expiry_date = trim( $expiry_date[1] ) . trim( $expiry_date[0] );
			$expiry_date = ( 4 == strlen( $expiry_date ) ) ? '20' . $expiry_date : $expiry_date;
			
			$subscription = false;
			//Verifica se a compra é de recorrencia
			foreach ( WC()->cart->get_cart_contents() as $key => $values ) {
				$_product = $values['data'];
				if ($_product->is_type('azpay_subscription')){
					$subscription = true;					
					$frequency = (int) get_post_meta($_product->get_id(), 'azpay_subscription_frequency', true);
					$period = get_post_meta($_product->get_id(), 'azpay_subscription_period', true);
					$days = get_post_meta($_product->get_id(), 'azpay_subscription_days', true);
					$endDate = date('Y-m-d', strtotime(sprintf("+ %d $period", $days)));
					if ($period == 'day'){
						$period = Rebill::DAILY;
					}else if ($period == 'week'){
						$period = Rebill::WEEKLY;
					}else if ($period == 'month'){
						$period = Rebill::MONTHLY;
					}else if ($period == 'year'){
						$period = Rebill::YEARLY;
					}

					if ( 'yes' == $this->gateway->debug ) {
						$this->gateway->log->add( $this->gateway->id, "log subscription.. Frequency: $frequency | Period: $period | Days: $days | endDate: $endDate" );
					}
				}
			}
			
			### CREATE A NEW TRANSACTION
			$transaction = new Transaction();
			// Set ORDER
			if ($subscription){
				$transaction->Order()
				->setReference($order->get_order_number())
				->setTotalAmount((int) $order_total)
				->setDateStart(date('Y-m-d') )
				->setDateEnd($endDate)
				->setPeriod($period)
				->setFrequency($frequency);

				$response = $this->credit_subscription_payment($order, $transaction, $gateway, $payment_type, $installments, $card_brand, $credit_card_data, $expiry_date);

			}else{
				
				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, "log credit $payment_type - total: $order_total | " .  $this->gateway->installment_type . " installments $installments - " . $this->gateway->interest);
				}
				$transaction->Order()
				->setReference($order->get_order_number())
				->setTotalAmount((int) $order_total);
			
				if ($payment_type == 3){
					$response = $this->slip_payment($order, $transaction, $gateway, $payment_type);				
				}else if ($payment_type == 4){
					$response = $this->transfer_payment($order, $transaction, $gateway, $payment_type);				
				}else{
					$response = $this->credit_debit_payment($order, $transaction, $gateway, $payment_type, $installments, $card_brand, $credit_card_data, $expiry_date);
				}
			}
		} catch (Exception $e){
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'An error occurred while requesting the transaction: ' . print_r( $e, true ) );
			}
			if (!isset($response)) $response = new stdClass();
			$response->message = __($e->getMessage(), 'azpay-woocommerce');
		}
		return $response;
	}

	private function credit_debit_payment($order, $transaction, $gateway, $payment_type, $installments, 
		$card_brand, $credit_card_data, $expiry_date){

		$billing_name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
		$billing_rg = $order->get_meta( '_billing_rg' );
		$billing_cpf = $order->get_meta( '_billing_cpf' );
		
		$acquirer = $this->gateway->get_acquirer();
		$method = Methods::CREDIT_CARD_INTEREST_BY_ISSUER;
		if ( $payment_type == 1 ) { // DEBITO
			$method = Methods::DEBIT_CARD;
		}
		$items = [];
		foreach ($order->get_items() as $item_id => $item_data) {

			// Get an instance of corresponding the WC_Product object
			$product = $item_data->get_product();
			$name = $product->get_name(); // Get the product name
		
			$quantity = $item_data->get_quantity(); // Get the item quantity
		
			$price = $product->get_price(); // Get the item line total
		
			$items[] = array("productName" => $name, "quantity" => $quantity, "price" => $price);
			
		}
		// Set PAYMENT		
		$transaction->Payment()
			->setAcquirer($acquirer)
			->setMethod($method)
			->setCurrency(Currency::BRAZIL_BRAZILIAN_REAL_BRL)
			->setCountry("BRA")
			->setNumberOfPayments($installments)
			->setSoftDescriptor($this->gateway->soft_descriptor)
			->Card()
				->setBrand($card_brand)
				->setCardHolder($credit_card_data['name_on_card'])
				->setCardNumber(preg_replace( '([^0-9])', '', sanitize_text_field( $credit_card_data['card_number'] ) ))
				->setCardSecurityCode($credit_card_data['card_cvv'])
				->setCardExpirationDate($expiry_date);

		$user_id = get_post_meta($order->get_id(), '_customer_user', true);

		//Se não tiver cpf (antifraude desabilitado)
		if (!isset($billing_cpf) || empty($billing_cpf)){
			$billing_cpf = 11111111111;
		}
		
		// SET CUSTOMER
		$transaction->Customer()
			->setCustomerIdentity(strval($user_id))
			->setName($billing_name)	
			->setCpf($billing_cpf)			
			->setEmail($order->get_billing_email())
			->setAddress($order->get_billing_address_1())
			->setAddress2($order->get_billing_address_2())			
			->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
			->setCity($order->get_billing_city())
			->setState($order->get_billing_state())
			->setCountry("BR");		
		

		if ( $this->gateway->antifraud == 'yes' ){
			if (!isset($billing_rg) || empty($billing_rg)){
				$billing_rg = $billing_cpf;
			}
			// SET FRAUD DATA OBJECT
			$transaction->FraudData()
				->setMethod($this->gateway->get_fraud_method())
				->setOperator($this->gateway->get_fraud_operator())
				->setName($billing_name)
				->setDocument($billing_rg)
				->setEmail($order->get_billing_email())
				->setAddress($order->get_billing_address_1())
				->setAddress2($order->get_billing_address_2())
				->setAddressNumber("")
				->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
				->setCity($order->get_billing_city())
				->setState($order->get_billing_state())
				->setCountry("BR")
				->setPhonePrefix("")
				->setPhoneNumber($order->get_billing_phone())
				->setDevice($_SERVER['HTTP_USER_AGENT'])
				->setCostumerIP($_SERVER['REMOTE_ADDR'])
				->setItems($items);
		}

		$order->add_order_note( "Criando transação" );
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, $payment_type . ' - Transaction: ' . print_r( $transaction, true ) );
			$this->gateway->log->add( $this->gateway->id, $this->gateway->get_api_return_url( $order ) );
		}

		// Set URL RETURN
		//if ( $payment_type == 1 ) { // DEBITO
			$transaction->setUrlReturn( get_site_url() . "/wp-json/azpay/v1/azpay_order_return");
		//}
		// PROCESS - ACTION
		if ($this->gateway->capture == 'yes'){

			$this->gateway->log->add( $this->gateway->id, 'method sale - ' . print_r( $transaction, true) );
			$response = $gateway->Sale($transaction);
			update_post_meta($order->get_id(), '_payment_captured', true);			
		}else{
			$this->gateway->log->add( $this->gateway->id, 'method authorize - ' . print_r( $transaction, true) );
			$response = $gateway->Authorize($transaction);
			update_post_meta($order->get_id(), '_payment_captured', false);
			$order->update_status( 'authorized' );
			$order->save();
		}
			
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Response object: ' . print_r( $response->getResponse(), true ) );
		}
		
		// RESULTED
		if ($response->isAuthorized()) { // Action Authorized
			$order->add_order_note( "Transação autorizada" );
			$this->gateway->log->add( $this->gateway->id, 'Authorized. ' . $response->getStatus() );
		} else { // Action Unauthorized	
			$order->add_order_note( "Transação não autorizada. Status: " . $response->getStatus() );			
			$this->gateway->log->add( $this->gateway->id, 'Not authorized. ' . $response->getStatus() );
		}

		// CAPTURE
		
		/*if ($response->canCapture() && $this->gateway->capture) {
			$response = $gateway->Capture($response->getTransactionID());	
			$order->add_order_note( "Capturando transação. " );
			
		}*/
		
		update_post_meta($order->get_id(), '_azpay_tid', $response->getTransactionID());
		
		return $response;
	}

	private function credit_subscription_payment($order, $transaction, $gateway, $payment_type, $installments, 
		$card_brand, $credit_card_data, $expiry_date){

		$billing_name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
		$billing_rg = $order->get_meta( '_billing_rg' );
		$billing_cpf = $order->get_meta( '_billing_cpf' );
		
		$acquirer = $this->gateway->get_acquirer();
		$method = Methods::CREDIT_CARD_INTEREST_BY_ISSUER;
		
		$items = [];
		foreach ($order->get_items() as $item_id => $item_data) {

			// Get an instance of corresponding the WC_Product object
			$product = $item_data->get_product();
			$name = $product->get_name(); // Get the product name
		
			$quantity = $item_data->get_quantity(); // Get the item quantity
		
			$price = $product->get_price(); // Get the item line total
		
			$items[] = array("productName" => $name, "quantity" => $quantity, "price" => $price);
			
		}

		// Set PAYMENT		
		$transaction->Payment()
			->setAcquirer($acquirer)
			->setMethod($method)
			->setCurrency(Currency::BRAZIL_BRAZILIAN_REAL_BRL)
			->setCountry("BRA")
			->setNumberOfPayments($installments)
			->setSoftDescriptor($this->gateway->soft_descriptor)
			->Card()
				->setBrand($card_brand)
				->setCardHolder($credit_card_data['name_on_card'])
				->setCardNumber(preg_replace( '([^0-9])', '', sanitize_text_field( $credit_card_data['card_number'] ) ))
				->setCardSecurityCode($credit_card_data['card_cvv'])
				->setCardExpirationDate($expiry_date);
		
		$user_id = get_post_meta($order->get_id(), '_customer_user', true);
		
		//Se não tiver cpf (antifraude desabilitado)
		if (!isset($billing_cpf) || empty($billing_cpf)){
			$billing_cpf = 11111111111;
		}
		// SET CUSTOMER
		$transaction->Customer()
			->setCustomerIdentity(strval($user_id))
			->setName($billing_name)
			->setCpf($billing_cpf)
			->setEmail($order->get_billing_email())
			->setAddress($order->get_billing_address_1())
			->setAddress2($order->get_billing_address_2())			
			->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
			->setCity($order->get_billing_city())
			->setState($order->get_billing_state())
			->setCountry("BR");

		if ($this->gateway->antifraud == 'yes'){
			// SET FRAUD DATA OBJECT
			$transaction->FraudData()
				->setName($billing_name)
				->setDocument($billing_cpf)
				->setEmail($order->get_billing_email())
				->setAddress($order->get_billing_address_1())
				->setAddress2($order->get_billing_address_2())
				->setAddressNumber("")
				->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
				->setCity($order->get_billing_city())
				->setState($order->get_billing_state())
				->setCountry("BR")
				->setPhonePrefix("")
				->setPhoneNumber($order->get_billing_phone())
				->setDevice($_SERVER['HTTP_USER_AGENT'])
				->setCostumerIP($_SERVER['REMOTE_ADDR'])
				->setItems($items);
		}

		$order->add_order_note( "Criando transação" );
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'An error occurred while requesting the transaction: ' . print_r( $transaction, true ) );
		}

		// Set URL RETURN
		$transaction->setUrlReturn( get_site_url() . "/wp-json/azpay/v1/azpay_order_return");

		// PROCESS - ACTION
		#$response = $gateway->sale($transaction);
		$response = $gateway->rebill($transaction);

		// REDIRECT IF NECESSARY (Debit uses)
		if ($response->isRedirect()) {
			$response->redirect();
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'An error occurred while requesting the transaction: ' . print_r( $response, true ) );
		}
		
		// RESULTED
		if ($response->isAuthorized()) { // Action Authorized
			$order->add_order_note( "Transação autorizada" );
			$this->gateway->log->add( $this->gateway->id, 'Authorized. ' . $response->getStatus() );
		} else { // Action Unauthorized	
			$order->add_order_note( "Transação não autorizada. Status: " . $response->getStatus() );			
			$this->gateway->log->add( $this->gateway->id, 'Not authorized. ' . $response->getStatus() );
		}

		// CAPTURE
		update_post_meta($order->get_id(), '_payment_captured', false);
		if ($response->canCapture() && $this->gateway->capture) {
			$response = $gateway->Capture($response->getTransactionID());	
			$order->add_order_note( "Capturando transação. " );
			update_post_meta($order->get_id(), '_payment_captured', true);
		}
		
		update_post_meta($order->get_id(), '_azpay_tid', $response->getTransactionID());

		//Registrar pagamentos agendados
		$payments = $response->getResponse()['processor']['payments'];
		foreach ($payments as $payment){	
			$status = $payment['payment']['status'];
			if ($status == 8){
				$order->payment_complete();
				$order->update_status( 'processing' );
			}else if ($status == 2 || 4 == $status  ){
				$order->update_status( 'failed' );
			}	
			$this->register_payment_db($response->getTransactionID(), $payment['payment']);
		}

		// REPORT
		$response = $gateway->Report($response->getTransactionID());
		return $response;
	}

	private function slip_payment($order, $transaction, $gateway, $payment_type){

		$billing_name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
		$billing_rg = $order->get_meta( '_billing_rg' );
		$billing_cpf = $order->get_meta( '_billing_cpf' );
		
		$acquirer = $this->gateway->get_acquirer();
		
		// Set PAYMENT		
		$transaction->Payment()
			->setAcquirer($acquirer)			
			->setCurrency(Currency::BRAZIL_BRAZILIAN_REAL_BRL)
			->setCountry("BRA")
			->setExpire(date('Y-m-d', strtotime("+".$this->gateway->slip_expire." days")))
			->setNrDocument(rand(1, 1000000))
			->setInstructions($this->gateway->instructions);
			
		$user_id = get_post_meta($order->get_id(), '_customer_user', true);

		// SET CUSTOMER
		$transaction->Customer()
			->setCustomerIdentity(strval($user_id))
			->setName($billing_name)
			->setCpf($billing_cpf)
			->setEmail($order->get_billing_email())
			->setAddress($order->get_billing_address_1())
			->setAddress2($order->get_billing_address_2())			
			->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
			->setCity($order->get_billing_city())
			->setState($order->get_billing_state())
			->setCountry("BR");
		
		// Set URL RETURN
		$transaction->setUrlReturn( get_site_url() . "/wp-json/azpay/v1/azpay_order_return");

		$order->add_order_note( "Criando transação" );

		// PROCESS - ACTION
		#$response = $gateway->sale($transaction);
		$response = $gateway->Boleto($transaction);

		// REDIRECT IF NECESSARY (Debit uses)
		
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Retorno da transação: ' . print_r( $response, true ) );
		}
		
					
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Response transação: ' . print_r( $response->getResponse(), true ) );
		}
		
		$nrDocumento = $response->getResponse()['processor']['Boleto']['details']['nrDocument'];
		
		$order->add_order_note( "Boleto criado. Aguardando pagamento. nrDocumento: $nrDocumento" );
		update_post_meta($order->get_id(), '_payment_captured', false);
		update_post_meta($order->get_id(), '_azpay_tid', $response->getTransactionID());
		update_post_meta($order->get_id(), '_slip_ndoc', $nrDocumento);
		if (isset($response->getResponse()['processor']['Boleto']['details']['urlBoleto']))
			update_post_meta($order->get_id(), '_slip_url', $response->getResponse()['processor']['Boleto']['details']['urlBoleto']);
		else if(isset($response->getResponse()['processor']['urlBoleto'])){
			update_post_meta($order->get_id(), '_slip_url', $response->getResponse()['processor']['urlBoleto']);
		}
		
		$responseReport = $gateway->Report($response->getTransactionID());


		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Response report transação: ' . print_r( $responseReport, true ) );
		}		

		return $response;
	}

	private function transfer_payment($order, $transaction, $gateway, $payment_type){

		$billing_name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
		$billing_rg = $order->get_meta( '_billing_rg' );
		$billing_cpf = $order->get_meta( '_billing_cpf' );
		
		$acquirer = $this->gateway->get_acquirer();
		
		// Set PAYMENT		
		$transaction->Payment()
			->setAcquirer($acquirer);			

		$user_id = get_post_meta($order->get_id(), '_customer_user', true);
		// SET CUSTOMER
		$transaction->Customer()
			->setCustomerIdentity(strval($user_id))
			->setName($billing_name)
			->setCpf($billing_cpf)
			->setEmail($order->get_billing_email())
			->setAddress($order->get_billing_address_1())
			->setAddress2($order->get_billing_address_2())			
			->setPostalCode(preg_replace('/[^0-9]/', '', $order->get_billing_postcode()))
			->setCity($order->get_billing_city())
			->setState($order->get_billing_state())
			->setCountry("BR");
		
		// Set URL RETURN
		$transaction->setUrlReturn( get_site_url() . "/wp-json/azpay/v1/azpay_order_return");

		$order->add_order_note( "Criando transação" );

		// PROCESS - ACTION
		#$response = $gateway->sale($transaction);
		$response = $gateway->OnlineTransfer($transaction);

		// REDIRECT IF NECESSARY (Debit uses)
		
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Transação: ' . print_r( $transaction, true ) );
			$this->gateway->log->add( $this->gateway->id, 'Retorno da transação: ' . print_r( $response, true ) );
		}
		
		// RESULTED
		if ($response->isAuthorized()) { // Action Authorized
			$order->add_order_note( "Transação autorizada" );
			$this->gateway->log->add( $this->gateway->id, 'Authorized. ' . $response->getStatus() );
		} else { // Action Unauthorized				
			$order->add_order_note( "Transação não autorizada. Status: " . $response->getStatus() );			
			$this->gateway->log->add( $this->gateway->id, 'Not authorized. ' . $response->getStatus() );
		}		
		
		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Response transação: ' . print_r( $response->getResponse(), true ) );
		}
						
		update_post_meta($order->get_id(), '_payment_captured', false);
		update_post_meta($order->get_id(), '_azpay_tid', $response->getTransactionID());		
		update_post_meta($order->get_id(), '_transfer_url', $response->getResponse()['processor']['Transfer']['urlTransfer']);
		
		$responseReport = $gateway->Report($response->getTransactionID());

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Response report transação: ' . print_r( $responseReport, true ) );
		}
		

		return $response;
	}

	/**
	 * Get transaction data.
	 *
	 * @param  WC_Order $order Order data.
	 * @param  string   $tid     Transaction ID.
	 * @param  string   $id      Request ID.
	 *
	 * @return SimpleXmlElement|StdClass Transaction data.
	 */
	public function get_transaction_data( $order, $tid, $id ) {
		if ( 'production' == $this->gateway->environment ) {
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::PRODUCTION);
		}else
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::SANDBOX);
				
		
		$gateway = new Gateway($credential);

		$response_data = $gateway->Report($tid);
		

		return $response_data;
	}

	/**
	 * Do transaction cancellation.
	 *
	 * @param  WC_Order $order Order data.
	 * @param  string   $tid     Transaction ID.
	 * @param  string   $id      Request ID.
	 * @param  float    $amount  Amount for refund.
	 *
	 * @return array
	 */
	public function do_transaction_cancellation( $order, $tid, $id, $amount = 0 ) {
		if ( 'production' == $this->gateway->environment ) {
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::PRODUCTION);
		}else
			$credential = new Credential($this->gateway->merchant_id, $this->gateway->merchant_key, Environment::SANDBOX);
				
		
		$gateway = new Gateway($credential);
		
		$response_data = $gateway->Cancel($tid, $amount * 100);
		
		
		return $response_data;
	}
}
