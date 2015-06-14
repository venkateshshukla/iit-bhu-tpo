<?php 
	
	/**
	 *	@launch Person Find
	**/
	$memory = Snowblozm::run(array(
		'service' => 'invoke.launch.workflow.workflow',
		'message' => array(
			'service' => 'executive.student.find.workflow'
		)
	), $memory);
	//echo json_encode($memory);exit;
	/**
	 *	@launch Read response
	**/
	if($memory['valid'] && $memory['message']['valid']){
		$student = $memory['message'];
		$stdid = $student['stdid'];
		$memory['stowner'] = $student['student']['owner'];
		
		//FIRESPARK_SI_DATA_URL_run.php_DATA_service=person&id=379&name=tr4n2uil&_TYPE_json_REQUEST_POST
		$HTML .= '
			<script type="text/javascript">
				"FIRESPARK_SI_DATA_URL_run.php_DATA_service=student&id='.$student['person']['username'].'&navigator=#/ui/student/'.$student['person']['username'].'/~/&_TYPE_json_REQUEST_POST".save('.$memory['result'].');
				Executive.session.user = "'.$memory['user'].'";
				Executive.data.student = '.$stdid.';
				Executive.data.username = "'.$student['person']['username'].'";
				Executive.data.dept = "'.$student['batch']['dept'].'";
				Executive.data.batch = "'.$student['batch']['btname'].'";
			</script>';
		
		$memory['person'] = $student;
		$info = '';
		if($student['student']['ustatus'] == '0'){
			$info = '<li><a href="#" class="navigate">Account Suspended</a></li>';
		}
		
		$mem = Snowblozm::execute(array(
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('stowner'),
			'conn' => 'exconn',
			'relation' => '`willingnesses` w, `visits` v',
			'type' => 'student',
			'sqlprj' => "count(wlgsid) as `cnt`",
			'sqlcnd' => "where v.`visitid`=w.`visitid` and w.`owner`=\${stowner} and v.`vstatus`='Open for Willingness' and w.`status`=0",
			'successmsg' => 'Students information given successfully',
			'output' => array('result' => 'opptodo'),
			'escparam' => array(),
			'check' => false,
			'ismap' => false,
		),
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('stowner'),
			'conn' => 'exconn',
			'relation' => '`willingnesses` w, `visits` v',
			'type' => 'student',
			'sqlprj' => "count(wlgsid) as `cnt`",
			'sqlcnd' => "where v.`visitid`=w.`visitid` and w.`owner`=\${stowner} and v.`vstatus`='Process Completed' and w.`status`=1 and w.`approval` > 0 and w.`experience`=''",
			'successmsg' => 'Students information given successfully',
			'output' => array('result' => 'exptodo'),
			'escparam' => array(),
			'check' => false,
			'ismap' => false,
		)), $memory);
		
		if($mem['valid']){
			$opptodo = $mem['opptodo'][0]['cnt'];
			$exptodo = $mem['exptodo'][0]['cnt'];
			
			$protodo = 0;
			$proarray = array();
			$std = $student['student'];
			$cnt = $student['contact'];
			$grd = $student['grade'];
			$bch = $student['batch'];
			$psl = $student['personal'];
			if(preg_match('/\d/', $std['name'])){ $protodo++; array_push($proarray, 'name'); }
			if(strlen($cnt['phone']) != 10 or !is_numeric($cnt['phone'])){ $protodo++; array_push($proarray, 'phone'); }
			if(!$cnt['address']){ $protodo++; array_push($proarray, 'address'); }
			if(strlen($std['resphone']) != 10 or !is_numeric($std['resphone'])){ $protodo++; array_push($proarray, 'resphone'); }
			if(!$std['resaddress']){ $protodo++; array_push($proarray, 'resaddress'); }
			if(!$std['city']){ $protodo++; array_push($proarray, 'city'); }
			if(!$psl['dateofbirth']){ $protodo++; array_push($proarray, 'dateofbirth'); }
			if($psl['gender'] == 'N'){ $protodo++; array_push($proarray, 'gender'); }
			if(!$std['category']){ $protodo++; array_push($proarray, 'category'); }
			if(!$std['language']){ $protodo++; array_push($proarray, 'language'); }
			if(!$std['father']){ $protodo++; array_push($proarray, 'father'); }
			if(!$std['foccupation']){ $protodo++; array_push($proarray, 'foccupation'); }
			if(!$std['mother']){ $protodo++; array_push($proarray, 'mother'); }
			if(!$std['moccupation']){ $protodo++; array_push($proarray, 'moccupation'); }
			if(!is_numeric($grd['cgpa']) or !(float)$grd['cgpa']){ $protodo++; array_push($proarray, 'cgpa'); }
			if(!is_numeric($grd['sscx']) or !(float)$grd['sscx']){ $protodo++; array_push($proarray, 'sscx'); }
			if(!$grd['sscyear'] or !is_numeric($grd['sscyear'])){ $protodo++; array_push($proarray, 'sscyear'); }
			if(!$grd['sscboard']){ $protodo++; array_push($proarray, 'sscboard'); }
			if(!is_numeric($grd['hscxii']) or !(float)$grd['hscxii']){ $protodo++; array_push($proarray, 'hscxii'); }
			if(!$grd['hscyear'] or !is_numeric($grd['hscyear'])){ $protodo++; array_push($proarray, 'hscyear'); }
			if(!$grd['hscboard']){ $protodo++; array_push($proarray, 'hscboard'); }
			if(!$grd['jee']){ $protodo++; array_push($proarray, 'jee'); }
			
			if($bch['course'] == 'mtech'){
				if($std['specialization'] == ''){ $protodo++; array_push($proarray, 'specialization'); }
				if(!is_numeric($grd['gradcent']) or !(float)$grd['gradcent']){ $protodo++; array_push($proarray, 'gradcent'); }
				if(!$grd['gradyear'] or !is_numeric($grd['gradyear'])){ $protodo++; array_push($proarray, 'gradyear'); }
				if(!$grd['gradboard']){ $protodo++; array_push($proarray, 'gradboard'); }
				if(!$grd['gate'] or !is_numeric($grd['gate'])){ $protodo++; array_push($proarray, 'gate'); }
			}
			
			$remtodo = $std['remarks'] ? 1 : 0;
			
			$todo = $opptodo + $exptodo + $protodo + $remtodo;
		}
		else {
			$opptodo = 0;
			$exptodo = 0;
			$protodo = 0;
			$remtodo = 0;
			$todo = 0;
		}
		
		$STATEMENU .=
		'<ul class="hover-menu vertical fright">
			<li>
				<a href="student/'.$student['person']['username'].'/" class="ui" >'.$memory['user'].'<img src="storage/public/thumbnails/person/'.$memory['person']['person']['username'].'.png" alt="USER_THUMBNAIL" class="thumbhead" style="margin: -8px 0;"></a>
				<ul class="menu-item">
					'.$info.'
					<li><a href="student/'.$student['person']['username'].'/" class="ui" >Profile</a></li>
					<li><a href="identities/" class="ui">Identities</a></li>
					<li><a href="#/read/~/data/service=session&enc=get/ln/#login" class="launch">Sign Out</a></li>
				</ul>
			</li>
		</ul>';
		
		if($student['person']['username'] != 'tpo.itbhu' and $student['person']['username'] != 'vibhaj8')
			$STATEMENU .=
			'<ul class="hover-menu vertical fright">
				<li>
					<a href="#" class="navigate portal-menu" >Portal</a>
					<ul class="menu-item">
						<li><a href="batch/'.$student['batch']['btname'].'/" class="ui" >Batch '.$student['batch']['btname'].'</a></li>
						<li><a href="batches/'.$student['batch']['year'].'/" class="ui" >Batches '.$student['batch']['year'].'</a></li>
						<li><a href="batches/'.$student['batch']['dept'].'/" class="ui" >Batches '.strtoupper($student['batch']['dept']).'</a></li>
						<li><a href="docs/TPR Executive User Manual.pdf" target="_blank" >Portal User Manual</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="navigate todo-menu" >Todo ('.$todo.')</a><span class="hidden">'.json_encode($proarray).'</span>
					<ul class="menu-item">
						<li><a href="me/opportunities/'.$student['student']['stdid'].'/'.$student['batch']['btname'].'/" class="ui" >Opportunities ('.$opptodo.')</a></li>
						<li><a href="'.$student['person']['name'].'/experiences/'.$student['student']['stdid'].'/'.$student['batch']['btname'].'/" class="ui" >Experiences ('.$exptodo.')</a></li>
						<li><a href="student/'.$student['person']['username'].'/" class="ui" >Profile ('.$protodo.')</a></li>
						<li><a href="student/'.$student['person']['username'].'/" class="ui" >Remarks ('.$remtodo.')</a></li>
					</ul>
				</li>
			</ul>';
		else
			$STATEMENU .= '
			<ul class="hover-menu vertical fright">
				<li><a href="filter/" class="ui people-menu">Filter</a></li>
				<li>
					<a href="#" class="navigate portal-menu" >Portal</a>
					<ul class="menu-item">
						<li><a href="docs/TPR Executive User Manual.pdf" target="_blank" >Portal User Manual</a></li>
					</ul>
				</li>
			</ul>';
		
		$TPR = $memory['message']['btadmin'];
		$PORTAL = 'student';
	}
	else {
		/**
		 *	@launch Person Find
		**/
		$memory = Snowblozm::run(array(
			'service' => 'invoke.launch.workflow.workflow',
			'message' => array(
				'service' => 'executive.company.find.workflow'
			)
		), $memory);
		//echo json_encode($memory);exit;
		/**
		 *	@launch Read response
		**/
		if($memory['valid'] && $memory['message']['valid']){
			$company = $memory['message'];
			$comid = $company['comid'];
		
			//FIRESPARK_SI_DATA_URL_run.php_DATA_service=person&id=379&name=tr4n2uil&_TYPE_json_REQUEST_POST
			$HTML .= '
				<script type="text/javascript">
					Snowblozm.Registry.save("FIRESPARK_SI_DATA_URL_run.php_DATA_service=company&id='.$company['person']['username'].'&navigator=#/ui/company/'.$company['person']['username'].'/~/&_TYPE_json_REQUEST_POST", '.$memory['result'].');
					Executive.session.user = "'.$memory['user'].'";
					Executive.data.company = '.$comid.';
					Executive.data.username = "'.$company['person']['username'].'";
					Executive.data.dept = "";
				</script>';
		
			$memory['person'] = $company;
		
			$STATEMENU .=
			'<ul class="hover-menu vertical fright">
				<li>
					<a href="#" class="navigate portal-menu" >Portal</a>
					<ul class="menu-item">
						<li><a href="calendar/'.$company['person']['username'].'/" class="ui">Campus Visits</a></li>
						<li><a href="docs/TPR Executive User Manual.pdf" target="_blank" >Portal User Manual</a></li>
					</ul>
				</li>
				<li>
					<a href="company/'.$company['person']['username'].'/" class="ui" >'.$memory['user'].'<img src="storage/public/thumbnails/person/'.$memory['person']['person']['username'].'.png" alt="USER_THUMBNAIL" class="thumbhead" style="margin: -8px 0;"></a>
					<ul class="menu-item">
						<li><a href="company/'.$company['person']['username'].'/" class="ui" >Profile</a></li>
						<li><a href="identities/" class="ui">Identities</a></li>
						<li><a href="#/read/~/data/service=session&enc=get/ln/#login" class="launch">Sign Out</a></li>
					</ul>
				</li>
			</ul>';
			
			$PORTAL = 'company';
		}
		else {
			$STATEMENU .=
			'<ul class="hover-menu vertical fright">
				<li>
					<a href="#" class="navigate portal-menu" >Portal</a>
					<ul class="menu-item">
						<li><a href="docs/TPR Executive User Manual.pdf" target="_blank" >Portal User Manual</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="navigate" >'.$memory['user'].'<img src="storage/public/thumbnails/person.png" alt="USER_THUMBNAIL" class="thumbhead" style="margin: -8px 0;"></a>
					<ul class="menu-item">
						<li><a href="#/view/#register/" class="navigate">Register</a></li>
						<li><a href="#/view/#verify/" class="navigate">Verify</a></li>
						<li><a href="#/read/~/data/service=session&enc=get/ln/#login" class="launch">Sign Out</a></li>
					</ul>
				</li>
			</ul>';
			
			$SHTML.= file_get_contents('ui/html/login.html');
			$PORTAL = 'register';
		}
	}
	
?>