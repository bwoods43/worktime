<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/common.js"></script>

<?php
$this->request = UB_Request::getInstance(); 
$file = $this->request->getController() . '-' . $this->request->getAction() . '.js';
if ( file_exists(DOC_ROOT . DIRECTORY_SEPARATOR . $file) ) : 
?>
<script type="text/javascript" src="/<?php echo htmlentities($file); ?>"></script>
<?php endif; ?>