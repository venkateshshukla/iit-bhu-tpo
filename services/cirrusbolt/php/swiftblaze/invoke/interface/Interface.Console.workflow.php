<?php 
require_once(SBSERVICE);

/**
 *	@class InterfaceConsoleWorkflow
 *	@desc Processes interface console requests in Tile UI
 *
 *	@param root string root [memory] optional default 'home'
 *	@param page string page [memory] optional default false
 *	@param subpage string subpage [memory] optional default false
 *	@param pages object Array of static html pages [memory] optional default array()
 *
 *	@param html string Tile UI html [memory] optional default ''
 *	@param tiles string Tile UI tiles [memory] optional default ''
 *
 *	@return html string Tile UI html [memory]
 *	@return tiles string Tile UI tiles [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class InterfaceConsoleWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array(
				'root' => 'home',
				'page' => false, 
				'subpage' => false,
				'pages' => array(), 
				'id' => false,
				'name' => false,
				'html' => '', 
				'tiles' => '',
				'console' => array(),
				'emergency' => false
			),
			'set' => array('root', 'page', 'subpage')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['page'] = $memory['id'] ? $memory['id'] : $memory['page'];
		$memory['subpage'] = $memory['name'] ? $memory['name'] : $memory['subpage'];
		$memory['tiles'] = '';
		$memory['html'] = '';
		
		if($memory['page']){
			if(isset($memory['pages'][$memory['root']])){
				$page = $memory['pages'][$memory['root']].'/'.$memory['page'];
				if($memory['subpage'])
					$page .= '/'.$memory['subpage'];
			}
			else {
				$page = $memory['pages']['error'];
			}
			
			if(file_exists(UIBASE. 'html/'.$page.'.tile.html'))
				$memory['tiles'] .= file_get_contents(UIBASE. 'html/'.$page.'.tile.html');
			
			if(file_exists(UIBASE. 'html/'.$page.'.html'))
				$memory['html'] .= file_get_contents(UIBASE. 'html/'.$page.'.html');
			
			if(!$memory['emergency'] && file_exists(UIBASE. 'php/'.$page.'.console.php')){
				require_once(UIBASE. 'php/'.$page.'.console.php');
				$console = new Console($memory['keyid'], $memory['user'], $memory['console']);
				$memory['tiles'] .= $console->tiles();
				$memory['html'] .= $console->html();
			}
		}

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Tile UI Interface Console';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('html', 'tiles');
	}
	
	public static function get_include_contents($filename) {
		if (is_file($filename)) {
			ob_start();
			include $filename;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return false;
	}
	
}

?>