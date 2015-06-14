<?php 
require_once(SBSERVICE);

/**
 *	@class DataEncodeService
 *	@desc Encodes array into JSON XML WDDX PLAIN HTML NONE data
 *
 *	@param type string Request type [memory] optional default 'json' ('json', 'xml', 'wddx', 'plain', 'html', 'none')
 *	@param data array Data to be encoded [memory]
 *
 *	@return result string Encoded data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataEncodeService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('type' => 'json', 'data' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$type = $memory['type'];
		$data = $memory['data'];
		
		switch($type){
			case 'json' :
				$result = json_encode($data);
				break;
			case 'xml' :
				$result = $this->xml_encode($data);
				break;
			case 'wddx' :
				$result = wddx_serialize_value($data);
				break;
			case 'plain' :
				$result = var_dump($data);
				break;
			case 'html' :
				$result = $this->html_encode($data);
				break;
			case 'none' :
				$result = $data;
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Data Type';
				$memory['status'] = 501;
				$memory['details'] = 'Data encoding not supported for type : '.$type.' @data.encode.service';
				return $memory;
		}
		
		if($result === false || $result == null){
			$memory['valid'] = false;
			$memory['msg'] = 'Unable to encode data';
			$memory['status'] = 501;
			$memory['details'] = 'Data could not be encoded @data.encode.service';
			return $memory;
		}

		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Encoding';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result');
	}
	
	public function xml_encode($data){
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('root');
		
		@$this->write($xml, $data);

		$xml->endElement();
		$xml->endDocument();
		return $xml->outputMemory(true);
	}
	
	private function write(XMLWriter $xml, $data){
		foreach($data as $key => $value){
			if(is_array($value)){
				$xml->startElement($key);
				@$this->write($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	} 
	
	public function html_encode($data){
		return 'Not implemented yet';
	}
	
}

?>