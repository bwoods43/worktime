<?php // use local files if not connected to the internet
if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	print '<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>';
} else {
	print '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>';
}
?>
<script type="text/javascript" src="/common.js"></script>

<?php
$this->request = UB_Request::getInstance(); 
$file = $this->request->getController() . '-' . $this->request->getAction() . '.js';
if ( file_exists(DOC_ROOT . DIRECTORY_SEPARATOR . $file) ) : 
?>
<script type="text/javascript" src="/<?php echo htmlentities($file); ?>"></script>
<?php endif; ?>