<?php 
require_once(SBSERVICE);

/**
 *	@class ProcessRunService
 *	@desc Runs a command as new process with given input and returns output and error
 *
 *	@param cmd string Command [memory]
 *	@param intype integer Input type [memory] (0=pipe 1=file) optional default 0
 *	@param stdin string Input [memory] optional default false (pipe='', file="/dev/null")
 *	@param outtype integer Output type [memory] (0=pipe 1=file) optional default 0
 *	@param stdout string Output [memory] optional default false (pipe=returned output, file="/dev/null")
 *	@param errtype integer Error type [memory] (0=pipe 1=file) optional default 0
 *	@param stderr string Error [memory] optional default false (pipe=returned error, file="/dev/null")
 *	@param cwd string Directory absolute path [memory] optional default null
 *
 *	@return result integer Return value [memory]	
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class ProcessRunService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('cmd'),
			'optional' => array('intype' => 0, 'stdin' => false, 'outtype' => 0, 'stdout' => false, 'errtype' => 0, 'stderr' => false, 'cwd' => null)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$cmd = escapeshellarg($memory['cmd']);
		$cwd = $memory['cwd'];
		$stdin = $memory['intype'] ? array('file', $memory['stdin'] ? $memory['stdin'] : '/dev/null', 'r') : array('pipe', 'r');
		$stdout = $memory['outtype'] ? array('file', $memory['stdout'] ? $memory['stdout'] : '/dev/null', 'w') : array('pipe', 'w');
		$stderr = $memory['errtype'] ? array('file', $memory['stderr'] ? $memory['stderr'] : '/dev/null', 'a') : array('pipe', 'w');
		
		$desc = array(
			0 => $stdin,
			1 => $stdout,
			2 => $stderr
		);
		
		$process = proc_open($cmd, $desc, $pipes, $cwd);
		if(is_resource($process)) {
			if(!$memory['intype']){
				fwrite($pipes[0], $memory['stdin'] ? $memory['stdin'] : '');
				fclose($pipes[0]);
			}
			
			if(!$memory['outtype']){
				$memory['stdout'] = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
			}
			
			if(!$memory['errtype']){
				$memory['stderr'] = stream_get_contents($pipes[2]);
				fclose($pipes[2]);
			}
			
			$memory['result'] = proc_close($process);
		}
		else {
			$memory['valid'] = false;
			$memory['msg'] = "Unable to Run Process";
			$memory['status'] = 505;
			$memory['details'] = 'Error running process : '.$directory.' @process.run.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Process Ran Successfully';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'stdout');
	}
	
}

?>