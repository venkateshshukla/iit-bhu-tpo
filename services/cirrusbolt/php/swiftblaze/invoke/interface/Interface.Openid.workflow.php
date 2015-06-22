<?php 
require_once(SBSERVICE);
require_once(LIGHTOPENID);

/**
 *	@class InterfaceOpenidWorkflow
 *	@desc Processes interface openid authentication
 *
 *	@param openid_identifier string OpenID Identifier to be authenticated [memory] optional default false
 *	@param continue string URL to continue [memory] optional default false
 *
 *	@param session array Session instance configuration key [memory] ('key', 'expiry', 'root', 'context')
 *
 *	@return user string Username [memory]
 *	@return key string Cookie name [memory]
 *	@return value string Session ID [memory]
 *	@return expiry string Cookie expiry [memory]
 *	@return continue string URL to continue [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
**/
class InterfaceOpenidWorkflow implements Service {

	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('openid_identifier' => false, 'continue' => false)
		);
	}

	/**
	 *	@interface Service
	**/
	public function run_old($memory){
		$session = Snowblozm::get('session');
		$openid = new LightOpenID($_SERVER['HTTP_HOST']);

		try {
			if(!$openid->mode && $memory['openid_identifier']){
				$openid->identity = $memory['openid_identifier'];
				$openid->required = array('contact/email', 'namePerson/first', 'namePerson/last');
				header('Location: ' . $openid->authUrl());
				exit;
			}
			elseif($openid->mode == 'cancel'){
				$memory['valid'] = false;
				$memory['msg'] = 'Authentication Cancelled';
				$memory['status'] = 407;
				$memory['details'] = "User cancelled authentication @interface.openid";
				return $memory;
			}
			elseif($openid->validate()) {
				$attr = $openid->getAttributes();

				if(!isset($attr['contact/email'])){
					$memory['valid'] = false;
					$memory['msg'] = 'Invalid OpenID';
					$memory['status'] = 500;
					$memory['details'] = "OpenID Provider did not provide contact/email information @interface.openid";
					return $memory;
				}

				$memory['email'] = $attr['contact/email'];

				$workflow = array(
				array(
					'service' => 'guard.openid.find.workflow'
				),
				array(
					'service' => 'guard.key.info.workflow'
				));

				$memory = Snowblozm::execute($workflow, $memory);
				$register = false;

				if(!$memory['valid']){
					$memory['valid'] = $register = true;
					$session['expiry'] = 1;
					$memory['user'] = $memory['email'];
				}

				$workflow = array(
				array(
					'service' => 'cbcore.session.add.workflow',
					'expiry' => $session['expiry'],
					'output' => array('sessionid' => 'value')
				));

				$memory = Snowblozm::execute($workflow, $memory);

				if(!$memory['valid'])
					return $memory;

				//header('Location: '. isset($memory['continue']) ? $memory['continue'] : $session['root']);
				setcookie($session['key'], $memory['value'], $register ? 0 : time()+60*60*24*$session['expiry'], '/');
			}
			else {
				$memory['valid'] = false;
				$memory['msg'] = 'Authentication Failed';
				$memory['status'] = 407;
				$memory['details'] = "User authentication failed @interface.openid";
				return $memory;
			}
		}
		catch(ErrorException $e){
			$memory['valid'] = false;
			$memory['msg'] = 'Authentication Failure';
			$memory['status'] = 407;
			$memory['details'] = "Exception occurred : ".$e->getMessage()." @interface.openid";
			return $memory;
		}

		$memory['key'] = $session['key'];
		$memory['expires'] = $session['expiry'];
		$memory['continue'] = $memory['continue'] ? $memory['continue'] : $session['root'];

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Interface Session';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}

	/*
	 * Run using the new openid connect
	 */
	public function run($memory){
		$session = Snowblozm::get('session');

		set_include_path(get_include_path() . PATH_SEPARATOR . EXROOT . 'dev/libraries/google-api-php-client/src');
		require_once('Google/autoload.php');

		$client_id = '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com';
		$client_secret = 'f6KsTE5nK_TKzE7HtqKeA3HY';
		$redirect_uri = '/auth.php';

		$client = new Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->setScopes('email');

		$token = $memory['idtoken'];

		try {
			$ticket = $client->verifyIdToken($token);
			if ($ticket) {
				$data = $ticket->getAttributes();

				if (!isset($data['email'])) {
					$memory['valid'] = false;
					$memory['msg'] = 'Invalid OpenID';
					$memory['status'] = 500;
					$memory['details'] = "OpenID Provider did not provide contact/email information @interface.openid";
					return $memory;
				}

				$memory['email'] = $data['email'];

				$workflow = array(
				array(
					'service' => 'guard.openid.find.workflow'
				),
				array(
					'service' => 'guard.key.info.workflow'
				));

				$memory = Snowblozm::execute($workflow, $memory);
				$register = false;

				if(!$memory['valid']){
					$memory['valid'] = $register = true;
					$session['expiry'] = 1;
					$memory['user'] = $memory['email'];
				}

				$workflow = array(
					array(
					'service' => 'cbcore.session.add.workflow',
					'expiry' => $session['expiry'],
					'output' => array('sessionid' => 'value')
				));

				$memory = Snowblozm::execute($workflow, $memory);

				if(!$memory['valid'])
					return $memory;

				//header('Location: '. isset($memory['continue']) ? $memory['continue'] : $session['root']);
				setcookie($session['key'], $memory['value'], $register ? 0 : time()+60*60*24*$session['expiry'], '/');

			} else {
				$memory['valid'] = false;
				$memory['msg'] = 'Authentication Failed';
				$memory['status'] = 407;
				$memory['details'] = "User authentication failed @interface.openid";
				return $memory;
			}

		} catch(ErrorException $e){
			$memory['valid'] = false;
			$memory['msg'] = 'Authentication Failure';
			$memory['status'] = 407;
			$memory['details'] = "Exception occurred : ".$e->getMessage()." @interface.openid";
			return $memory;
		}

		$memory['key'] = $session['key'];
		$memory['expires'] = $session['expiry'];
		$memory['continue'] = $memory['continue'] ? $memory['continue'] : $session['root'];

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Interface Session';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}

	/**
	 *	@interface Service
	**/
	public function output(){
		return array('user', 'key', 'value', 'expires', 'continue');
	}

}

?>
