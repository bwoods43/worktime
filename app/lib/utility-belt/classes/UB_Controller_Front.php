<?php

class UB_Controller_Front {
	
	public static $instance = null;
	
	private function __construct() {
		
	}

	//
	// TODO: ADD Backreferencing support.
	//
	public static function run( $routes = null ) {
		$request = UB_Request::getInstance();
		$action = $request->getAction();
		$controller = ucwords($request->getController()) . '_Controller';
		if ( is_array($routes) && count($routes) > 0 ) {
			// handle basic routing stuff.
			foreach ( $routes as $pattern => $params ) {
				if ( preg_match( '/' . $pattern . '/', $request->getURI()) ) {
					$controller = $params[0] ? ucwords($params[0]) : 'Index';
					$controller .= '_Controller';
					$action = $params[1] ? $params[1] : 'index'; 
					break; // exit the loop if we find a match.
				}
			}
		}
		if ( class_exists($controller) ) {
			$controller = new $controller();
		}
		else {
			$controller = 'Error_Controller';
			$action = 'index';
		} 
		call_user_func( array($controller, $action) );
	}
	
}
