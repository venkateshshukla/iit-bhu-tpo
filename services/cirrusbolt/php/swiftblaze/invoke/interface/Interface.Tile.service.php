<?php 
require_once(SBSERVICE);

/**
 *	@class InterfaceTileService
 *	@desc Processes interface elements in Tile UI
 *
 *	@param cookiename string Cookie name [memory]
 *	@param cookieexpiry string Cookie expiry [memory]
 *	@param rootpath string Rootpath [memory]
 *
 *	@param page string Tile UI content [memory] optional default 'home'
 *	@param pages object Array of static html pages [memory] optional default array()
 *	@param ui object UI data [memory] optional default array()
 *
 *	@param reqtype string request type [memory] ('get', 'post', 'json', 'wddx', 'xml')
 *	@param restype string response types [memory] ('json', 'wddx', 'xml', 'plain', 'html'),
 *	@param crypt string Crypt types [memory] ('none', 'rc4', 'aes', 'blowfish', 'tripledes')
 *	@param hash string Hash types [memory] ('none', 'md5', 'sha1', 'crc32')
 *	@param access array allowed service provider names [memory] optional default false
 *
 *	@param email string Identification email to be used if not set in message [memory] optional default false
 *	@param context string Application context for email [memory] optional default false
 *
 *	@param html string Tile UI html [memory] optional default ''
 *	@param tiles string Tile UI tiles [memory] optional default ''
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *
 *	@return html string Tile UI html [memory]
 *	@return tiles string Tile UI tiles [memory]
 *	@return execute boolean Service execute flag [memory]
 *	@return email string Email [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class InterfaceTileService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('reqtype', 'restype', 'crypt' , 'hash'),
			'optional' => array(
				'page' => 'home', 
				'pages' => array(), 
				'ui' => array(), 
				'access' => array(), 
				'email' => false, 
				'context' => false, 
				'html' => '', 
				'tiles' => '',
				'cache' => true,
				'expiry' => 150
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$cache = $memory['cache'];

		if($cache){
			$poolkey = 'INTERFACE_TILE_'.json_encode($memory);
			$pool = Snowblozm::run(array(
				'service' => 'pool.lite.get.service',
				'key' => $poolkey
			), array());
		}
		
		if($cache && $pool['valid']){
			$memory = $pool['data'];
		} 
		else {
		
			$memory['execute'] = false;
			
			if(isset($memory['pages'][$memory['page']])){
				$page = $memory['pages'][$memory['page']];
			}
			else if(isset($memory['ui'][$memory['page']])){
				$memory['execute'] = true;
				$config = $memory['ui'][$memory['page']];
				$idkey = $config['id'];
				$fragment = $config['fragment'];
				$page = array();
			}
			else {
				$page = $memory['pages']['error'];
			}
			
			if(is_array($page)){
				foreach($page as $pg){
					$memory['tiles'] .= file_get_contents($pg.'.tile.html');
					$memory['html'] .= file_get_contents($pg.'.html');
				}
			}
			else {
				$memory['tiles'] .= file_get_contents($page.'.tile.html');
				$memory['html'] .= file_get_contents($page.'.html');
			}
			
			/**
			 *	@invoke Launch Message if any 
			**/
			if($memory['execute']){
				$memory = Snowblozm::run(array(
					'service' => 'invoke.launch.message.workflow',
					'raw' => true
				), $memory);
				
				$id = $memory['response'][$idkey];
				$pg = str_replace('.', '-', $memory['page']);
				
				$memory['html'] .= '
					<script type="text/javascript">
						Snowblozm.Registry.save("ui-'.$pg.'-'.$id.'", '.$memory['result'].');
						window.location.hash = OrbitNote.jquery.helper.defaultHash("#inittile:key=ui-'.$pg.':id='.$id.':ins='.$ins.':tpl='.$tpl.':tile='.$tile.$id.'");
					</script>';
			}
			
			$memory['valid'] = true;
			$memory['msg'] = 'Valid Tile UI Interface';
			$memory['status'] = 200;
			$memory['details'] = 'Successfully executed';
		
			if($cache){
				Snowblozm::run(array(
					'service' => 'pool.lite.save.service',
					'key' => $poolkey,
					'data' => $memory
				), array());
			}
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('html', 'tiles', 'execute', 'email');
	}
	
}

?>