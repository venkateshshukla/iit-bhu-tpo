<?php 
require_once(SBSERVICE);

/**
 *	@class PersonEditWorkflow
 *	@desc Edits person using ID
 *
 *	@param pnid long int Person ID [memory]
 *	@param name string Person name [memory]
 *	@param title string Title [memory]
 *	@param dateofbirth string Date of birth [memory] (Format YYYY-MM-DD)
 *	@param gender string Gender [memory]  (M=Male F=Female N=None)
 *	@param address string Address [memory] 
 *	@param country string Country [memory]
 *	@param location long int Location [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'pnid', 'name', 'title', 'dateofbirth', 'gender', 'address', 'country'),
			'optional' => array('location' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$dob = $memory['dateofbirth'] ? "'\${dateofbirth}'" : 'null';
		
		$countries = array('Afghanistan','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antigua and Barbuda','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia-Herzegovina','Botswana','Bouvet Island','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Christmas Island','Cocos (Keeling) Islands','Colombia','Comoros','Congo, Democratic Republic of the (Zaire)','Congo, Republic of','Cook Islands','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Falkland Islands','Faroe Islands','Fiji','Finland','France','French Guiana','Gabon','Gambia','Georgia','Germany','Ghana','Gibraltar','Greece','Greenland','Grenada','Guadeloupe (French)','Guam (USA)','Guatemala','Guinea','Guinea Bissau','Guyana','Haiti','Holy See','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Ivory Coast (Cote D`Ivoire)','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique (French)','Mauritania','Mauritius','Mayotte','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','Netherlands Antilles','New Caledonia (French)','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','North Korea','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn Island','Poland','Polynesia (French)','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russia','Rwanda','Saint Helena','Saint Kitts and Nevis','Saint Lucia','Saint Pierre and Miquelon','Saint Vincent and Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia and South Sandwich Islands','South Korea','Spain','Sri Lanka','Sudan','Suriname','Svalbard and Jan Mayen Islands','Swaziland','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste (East Timor)','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Turks and Caicos Islands','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Venezuela','Vietnam','Virgin Islands','Wallis and Futuna Islands','Yemen','Zambia','Zimbabwe');
		
		if(!in_array($memory['country'], $countries)){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid Country';
			$memory['status'] = 500;
			$memory['details'] = 'Invalid country : '.$memory['country'].' @person.edit';
			return $memory;
		}
	
		$service = array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('name', 'title', 'dateofbirth', 'gender', 'address', 'location', 'country'),
			'input' => array('id' => 'pnid', 'cname' => 'user'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "set `name`='\${name}', `title`='\${title}', `dateofbirth`=$dob, `gender`='\${gender}', `address`='\${address}', `location`=\${location}, `country`='\${country}' where `pnid`=\${id}",
			'escparam' => array('name', 'title', 'dateofbirth', 'gender', 'address', 'country'),
			'check' => false,
			'successmsg' => 'Person edited successfully',
			'errormsg' => 'No Change / Invalid Person ID'
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