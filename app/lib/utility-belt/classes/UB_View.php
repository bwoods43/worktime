<?php
/**
 * Utility Belt View
 * -----------------
 * Handles the display of basic PHP view scripts.
 * 
 * @author Andy Stratton <theandystratton@gmail.com>
 * 
 */
class UB_View
{
	public $template_path;
	
	private	$_data;
	private $_scriptPath;
	private $_escapeType;
	
	public function __construct() {
		$this->request = UB_Request::getInstance();
	}
	
	public function setPath($path) {
		if ( is_dir($path) ) $this->_scriptPath = rtrim($path, UB_DS);
		else throw new UB_Exception("You must specifiy a valid directory to set a script path.");
	}
	
	/**
	 * Clears data from the view object.
	 * 
	 */
	public function clear() { $this->_data = array(); }

	/**
	 * Assigns variables to the object. If $var is an associative array, it stores the
	 * keys as variable names and the values as their values.
	 *
	 * @param string|array $var
	 * @param string $value
	 */
	public function assign( $var, $value = null ) {
		if (is_string($var)) {
			if (substr($var, 0, 1) == '_') throw new UB_Exception("You cannot set a private variable!");
			$this->$var = $value;
		}
		elseif (is_array($var)) {
			foreach ($var as $name => $value)
			{
				if (substr($var, 0, 1) == '_') throw new UB_Exception("You cannot set a private variable!");			
				$this->$name = $value;
			}
		}
		else {
			throw new UB_Exception("First parameter must be an array or string.");
		}
	}
	
	public function setEscape( $type ) {
		if ( function_exists($type) || is_null($type) ) $this->_escapeType = $type;
	}
	
	public function escape( $val ) {
		if ( is_null($this->_escapeType) ) return htmlspecialchars( $val );
		else return call_user_func($this->_escapeType, $val);
	}
	
	public function getPath() { return $this->_scriptPath; }
	
	public function render( $file ) {
		if ( substr($file, -4) != '.php' ) $file .= '.php';
		$file = VIEW_PATH . $file;
		if ( !is_file($file) ) throw new UB_Exception("You must specify a valid script path and filename to render a view. $file is invalid.");
		ob_start();
		$this->_render($file);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	protected function _render($file) { include $file; }
	
	public function get( $template_script, $return = false ) {
		if ( substr($template_script, -4) != '.php' ) $template_script .= '.php';
		$file = $this->template_path . UB_DS . $template_script;
		if ( file_exists($file) ) {
			ob_start();
			$this->_render($file);
			$contents = ob_get_contents();
			ob_end_clean();
			if ( $return ) return $contents;
			else echo $contents;
		}
	}
	
	public function clean( $param ) {
		return UB_Text::stringFromPost($param);
	}
	
}

