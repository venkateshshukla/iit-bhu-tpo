/**
 *	@helper readVType
 *
 *	@param tp visit type character
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readVType = function($tp){
	switch($tp){
		case 'placement' :
			return 'Placement';
		case 'internship' :
			return 'Internship';
		case 'ppo' :
			return 'Pre-Placement Offer';
		default :
			return '';
			break;
	}
}
