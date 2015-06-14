<?php 
require_once(SBSERVICE);

/**
 *	@class MailListWorkflow
 *	@desc Returns all mails information in queue
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid/id long int Queue ID [memory] optional default 0
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@param user string User email [memory] optional default 'unknown@mail.list'
 *
 *	@return mails array Mails information [memory]
 *	@return queid long int Queue ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('queid' => 0, 'pgsz' => false, 'pgno' => 0, 'total' => false, 'user' => 'unknown@mail.list', 'id' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['queid'] = $memory['queid'] ? $memory['queid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'queid'),
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlprj' => '`mailid`, `to`, `subject`, `status`, `stime`, substring(`body`, 0, 50) as `body`',
			'sqlcnd' => "where `mailid` in \${list} order by `status` asc, `mailid` desc",
			'type' => 'mail',
			'successmsg' => 'Mails information given successfully',
			'output' => array('entities' => 'mails'),
			'mapkey' => 'mailid',
			'mapname' => 'mail'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('mails', 'queid', 'admin');
	}
	
}

?>