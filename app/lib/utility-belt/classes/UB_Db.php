<?php
/**
 * Utility Belt Db
 * ---------------
 * Piggybacks on ezSQL_mysql by Justin Vincent (jv@jvmultimedia.com).
 * http://twitter.com/justinvincent
 */

require_once UB_PATH . UB_DS . 'ez_sql_core.php';
require_once UB_PATH . UB_DS . 'ez_sql_mysql.php';

class UB_Db {
	
	private static $_instance;
	
	public function getInstance() {
		if ( is_null(self::$_instance) ) {
			self::$_instance = new ezSQL_mysql(DB_USER, DB_PASS, DB_NAME, DB_HOST);
		}
		return self::$_instance;
	}
	
}