<?php 
require_once(SBSERVICE);
require_once(EXGPACONF);

/**
 *	@class SlotEditWorkflow
 *	@desc Edits slot using ID
 *
 *	@param slotid long int Slot ID [memory]
 *
 *	@param mr varchar MR Slot [memory]
 *	@param ordinary varchar Ordinary Slot [memory]
 *	@param dream varchar Dream Slot [memory]
 *	@param super varchar Super Slot [memory]
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *
 *	@return slotid long int Slot ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return slot array Slot information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SlotEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'slotid'),
			'optional' => array('batchid' => 0, 'btname' => '', 'mr' => '', 'ordinary' => '', 'dream' => '', 'super' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		if($memory['super']){
			$memory['dream'] = $memory['dream'] ? $memory['dream'] : 'Not Eligible';
			$memory['ordinary'] = $memory['ordinary'] ? $memory['ordinary'] : 'Not Eligible';
			$memory['mr'] = $memory['mr'] ? $memory['mr'] : 'Not Eligible';
		}
		elseif($memory['dream']){
			$memory['ordinary'] = $memory['ordinary'] ? $memory['ordinary'] : 'Not Eligible';
			$memory['mr'] = $memory['mr'] ? $memory['mr'] : 'Not Eligible';
		}
		elseif($memory['ordinary']){
			$memory['mr'] = $memory['mr'] ? $memory['mr'] : 'Not Eligible';
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('mr', 'ordinary', 'dream', 'super'),
			'input' => array('id' => 'slotid', 'cname' => 'user', 'parent' => 'batchid', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'type' => 'slot',
			'sqlcnd' => "set `mr`='\${mr}', `ordinary`='\${ordinary}', `dream`='\${dream}', `super`='\${super}' where `slotid`=\${id}",
			'escparam' => array('mr', 'ordinary', 'dream', 'super'),
			'check' => false,
			'init' => false,
			'self' => false,
			'successmsg' => 'Slot edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'slotid', 'parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'sqlcnd' => "where `slotid`=\${id}",
			'errormsg' => 'Invalid Slot ID',
			'type' => 'slot',
			'successmsg' => 'Slot information given successfully',
			'output' => array('entity' => 'slot'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'batchid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('slotid', 'batchid', 'btname', 'slot', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>