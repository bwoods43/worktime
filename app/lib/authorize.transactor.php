<?php
class Authorize_Transactor {
	
	public static $production_url = 'https://secure.authorize.net/gateway/transact.dll';
	public static $test_url = 'https://test.authorize.net/gateway/transact.dll';

	public static $prod_arb_url = 'https://api.authorize.net/xml/v1/request.api';
	public static $test_arb_url = 'https://apitest.authorize.net/xml/v1/request.api';
	
	public static $card_types = array(
		'Visa' => 'VISA',
		'Mastercard' => 'MC',
		'Discover' => 'DISCOVER',
		'American Express' => 'AMEX'
	);
	
	public $use_test = true;
	
	public $raw_response = null;
	public $response = null;
	public $params = array();
	
	public function __construct( $is_production = false ) {
		if ( $is_production ) {
			$this->use_test = false;
		}
	}
	
	public function setField( $var, $val ) {
		$this->params[$var] = $val;
	}
	
	public function setFields( $array ) {
		$this->params = $array;
	}
	
	public function updateFields( $array ) {
		foreach ( $array as $k => $v ) {
			$this->params[$k] = $v;
		}
	}
	
	public function process() {
		$gateway_uri = $this->use_test ? self::$test_url : self::$production_url;
		
		$post_string = '';
		foreach ( $this->params as $key => $value ) { 
			$post_string .= $key . '=' . urlencode($value) . '&'; 
		}
		$post_string = substr($post_string, 0, -1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $gateway_uri);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$this->raw_response = curl_exec($ch);
		$this->response = explode($this->params['x_delim_char'], $this->raw_response);
		curl_close($ch);
		return $this->raw_response;
	}
	
	public function isSuccess() {
		$success = true;
		// not approved?
		if ( $this->getResponseCode() != '1' ) {
			$success = false;
		}
		return $success;
	}
	
	public function getErrorText() {
		// coming soon
	}

	// getters for basic response fields
	public function getResponseCode() {
		return $this->response[0];
	}
	public function getResponseReason() {
		return $this->response[3];
	}
	public function getAuthorizationCode() {
		return $this->response[4];
	}
	public function getAVSResponse() {
		return $this->response[5];
	}
	public function getTransactionID() {
		return $this->response[6];
	}
	public function getCustomerID() {
		return $this->response[13];
	}
	public function getTaxExemptStatus() {
		return $this->response[35];
	}
	public function getCCVResponse() {
		return $this->response[35];
	}

	
	// static functions
	public static function isValidCard( $type, $number ) {
		$valid = false;
		switch ( $type ) {
			case 'VISA':
				$valid = self::isVisa($number);
				break;
			case 'MC':
				$valid = self::isMasterCard($number);
				break;
			case 'DISCOVER':
				$valid = self::isDiscover($number);
				break;
			case 'AMEX':
				$valid = self::isAmex($number);
				break;
		}
		return $valid && self::isMod10($number);
	}
	
	public static function isMod10( $number ) {
		$number = strrev($number);
		$sum = 0;
		for ( $i = 0; $i < strlen($number); $i++ ) {
			$current = substr($number, $i, 1);
			if ( $i % 2 == 1 ) {
				$current *= 2;
			}
			if ( $current > 9 ) {
				$first = $current % 10;
				$second = ($current - $first) / 10;
				$current = $first + $second;
			}
			$sum += $current;
		}
		return $sum % 10 == 0;
	}
	
	public static function isMasterCard( $number ) {
		$number = preg_replace('/[^\\d]/i', '', $number);
		return (preg_match('/^5[1-5]/', $number)) && strlen($number) == 16;		
	}
	
	public static function isVisa( $number ) {
		$number = preg_replace('/[^\\d]/i', '', $number);
		return substr($number, 0, 1) == '4' && (strlen($number) == 16 || strlen($number) == 13);
	}
	
	public static function isDiscover( $number ) {
		$number = preg_replace('/[^\\d]/i', '', $number);
		return substr($number, 0, 4) == '6011' && strlen($number) == 16;
	}
	
	public static function isAmex( $number ) {
		$number = preg_replace('/[^\\d]/i', '', $number);
		return (substr($number, 0, 2) == '34' || substr($number, 0, 2) == '37') && strlen($number) == 15;
	}
	
	public static function doArbRequest( $request, $is_production = true ) {
		// set URL based on testing preferences
		if ( !$is_production ) $url = self::$test_arb_url;
		else $url = self::$prod_arb_url;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSLVERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		$response = curl_exec($ch);
		curl_close($ch);
		$xml = @simplexml_load_string($response);
		return $xml;
	}
	
}