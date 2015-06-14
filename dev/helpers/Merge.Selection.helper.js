/**
 *	@helper mergeSelection
 *
 *	@param selections array
 *	@param students array
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.mergeSelection = function($selections, $students, $stages){
	var $stdmap = {};
	for(var $i in $students){
		$std = $students[$i];
		$stdmap[$std['stdid']] = $std;
	}
	
	for(var $i in $selections){
		$sel = $selections[$i];
		$sel['student'] = $stdmap[$sel['selection']['refer']];
	}
	
	var $stgmap = {};
	for(var $i in $stages){
		$stg = $stages[$i]['stage'];
		$stgmap[$stg['stageid']] = $stg;
	}
	
	for(var $i in $selections){
		$sel = $selections[$i];
		$sel['stage'] = $stgmap[$sel['selection']['stageid']];
	}
	
	return $selection;
}
