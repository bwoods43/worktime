<?php
/**
 * Utility Belt PHP Framework
 * @version 1.5
 * @author Andy Stratton <hello@theandystratton.com>
 */

// Path definitions
define('UB_DS', DIRECTORY_SEPARATOR );
define('UB_PATH', dirname(__FILE__) );
if ( !defined('APP_PATH') ) define('APP_PATH', str_replace(UB_DS . 'lib' . UB_DS . 'utility-belt', '', UB_PATH) );
define('CONTROLLER_PATH', APP_PATH . UB_DS . 'controllers');
define('MODEL_PATH', APP_PATH . UB_DS . 'models');
define('VIEW_PATH', APP_PATH . UB_DS . 'views');
define('TEMPLATE_PATH', APP_PATH . UB_DS . 'templates');
define('LIB_PATH', APP_PATH . UB_DS . 'lib');
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

class UB
{
	/**
	 * Readable variable dump -- a la Zend::dump()
	 * 
	 */
	public static function dump( $var, $label = null, $echo = true )
	{
		if (!empty($label)) echo '<strong>'. $label . '</strong>';
		ob_start();
		var_dump( $var );
		$contents = ob_get_contents();
		ob_end_clean();
		if ($echo) echo '<pre>'. $contents . '</pre>';
		return $contents;
	}
	
	/**
	 * Add an action to a hook.
	 *  
	 * @string $action
	 * @mixed $function (string or array with object)
	 */
	public static function add_action( $action, $function ) {
		$reg = UB_Registry::getInstance();
		if ( !is_array($reg->UB_actions) ) $reg->UB_actions = array();
		if ( !is_array($reg->UB_actions[$action]) ) $reg->UB_actions[$action] = array();
		$reg->UB_actions[$action][] = $function;
	}
	
	/**
	 * Run all functions from an action
	 * 
	 * @string $action
	 */
	public static function run_actions( $action ) {
		$reg = UB_Registry::getInstance();
		if ( is_array($reg->UB_actions[$action]) && count($reg->UB_actions[$action]) > 0 ) {
			foreach ( $reg->UB_actions[$action] as $func ) {
				call_user_func($func);
			}
		}	
	}
	
}

class UB_Exception extends Exception { }	
	
/**
 * Autoload objects. If you'd like a customized autoloading function, 
 * create it and name it UB_autoload()
 *
 */
function __autoload( $class )
{
	if ( substr($class, 0, 3) === 'UB_' ) {
		$classFile = UB_PATH . UB_DS . 'classes' . UB_DS . $class . '.php';
		require_once $classFile;
	}
	else {
		$paths = array(CONTROLLER_PATH, MODEL_PATH, LIB_PATH);
		$class = $class;
		foreach ( $paths as $path ) {
			$the_path = $path . UB_DS . $class . '.php';
			$lower_path = $path . UB_DS . strtolower($class) . '.php';
			if ( file_exists($the_path)) {
				require_once $the_path;
				break;
			}
			if ( file_exists($lower_path)) {
				require_once $lower_path;
				break;
			}
		}
	}
	if ( !class_exists($class) ) {
		if ( function_exists("UB_autoload") ) {
			UB_autoload($class);
		}
		else {
			throw new Exception('The requested file does not exist.', 404);
			exit;
		}
	}
}

?>