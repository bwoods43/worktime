<?php
class UB_Controller {

	public $template = 'default';
	public $request;
	public $view;
	private $_viewRootPath;
	
	public function __construct()
	{
		$this->request = UB_Request::getInstance();
		$this->view = new UB_View();
		$this->view->setPath(VIEW_PATH);
		$this->view->template_path = TEMPLATE_PATH . UB_DS . $this->template;
	}

	public function __call( $function, $params )
	{
		throw new Exception("'$function' is an invalid action.");
	}

	public function render( $script = null ) {
		return $this->view->render($script);
	}
	
}
