/**
 *	@helper readUtype
 *
 *	@param ch utype character
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readUtype = function($ch){
	switch($ch){
		case 'S' :
			return 'Student';
		case 'C' :
			return 'Company';
		case 'T' :
			return 'TPO Web';
		default :
			return '';
			break;
	}
}
