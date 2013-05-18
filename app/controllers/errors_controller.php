<?php
class Errors_Controller extends UB_Controller {

	public function index( $code, $exception ) {
		$this->view->errors = array($exception->getMessage());
		echo $this->render('/index/error');
//		UB::dump($exception);
	}

}
