<?php 
require_once(SBSERVICE);

/**
 *	@class ProcessEvaluateWorkflow
 *	@desc Runs a command as new process with given input and evaluates performance
 *
 *	@param cmd string Command [memory]
 *	@param infile string Input [memory] optional default "/dev/null"
 *	@param outfile string Output [memory] optional default "/dev/null"
 *	@param cwd string Directory absolute path [memory] optional default '/tmp'
  *	@param lmt_mem integer memory limit (in bytes) [memory] optional default 64MB
 *	@param lmt_stk integer stack limit (in bytes) [memory] optional default 8MB
 *	@param lmt_fo integer output limit (in bytes) [memory] optional default 50MB
 *	@param lmt_fl integer file limit (count) [memory] optional default 16
 *	@param lmt_time integer time limit (in seconds) [memory] optional default 2
 *	@param lmt_tm_max integer maximum time limit (in seconds) [memory] optional default 3
 *
 *	@return exit_status integer exit status [memory]
 *	@return totaltime double total execution time [memory]
 *	@return usertime double user time [memory]
 *	@return systime double system time [memory]
 *	@return memory long memory [memory]
 *	@return mjpf long major page faults [memory]
 *	@return mnpf long minor page faults [memory]
 *	@return vcsw long voluntary context switches [memory]
 *	@return ivcsw long involuntary context switches [memory]
 *	@return fsin long file system inputs [memory]
 *	@return fsout long file system outputs [memory]
 *	@return msgrcv long messages received [memory]
 *	@return msgsnd long messages sent [memory]
 *	@return signals long signals [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class ProcessEvaluateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('cmd'),
			'optional' => array('infile' => '/dev/null', 'outfile' => '/dev/null', 'cwd' => '/tmp', 'lmt_mem' => 67108864, 'lmt_stk' => 8388608, 'lmt_fo' => 52428800, 'lmt_fl' => 16, 'lmt_time' => 2, 'lmt_tm_max' => 3)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['cmd'] = escapeshellarg($memory['cmd']);
		$memory['msg'] = 'Program Evaluated Successfully';
		
		$workflow = array(
		array(
			'service' => 'cbcore.data.numeric.service',
			'args' => array('lmt_mem', 'lmt_stk', 'lmt_fo', 'lmt_fl', 'lmt_time', 'lmt_tm_max')
		),
		array(
			'service' => 'cbcore.data.substitute.service',
			'args' => array('infile', 'outfile', 'cwd', 'lmt_mem', 'lmt_stk', 'lmt_fo', 'lmt_fl', 'lmt_time', 'lmt_tm_max', 'cmd'),
			'data' => 'evaluator "i:${infile}" "o:${infile}" "d:${cwd}" "m:${lmt_mem}" "s:${lmt_stk}" "f:${lmt_fo}" "l:${lmt_fl}" "t:${lmt_time}" "x:${lmt_tm_max}" ${cmd}',
			'output' => array('result' => 'evl_cmd')
		),
		array(
			'service' => 'invoke.process.run.service',
			'input' => array('cmd' => 'evl_cmd'),
			'cwd' => ADROOT.'../c/bin',
			'output' => array('stdout' => 'data')
		),
		array(
			'service' => 'cbcore.data.decode.service',
			'type' => 'json'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.exit_status' => 'exit_status', 'result.totaltime' => 'totaltime', 'result.usertime' => 'usertime', 'result.systime' => 'systime', 'result.memory' => 'memory', 'result.mjpf' => 'mjpf', 'result.mnpf' => 'mnpf', 'result.vcsw' => 'vcsw', 'result.ivcsw' => 'ivcsw', 'result.fsin' => 'fsin', 'result.fsout' => 'fsout', 'result.msgrcv' => 'msgrcv', 'result.msgsnd' => 'msgsnd', 'result.signals' => 'signals')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('exit_status', 'totaltime', 'usertime', 'systime', 'memory', 'mjpf', 'mnpf', 'vcsw', 'ivcsw', 'fsin', 'fsout', 'msgrcv', 'msgsnd', 'signals');
	}
	
}

?>