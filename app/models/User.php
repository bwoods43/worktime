<?php
class User extends UB_Model {
	
	public $_table = 'users';
	
	const TYPE_ADMIN = 'admin';
	const TYPE_MANAGER = 'manager';
	// 7/3/10 baw - add foreman type and combined employee_foreman type
	const TYPE_FOREMAN = 'foreman';
	const TYPE_EMPLOYEE = 'employee';
	const TYPE_EMPLOYEE_FOREMAN = 'employee_foreman';

	static $current_user;
	
	public function __construct( $ID = null ) {
		$this->setup();
		if ( !is_null($ID) ) {
			if ( !is_numeric($ID) ) {
				$ID = $this->_db->escape($ID);
				$the_ID = $this->_db->get_var("SELECT ID FROM {$this->_table} WHERE email = '$ID';");
				if ( $the_ID ) $this->load($the_ID);
			}
			else { 
				$this->load($ID);
			}
		}
	}
	
	public function setPassword( $passwd ) {
		$this->passwd = self::encryptPassword($passwd);
	}
	
	public function login( $passwd ) {
		if ( $this->passwd == self::encryptPassword($passwd) ) {
			$_SESSION['beynon_login'] = true;
			$_SESSION['beynon_id'] = $this->ID;
			$_SESSION['beynon_office_id'] = self::getOfficeID();
			return true;
		}
		else {
			return false;
		}
	}
	
	public function emailExists( $email ) {
		$ID_excluder = '';
		if ( $this->ID ) {
			$ID_excluder = " AND ID NOT IN ({$this->ID}) ";
		}
		$email = $this->_db->escape($email);
		return $this->_db->get_var("SELECT email FROM {$this->_table} WHERE email LIKE '{$email}'" . $ID_excluder);
	}
	
	public function notify( $password ) {
		$message = Swift_Message::newInstance();
		$message->setContentType('text/html');
		$message->setFrom('admin@benyontime.com')->setSender('admin@benyontime.com')->setTo( array($this->email) );
		$message->setSubject('Your New Account.');
		$view = new UB_View();
		$view->password = $password;
		$view->user = $this;
		$message->setBody($view->render('/admin/users/helpers/notification'));
		$mailer = Swift_MailTransport::newInstance();
		return $mailer->send( $message );
	}
	
	public static function encryptPassword( $passwd ) {
		return substr(md5('salted' . $passwd . 'detlas'), 0, 32);
	}

	public static function isLoggedIn() {
		return $_SESSION['beynon_login'] === true;
	}
	
	public static function logout() {
		$_SESSION['beynon_login'] = null;
		$_SESSION['beynon_id'] = null;
		$_SESSION['beynon_office_id'] = null;
		unset($_SESSION['beynon_login']);
	}

	public static function getCurrentUser() {
		if ( is_null(self::$current_user) ) {
			self::$current_user = new User($_SESSION['beynon_id']);
		}
		return self::$current_user;
	}
	
	public static function getAccountType() {
		return self::getCurrentUser()->account_type;
	}
	
	public static function getUsers($params = null) {
		$start = 0;
		$limit = 20;
		extract($params);
		$db = UB_Db::getInstance();
		// 2/26/11 baw - always need office id
		$sql = "SELECT * FROM users WHERE 1=1 AND office_id = " . $_SESSION['beynon_office_id'];
		if ( !is_null($ID) ) {
			$sql .= " AND ID IN ('" . $db->escape($ID) . "') ";
		}
		if ( !is_null($exclude) ) {
			$sql .= " AND ID NOT IN (" . $db->escape($exclude) . ") ";
		}
		if ( !is_null($email) ) {
			$sql .= " AND email = '" . $db->escape($email) . "' ";
		}
		if ( !is_null($hidden) ) {
			$sql .= " AND hidden = '" . $db->escape($hidden) . "' ";
		}
		if ( !is_null($account_type) ) {
			// 7/13/10 baw account for showing employees and foremen in pulldowns
			if ($account_type == 'employee_foreman') {
				$sql .= " AND (account_type = 'employee' OR account_type = 'foreman') ";
			} else {
				$sql .= " AND account_type = '" . $db->escape($account_type) . "' ";			
			}
		}
		$start = intval($start);
		$limit = intval($limit);
		$sql .= " ORDER BY last_name ASC, first_name ASC LIMIT {$start}, {$limit};";
		
		return $db->get_results($sql);
	}

	public static function getAccountTypes() {
		return array(
			'Employee' => self::TYPE_EMPLOYEE,
			// 7/3/10 baw - add foreman type
			'Foreman' => self::TYPE_FOREMAN,
			'Manager' => self::TYPE_MANAGER,
			'Administrator' => self::TYPE_ADMIN
		);
	}

	public static function getTotalUsers( $exclude_me = false ) {
		$db = UB_Db::getInstance();
		return $exclude_me ? $db->get_var("SELECT COUNT(*) FROM users WHERE office_id = " . $_SESSION['beynon_office_id'] . " AND ID NOT IN (" . self::getCurrentUser()->ID . ")") : $db->get_var("SELECT COUNT(*) FROM users WHERE office_id = " . $_SESSION['beynon_office_id']);
	}

	public function delete() {
		$ID = $this->_db->escape($this->{$this->_primary_key});
		// kill project link
		$this->_db->query("DELETE FROM projects_users WHERE user_id = '$ID';");
		$this->_db->query("UPDATE projects SET manager_id = NULL WHERE manager_id = '$ID';");
		// kill time entries for this task
		$this->_db->query("DELETE FROM project_time WHERE user_id = '$ID';");
		// kill task
		$this->_db->query("DELETE FROM {$this->_table} WHERE {$this->_primary_key} = '$ID';");
	}
	
	public static function isAdmin() {
		return self::getCurrentUser()->account_type == self::TYPE_ADMIN;
	}
	
	// 2/26/11 baw - find office for user
	public function getOfficeID($office_id=null) {
		if (!$office_id) {
			$db = UB_Db::getInstance();
			return $db->get_var("SELECT office_id FROM {$this->_table} WHERE ID = '$this->ID' LIMIT 1;");
		} else {
			return $office_id;
		}
	}	

	// 2/26/11 baw - find office name
	public function getOfficeName($office_id) {
		$db = UB_Db::getInstance();
		return $db->get_var("SELECT office_name FROM offices WHERE ID = '$office_id' LIMIT 1;");
	}	
}