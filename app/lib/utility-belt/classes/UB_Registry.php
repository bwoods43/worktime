<?php

class UB_Registry
{
	public static $instance;
	private $_contents;

	private function __construct()
	{
		$this->_contents = array();
	}

	public static function getInstance()
	{
		if ( is_null(self::$instance) ) {
			self::$instance = new UB_Registry();
		}
		return self::$instance;
	}

	public function __set( $param, $value )
	{
		$this->_contents[$param] = $value;
	}

	public function __get( $param )
	{
		return $this->_contents[$param];
	}

}