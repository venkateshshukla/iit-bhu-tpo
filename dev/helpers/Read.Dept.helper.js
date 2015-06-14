/**
 *	@helper readDept
 *
 *	@param dept dept character
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readDept = function($dept){
	switch($dept){
		case 'cer' :
			return 'Ceramic Engineering';
		case 'che' :
			return 'Chemical Engineering';
		case 'civ' :
			return 'Civil Engineering';
		case 'cse' :
			return 'Computer Engineering';
		case 'eee' :
			return 'Electrical Engineering';
		case 'ece' :
			return 'Electronics Engineering';
		case 'mec' :
			return 'Mechanical Engineering';
		case 'met' :
			return 'Metallurgical Engineering';
		case 'min' :
			return 'Mining Engineering';
		case 'phe' :
			return 'Pharmaceutical Engineering';
		case 'apc' :
			return 'Applied Chemistry';
		case 'apm' :
			return 'Applied Mathematics';
		case 'app' :
			return 'Applied Physics';
		case 'bce' :
			return 'Bio-Chemical Engineering';
		case 'bme' :
			return 'Bio-Medical Engineering';
		case 'mst' :
			return 'Material Science & Technology';
		default :
			return '';
			break;
	}
}
