<?php 
require_once(SBSERVICE);

/**
 *	@class PersonAddWorkflow
 *	@desc Adds new person
 *
 *	@param name string Person name [memory]
 *	@param username string Person username [memory]
 *	@param recaptcha_challenge_field string Challenge [memory]
 *	@param recaptcha_response_field string Response [memory] 
 *	@param country string Country [memory]
 *	@param email string Email [memory] optional default false
 *	@param phone string Phone [memory] optional default false
 *	@param device string Device to verify [memory] optional default 'mail' ('mail', 'sms')
 *	@param location long int Location [memory] optional default 0
 *	@param keyid long int Usage Key [memory] optional default -1
 *	@param user string User name [memory] optional default ''
 *	@param peopleid long int People ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default 1 (people admin access allowed)
 *
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'added'
 *	@param join string Activity join [memory] optional default 'to'
 *	@param public integer Public log [memory] optional default 0
 *	@param human boolean Check human [memory] optional default true
 *	@param authorize string Auth control [memory] optional default 'add:remove:edit:list:con:per:rel:sub:act:eme:pbinfo'
 *
 *	@return pnid long int Person ID [memory]
 *	@return owner long int Key ID [memory]
 *	@return password string Password [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('name', 'username', 'recaptcha_challenge_field', 'recaptcha_response_field'),
			'optional' => array(
				'keyid' => -1, 
				'user' => '',
				'country' => 'India',
				'email' => false, 
				'phone' => false, 
				'peopleid' => PEOPLE_ID, 
				'level' => 1, 
				'location' => 0, 
				'device' => 'mail', 
				'pname' => '',
				'verb' => 'added',
				'join' => 'to',
				'public' => 0,
				'human' => true,
				'authorize' => 'add:remove:edit:list:con:per:rel:sub:act:eme:pbinfo'
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Person added successfully';
		//To do PEOPLE_ID 
		//$memory['peopleid'] = 5;
		//$memory['level'] = 1;
		$memory['role'] = 0;
		
		$countries = array('Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antigua and Barbuda','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia-Herzegovina','Botswana','Bouvet Island','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Christmas Island','Cocos (Keeling) Islands','Colombia','Comoros','Congo, Democratic Republic of the (Zaire)','Congo, Republic of','Cook Islands','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Falkland Islands','Faroe Islands','Fiji','Finland','France','French Guiana','Gabon','Gambia','Georgia','Germany','Ghana','Gibraltar','Greece','Greenland','Grenada','Guadeloupe (French)','Guam (USA)','Guatemala','Guinea','Guinea Bissau','Guyana','Haiti','Holy See','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Ivory Coast (Cote D`Ivoire)','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique (French)','Mauritania','Mauritius','Mayotte','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','Netherlands Antilles','New Caledonia (French)','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','North Korea','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn Island','Poland','Polynesia (French)','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russia','Rwanda','Saint Helena','Saint Kitts and Nevis','Saint Lucia','Saint Pierre and Miquelon','Saint Vincent and Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia and South Sandwich Islands','South Korea','Spain','Sri Lanka','Sudan','Suriname','Svalbard and Jan Mayen Islands','Swaziland','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste (East Timor)','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Turks and Caicos Islands','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Venezuela','Vietnam','Virgin Islands','Wallis and Futuna Islands','Yemen','Zambia','Zimbabwe');
		
		if(!in_array($memory['country'], $countries)){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid Country';
			$memory['status'] = 500;
			$memory['details'] = 'Invalid country : '.$memory['country'].' @person.add';
			return $memory;
		}
		
		$workflow = array(
		array(
			'service' => 'people.person.available.workflow'
		),
		array(
			'service' => 'cbcore.random.string.service',
			'length' => 15,
			'output' => array('random' => 'password')
		),
		array(
			'service' => 'transpera.reference.create.workflow',
			'input' => array('keyvalue' => 'password', 'parent' => 'peopleid', 'newuser' => 'username'),
			'output' => array('id' => 'pnid'),
			'root' => '/'.$memory['username'],
			'type' => 'person'
		),
		array(
			'service' => 'storage.file.add.workflow',
			'ext' => 'png',
			'mime' => 'image/png',
			'dirid' => PERSON_THUMB,
			'input' => array('name' => 'username', 'user' => 'username', 'keyid' => 'owner'),	//@possible 'level' => 2,
			'output' => array('fileid' => 'thumbnail')
		),
		/*array(
			'service' => 'people.role.add.workflow',
			'name' => 'Global',
			'desc' => 'Default Role',
			'input' => array('user' => 'username'),
			'output' => array('rlid' => 'role')
		),*/
		array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('pnid', 'name', 'username', 'owner', 'thumbnail', 'email', 'phone', 'location', 'role', 'device'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "(`pnid`, `name`, `username`, `owner`, `thumbnail`, `email`, `phone`, `location`, `role`, `device`) values (\${pnid}, '\${name}', '\${username}', \${owner}, \${thumbnail}, '\${email}', '\${phone}', \${location}, \${role}, '\${device}')",
			'escparam' => array('name', 'username',  'email', 'phone', 'device')
		));
		
		if($memory['human'] !== false){
			array_unshift($workflow, array(
				'service' => 'invoke.human.recaptcha.service'
			));
		}
		
		if($memory['email'] !== false){
			array_push($workflow, array(
				'service' => 'guard.openid.add.workflow',
				'input' => array('keyid' => 'owner')
			));
		}
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('pnid', 'owner', 'password');
	}
	
}

?>