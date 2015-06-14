/**
 *	@initialization Namespaces
**/
Executive = {
	core : {
		service : {}, workflow : {}, helper : {}, constant : {}, template : {}
	}
};

/**
 *	@initialization Session variables
**/
Executive.session = {
	user : false,
	pass : false
};

/**
 *	@initialization UI data
**/
Executive.data = {
	launch : []
};

/**
 *	@constant loadmsg
 *	@desc HTML content to show while loading content (default '<p class="loading">Loading ...</p>')
 *
**/
Executive.core.constant.loadmsg = '<p class="loading">Loading ...</p>';

/**
 *	@constant successmsg
 *	@desc HTML content to indicate successful execution (default '<img src="ui/img/icons/ok.gif">')
 *
**/
Executive.core.constant.successimg = '<img src="ui/img/icons/ok.gif">';

/**
 *	@constant errormsg
 *	@desc HTML content to indicate erroneous execution (default '<img src="ui/img/icons/error.gif">')
 *
**/
Executive.core.constant.errorimg = '<img src="ui/img/icons/error.gif">';

var CKEDITOR_BASEPATH = 'ui/js/ckeditor/';

/**
 *	@overrides FireSpark constants and helpers
**/
FireSpark.smart.constant.config = ['desk'];
FireSpark.smart.constant.statusdelay = 500;
FireSpark.smart.constant.statusduration = 150;
FireSpark.smart.constant.moveup = true;
FireSpark.smart.constant.moveduration = 1000;

FireSpark.core.helper.readDate = function($time){
	if($time == '0000-00-00') return 'Date yet to confirm';
	var $d = new Date($time);
	return $d.toDateString();
}

