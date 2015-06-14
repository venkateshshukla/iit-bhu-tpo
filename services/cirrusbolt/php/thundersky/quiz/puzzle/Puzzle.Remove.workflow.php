<?php 
require_once(SBSERVICE);

/**
 *	@class PuzzleRemoveWorkflow
 *	@desc Removes puzzle by ID
 *
 *	@param pzlid long int Puzzle ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PuzzleRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'pzlid'),
			'optional' => array('quizid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'pzlid', 'parent' => 'quizid'),
			'conn' => 'ayconn',
			'relation' => '`puzzles`',
			'type' => 'puzzle',
			'sqlcnd' => "where `pzlid`=\${id}",
			'errormsg' => 'Invalid Puzzle ID',
			'successmsg' => 'Puzzle removed successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>