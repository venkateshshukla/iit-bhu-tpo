<?php 
require_once(SBSERVICE);

/**
 *	@class FormContactWorkflow
 *	@desc Accepts new contact online submission
 *
 *	@param name string Contact Name [memory]
 *	@param cntemail string Contact Email [memory]
 *	@param cntphone string Company Person Phone [memory]
 *	@param subject string Message Subject [memory]
 *	@param body string Message Body [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *
 *	@param mailto string Mail To [memory] optional default 'form_mailto' Snowblozm ['reponse']
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FormContactWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'name', 'cntemail', 'cntphone', 'subject', 'body'),
			'optional' => array('mailto' => false, 'user' => 'public')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		$memory['keyid'] = TPO_KEY;
		$memory['queid'] = FORM_ID;
		$memory['msg'] = 'Message Sent Successfully';
		
		//if(!$memory['mailto']){
		$mailto = Snowblozm::get('form_mailto');
		$memory['mailto'] = $mailto['contact'].','.$memory['cntemail'];
		//}
		
		$memory['body'] = 'Contact Us Message

Name		'.$memory['name'].'
Email		'.$memory['cntemail'].'
Phone		'.$memory['cntphone'].'

Message

'.$memory['body'].'
		
--
T&P Portal Online Forms

Training and Placement Cell
IIT (BHU) Varanasi - 221005
http://iitbhu.ac.in/tpo/
';
		
		$workflow = array(
		array(
			'service' => 'queue.mail.add.workflow',
			'input' => array('to' => 'mailto'),
			'subject' => '[Contact Online] '.$memory['subject']
		), 
		array(
			'service' => 'queue.mail.send.workflow'
		));

		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>
