<?php 
require_once(SBSERVICE);

/**
 *	@class PersonInfoWorkflow
 *	@desc Returns person information by ID
 *
 *	@param pnid/id string Person ID [memory] optional default false
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory]
 *	@param peopleid long int People ID [memory] optional default PEOPLE_ID
 *
 *	@return person array Person information [memory]
 *	@return contact array Person contact information [memory]
 *	@return personal array Person personal information [memory]
 *	@return pnid long int Person ID [memory]
 *	@return name string Person name [memory]
 *	@return title string Person title [memory]
 *	@return thumbnail long int Person thumbnail ID [memory]
 *	@return dirid long int Thumbnail Directory ID [memory]
 *	@return username string Person username [memory]
 *	@return peopleid long int People ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return chain array Chain data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('pnid' => false, 'peopleid' => PEOPLE_ID, 'id' => 0),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Person information given successfully';
		$attr = $memory['pnid'] ? 'pnid' : ($memory['id'] ? 'pnid' : 'owner');
		$memory['pnid'] = $memory['pnid'] ? $memory['pnid'] : ($memory['id'] ? $memory['id'] : $memory['keyid']);
		$memory['dirid'] = PERSON_THUMB;
		
		// args arguments
		$memory['auth'] = isset($memory['auth']) ? $memory['auth'] : true;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pnid', 'parent' => 'peopleid', 'cname' => 'user'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlprj' => '`pnid`, `username`, `name`, `thumbnail`, `title`',
			'sqlcnd' => "where `$attr`='\${id}'",
			'errormsg' => 'Invalid Person ID',
			'successmsg' => 'Person information given successfully',
			'output' => array('entity' => 'person')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('person'),
			'params' => array(/*'person.name' => 'name', 'person.title' => 'title', 'person.username' => 'username',*/ 'person.thumbnail' => 'thumbnail')
		),
		array(
			'service' => 'people.person.profile.service'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('person', 'contact', 'personal', 'pnid', /*'name', 'title', 'username',*/ 'thumbnail', 'dirid', 'peopleid', 'admin', 'chain');
	}
	
}

?>