/**
 *	@initialization Snowblozm and Executive
**/
$(document).ready(function(){

	'#tab'.save( FireSpark.smart.workflow.InterfaceTab );
	'#html'.save( FireSpark.smart.workflow.ReadHtml );
	'#read'.save( FireSpark.smart.workflow.ReadTmpl );
	'#write'.save( FireSpark.smart.workflow.WriteData );
	'#bind'.save( FireSpark.ui.workflow.TemplateBind );
	'#ui'.save( FireSpark.smart.workflow.InterfaceLoad );
	'#view'.save( FireSpark.smart.workflow.InterfaceTile );
	'#sync'.save( FireSpark.smart.service.InterfaceUrl );
	'#login'.save( FireSpark.core.workflow.WindowLogin );
	'#refresh'.save( FireSpark.core.service.WindowReload );
	'#close'.save( FireSpark.ui.service.ContainerRemove );
	
	[{
		service : FireSpark.smart.service.InterfaceTrigger,
		selector : 'a.navigate',
		event : 'click',
		attribute : 'href',
		escaped : false,
		hash : true
	},{
		service : FireSpark.smart.service.InterfaceTrigger,
		selector : 'a.launch',
		event : 'click',
		attribute : 'href',
		escaped : false,
		nav : true
	},{
		service : FireSpark.smart.service.InterfaceTrigger,
		selector : 'a.ui',
		event : 'click',
		attribute : 'href',
		escaped : false,
		nav : '#/ui/'
	},{
		service : FireSpark.smart.service.InterfaceTrigger,
		selector : 'form.navigate',
		event : 'submit',
		attribute : 'id',
		escaped : true
	},{
		service : FireSpark.smart.service.InterfaceTrigger,
		selector : '.button.navigate',
		event : 'click',
		attribute : 'id',
		escaped : false
	},{
		service : FireSpark.smart.service.InterfaceHistory
	}].execute();
	
	/*FireSpark.ui.helper.transformRobin($('div.partners a'), {
		selector : '.data',
		interval : 5000,
		total : 1
	});*/
	
	for(var $i in Executive.data.launch){
		Executive.data.launch[$i].launch();
	}
	
	/**
	 *	@urlcheck
	**/
	var urlcheck = function(){
		FireSpark.smart.service.InterfaceUrl.run({
			navigator : window.location.hash,
			save : true,
			nofrc : true
		});
	}
	//window.setInterval(urlcheck, 3500);
	$('#main-container>.tile-content').eq(0).fadeIn(1000);
	urlcheck();
	
	/**
	 *	@server loads
	**
	Executive.core.helper.serverTime($('#server-time'), 'ui/php/time.php');
	Executive.core.helper.serverPortal($('#server-portal'), 'ui/php/portal.php');
	
	/**
	 *	@scroll top
	**/
	$('span.top a').live('click', function (){
			$('body,html').animate({
				scrollTop: 0
			}, 850);
			return false;
	});
	
	/**
	 *	@editor wysiwyg
	**/
	$('textarea.wysiwyg').live('focusin', function(){
		if(!$(this).hasClass('wysiwyg-done')){
			$(this).wysiwyg().addClass('wysiwyg-done');
		}
	});
	
	/**
	 *	@panel smart-panel
	**/
	$('.smart-panel').live('load', function(){
		FireSpark.ui.helper.transformSmartpanel($(this).find('.smart-block'));
		FireSpark.ui.helper.transformSmartpanel($(this).find('.inline-block'), {
			display: '.inline-display',
			edit: '.inline-display',
			form: '.inline-form',
			cancel: '.inline-cancel'
		});
		return false;
	});
	
	$('.smart-panel').each(function(){
		FireSpark.ui.helper.transformSmartpanel($(this).find('.smart-block'));
		FireSpark.ui.helper.transformSmartpanel($(this).find('.inline-block'), {
			display: '.inline-display',
			edit: '.inline-display',
			form: '.inline-form',
			cancel: '.inline-cancel'
		});
		return false;
	});
	
	/**
	 *	@datatable dataTable
	**/
	$.extend($.fn.dataTable.defaults, {
		//bSort: false,
		aaSorting : [],
		sPaginationType: "full_numbers",
		iDisplayLength: 50,
		aLengthMenu: [[50, 100, 150, -1], [50, 100, 150, "All"]]
	});
	
	$('.data-table-panel').live('load', function(){
		var $el = $(this).find('table.datatable');
		if(!$el.hasClass('datatable-done')){
			$el.dataTable().addClass('datatable-done');
		}
		return false;
	});
	
	$('.data-table-panel').each(function(){
		var $el = $(this).find('table.datatable');
		if(!$el.hasClass('datatable-done')){
			$el.dataTable().addClass('datatable-done');
		}
		return true
	});
	
	/**
	 *	@editor cmnt-text
	**/
	$('textarea.cmnt-text').live('focusin', function(){
		if(!$(this).hasClass('cmnt-text-done')){
			$(this).addClass('cmnt-text-done');
		}
	});
	
	$.expander.defaults = {
		slicePoint: 150,
		preserveWords: true,
		widow: 5,
		expandText: 'show more',
		expandPrefix: '... ',
		summaryClass: 'summary',
		detailClass: 'details',
		moreClass: 'read-more',
		lessClass: 'read-less',
		collapseTimer: 0,
		expandEffect: 'fadeIn',
		expandSpeed: 250,
		collapseEffect: 'fadeOut',
		collapseSpeed: 200,
		userCollapse: true,
		userCollapseText: 'show less',
		userCollapsePrefix: ' '
	};
	
	$('.expander-panel').live('load', function(){
		$(this).find('.expander').expander();
	});
	
	FireSpark.smart.helper.dataState(FireSpark.smart.constant.initmsg, true);

});


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
/**
 *	@helper readCourse
 *
 *	@param crs course character
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readCourse = function($crs){
	switch($crs){
		case 'btech' :
			return 'B. Tech.';
		case 'idd' :
			return 'IDD / IMD';
		case 'mtech' :
			return 'M. Tech.';
		case 'web' :
			return 'IT BHU';
		default :
			return '';
			break;
	}
}
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
/**
 *	@helper readWapproval
 *
 *	@param ch wapproval integer
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readWapproval = function($ch){
	switch(Number($ch)){
		case -1 :
			return 'Willingness Rejected';
		case 0 :
			return 'Pending';
		case 1 :
			return 'Willingness Approved';
		case 2 :
			return 'Written Test';
		case 3 :
			return 'Group Discussion';
		case 4 :
			return 'Technical Interviews';
		case 5 :
			return 'HR Interviews';
		case 6 :
			return 'Received Offer';
		default :
			return '';
			break;
	}
}
/**
 *	@helper readWstatus
 *
 *	@param ch wstatus integer
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
Executive.core.helper.readWstatus = function($ch){
	switch(Number($ch)){
		case 0 :
			return 'Eligible';
		case 1 :
			return 'Willing';
		case -1 :
			return 'Not Willing';
		default :
			return '';
			break;
	}
}
