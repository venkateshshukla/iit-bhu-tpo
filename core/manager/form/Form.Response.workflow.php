<?php 
require_once(SBSERVICE);

/**
 *	@class FormResponseWorkflow
 *	@desc Accepts new response sheet submission
 *
 *	@param name string Company Name [memory]
 *	@param regtype string Registration Type [memory]
 *	@param regtype string Registration Type [memory] optional default ''
 *	@param indsector array Industry Sector [memory] optional default array()
 *	@param funreq array Functional Requirements [memory] optional default array()
 *	@param motto string Motto [memory] optional default ''
 *	@param compsnname string Company Person Name [memory]
 *	@param compsnpost string Company Person Post [memory]
 *	@param compsnemail string Company Person Email [memory]
 *	@param compsnphone string Company Person Phone [memory]
 *	@param sccgpa string CGPA Limit [memory] optional default ''
 *	@param scagelimit string Age Limit [memory] optional default ''
 *	@param seltest array Tests [memory] optional default array()
 *	@param selgd string GD [memory] optional default ''
 *	@param selinterview array Interviews [memory] optional default array()
 *	@param selagrmnt string Service Agreement [memory] optional default ''
 *	@param seltrprd string Training Period [memory] optional default ''
 *	@param reqmnt array Requirements [memory] optional default array()
 *	@param tectc string CTC [memory]
 *	@param teinhand string Inhand [memory] optional default ''
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *
 *	@param mailto string Mail To [memory] optional default 'form_mailto' Snowblozm ['reponse']
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FormResponseWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'name', 'compsnname', 'compsnpost', 'compsnemail', 'compsnphone', 'tectc'),
			'optional' => array('address' => '', 'regtype' => '', 'indsector' => array(), 'funreq' => array(), 'motto' => '', 'sccgpa' => '', 'scxiip' => '', 'scxp' => '', 'scspl' => '', 'scagelimit' => '', 'seltest' => array(), 'selgd' => '', 'selinterview' => array(), 'selagrmnt' => '', 'seltrprd' => '', 'reqmnt' => array(), 'teinhand' => '', 'mailto' => false, 'user' => 'public')
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
		$memory['msg'] = 'Response Submitted Successfully. Please check your mailbox <'.$memory['compsnemail'].'> for conrfirming that its sent correctly. <br />(It may take few minutes for sending the mail)';
		
		//if(!$memory['mailto']){
		$mailto = Snowblozm::get('form_mailto');
		$memory['mailto'] = $mailto['response'].','.$memory['compsnemail'];
		//}
		
		$memory['body'] = 'Company Response Sheet Submission

Basic Information
	
	Name of the Company : '.$memory['name'].'
	Address : '.$memory['address'].'
	Type : '.$memory['regtype'].'
	Industry Sector(s) : '.implode(', ', $memory['indsector']).'
	Requirement(s) in terms of Functional Areas : '.implode(', ', $memory['funreq']).'
	Vision and Interests : '.$memory['motto'].'
			
Contact Person from Organization

	Name : '.$memory['compsnname'].'
	Designation : '.$memory['compsnpost'].'
	Email : '.$memory['compsnemail'].'
	Phone : '.$memory['compsnphone'].'
			
Shortlisting Criteria
	
	CGPA (on 10 point scale) : '.$memory['sccgpa'].'
	XII Percentage : '.$memory['scxiip'].'
	X Percentage : '.$memory['scxp'].'
	Specialization (for post graduate students) : '.$memory['scspl'].'
	Age Limits (if any) : '.$memory['scagelimit'].'
			
Selection Process
	
	Test(s) : '.implode(', ', $memory['seltest']).'
	Group Discussion : '.$memory['selgd'].'
	Personal Interview(s) : '.implode(', ', $memory['selinterview']).'
	Service Agreement (if any) : '.$memory['selagrmnt'].'
	Training Period (if any) : '.$memory['seltrprd'].'
			
Students\' Profile and Your Tentative Requirement (expected, not necessarily committed)
	
';
		foreach($memory['reqmnt'] as $data)	$memory['body'] .= "\t\t$data\n";
			
		$memory['body'] .= '
Total Emoluments
			
	CTC : '.$memory['tectc'].'
	In Hand : '.$memory['teinhand'].'

(End of Response Sheet)

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
			'subject' => '[Response Sheet] '.$memory['name']
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
