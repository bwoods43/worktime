<?php
class Index_Controller extends UB_Controller {
	
	public function index() {
		if ( !User::isLoggedIn() ) {
			$this->request->redirect('/login', UB_Request::HTTP_303);
			exit;
		}
		switch ( User::getAccountType() ) {
			case User::TYPE_ADMIN:
				$this->request->redirect('/admin');
				break;
			case User::TYPE_MANAGER:
				$this->request->redirect('/managers');
				break;
			default:
				echo $this->render('/index/coming_soon');
				exit;
				break;
		}
	}

	public function login() {
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->view->errors = array();
			if ( !UB_Text::isEmail($this->view->clean['email']) ) {
				$this->view->errors[] = 'Enter a valid email address.';
			}
			else {
				if ( !empty($this->view->clean['passwd']) ) {
					$user = new User($this->view->clean['email']);
					if ( $user->login($this->view->clean['passwd']) ) {
						$this->request->redirect('/', UB_Request::HTTP_303);
						exit;
					}
					else {
						$this->view->errors[] = 'Invalid email/password combination.';
					}
				}
				else {
					$this->view->errors[] = 'Password was left blank.';
				}
			}
			
		}
		if ( $_GET['logout'] ) $this->view->success = 'You have been logged out';
		echo $this->render('/index/login');
	}

	public function logout() {
		User::logout();
		$this->request->redirect('/login?logout=1', UB_Request::HTTP_303);
		exit;
	}
	
	public function forgot() {
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array('email'));
			if ( !UB_Text::isEmail($this->view->clean['email']) ) {
				$this->view->errors[] = 'Enter a valid email address.';
			}
			else {
				$user = new User($this->view->clean['email']);
				if ( $user->email ) {
					$passwd = substr(User::encryptPassword(rand(2344, time()) . 'salty' . rand(1,30)), 0, 10);
					$user->setPassword( $passwd );
					$message = Swift_Message::newInstance();
					$message->setContentType('text/html');
					$message->setFrom('admin@benyontime.com')->setSender('admin@benyontime.com')->setTo( array($user->email) );
					$message->setSubject('Your Account.');
					$message->setBody("Your password has been changed to $passwd");
					$mailer = Swift_MailTransport::newInstance();
					if ( $mailer->send( $message ) ) {
						$user->save();
						$this->view->success = 'Your password has been changed. Check your email for your new password and <a href="/login">return to the login screen</a>.';
					}
					else {
						$this->view->errors[] = 'Could not send your password at this time.';
					}
				}
				else {
					$this->view->errors[] = 'This email address was not found.';
				}
			}
		}
		echo $this->render('/index/forgot');
	}
	
}
