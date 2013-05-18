<?php
/**
 * Utility Belt Request
 * --------------------
 * Manages items within the HTTP request. May include getting/setting HTTP headers, 
 * and redirects.
 * 
 * @author Andy Stratton <theandystratton@gmail.com
 *
 */
class UB_Request
{

	const	HTTP_301 = 'HTTP/1.1 301 Moved Permanently',
			HTTP_302 = 'HTTP/1.1 302 Moved Temporarily',
			HTTP_303 = 'HTTP/1.1 303 See Other',
			HTTP_404 = 'HTTP/1.1 404 Not Found';

	public static $singleton;

	private $_params,
			$_uri,
			$_pieces;

	private function __construct( $uri = null ) {
		if (is_null($uri)) $uri = $_SERVER['REQUEST_URI'];
		$uri = explode('?', $uri);
		$this->_uri = ltrim($uri[0], '/');
		$this->_uri = rtrim($this->_uri, '/');
		$this->_pieces = explode('/', $this->_uri);
		// make associative array of any parameter/value pairs.
		if (count($this->_pieces) > 2) {
			for ($i = 2; $i <= count($this->_pieces); $i = $i +2) {
				$this->_params[$this->_pieces[$i]] = $this->_pieces[$i+1];
			}
		}
	}

	public static function getInstance() {
		if (is_null(self::$singleton)) {
			self::$singleton = new UB_Request();
		}
		return self::$singleton;
	}

	public function getController() {
		return empty($this->_pieces[0]) ? 'index' : $this->_pieces[0];
	}

	public function getAction() {
		return empty($this->_pieces[1]) ? "index" : $this->_pieces[1];
	}

	public function getPiece( $index ) {
		return $this->_pieces[$index];
	}

	public function getParam( $name ) {
		return $this->_params[$name];
	}

	public function getURI() {
		return $this->_uri;
	}

	public function setController( $value ) {
		$this->_pieces[0] = $value;
	}

	public function setAction( $value ) {
		$this->_pieces[1] = $value;
	}

	public function setParam( $name, $value ) {
		$this->_params[$name] = $value;
	}

	public function forward( $uri ) {
		$this->__construct($uri);
	}


	public function redirect( $uri, $code = null ) {
		if (!headers_sent()) {
			if (!is_null($code)) header( $code );
			header("Location: $uri");
		} else throw new UB_Exception("Headers have already been sent, cannot redirect to $uri!");
	}
}
