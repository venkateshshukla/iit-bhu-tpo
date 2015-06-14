<?php 
require_once(SBSERVICE);

/**
 *	@class StudentFilterWorkflow
 *	@desc Returns all student information by filter
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param gcgpa long int CGPA > [memory] optional default 0
 *	@param lcgpa long int CGPA <= [memory] optional default 10
 *	@param gssc long int X % > [memory] optional default 0
 *	@param lssc long int X % <= [memory] optional default 100
 *	@param ghsc long int XII % > [memory] optional default 0
 *	@param lhsc long int XII % <= [memory] optional default 100
 *	@param ggrad long int Graduation % > [memory] optional default 0
 *	@param lgrad long int Graduation % <= [memory] optional default 100
 *	@param gsgpa long int All SGPA > [memory] optional default 0
 *	@param lsgpa long int All SGPA <= [memory] optional default 10
 *	@param gjee long int JEE > [memory] optional default 0
 *	@param ljee long int JEE <= [memory] optional default 8000
 *	@param ggate long int GATE > [memory] optional default 0
 *	@param lgate long int GATE <= [memory] optional default 8000
 *	@param gback long int Arrears >= [memory] optional default 0
 *	@param lback long int Arrears < [memory] optional default 10
 *	@param category long int Category [memory] optional default array('General', 'OBC', 'SC', 'ST')
 *	@param placed long int Placement Status [memory] optional default 0 ( 0=All 1=Unplaced/MR 2=Placed 3=Ordinary 4=Dream 5=Super )
 *	@param dept string Department [memory] optional default false
 *	@param course string Course [memory] optional default array('btech', 'idd', 'mtech')
 *	@param year string Year [memory] optional default CURRENT_YEAR
 *	@param export boolean Is Export [memory] optional default false
 *	@param archive boolean Is Archive [memory] optional default false
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return students array Students information [memory]
 *	@return admin integer Is admin [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentFilterWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'gcgpa' => 0, 'lcgpa' => 10, 'gsgpa' => 0, 'lsgpa' => 10, 'gssc' => 0, 'lssc' => 100, 'ghsc' => 0, 'lhsc' => 100, 'ggrad' => 0, 'lgrad' => 100, 'gjee' => 0, 'ljee' => 8000, 'ggate' => 0, 'lgate' => 8000, 'gback' => 0, 'lback' => 10, 'category' => array('General', 'OBC', 'SC', 'ST'), 'placed' => 0, 'year' => CURRENT_YEAR, 'dept' => array('cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'), 'course' => array('btech', 'idd', 'mtech'), 'pgsz' => false, 'pgno' => 0, 'total' => false, 'export' => false, 'options' => false, 'filter' => false),
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$name = 'filter.'.$memory['user'];
		
		$args = $esc = array();
		$qry = '';
		if( $memory['filter'] ){
			//$qry = "g.`cgpa` >= \${gcgpa} and g.`cgpa` <= \${lcgpa} and (g.`sgpa1` = 0 or (g.`sgpa1` > \${gsgpa} and g.`sgpa1` <= \${lsgpa})) and (g.`sgpa2` = 0 or (g.`sgpa2` > \${gsgpa} and g.`sgpa2` <= \${lsgpa})) and (g.`sgpa3` = 0 or (g.`sgpa3` > \${gsgpa} and g.`sgpa3` <= \${lsgpa})) and (g.`sgpa4` = 0 or (g.`sgpa4` > \${gsgpa} and g.`sgpa4` <= \${lsgpa})) and (g.`sgpa5` = 0 or (g.`sgpa5` > \${gsgpa} and g.`sgpa5` <= \${lsgpa})) and (g.`sgpa6` = 0 or (g.`sgpa6` > \${gsgpa} and g.`sgpa6` <= \${lsgpa})) and (g.`sgpa7` = 0 or (g.`sgpa7` > \${gsgpa} and g.`sgpa7` <= \${lsgpa})) and (g.`sgpa8` = 0 or (g.`sgpa8` > \${gsgpa} and g.`sgpa8` <= \${lsgpa})) and (g.`sgpa9` = 0 or (g.`sgpa9` > \${gsgpa} and g.`sgpa9` <= \${lsgpa})) and (g.`sgpa10` = 0 or (g.`sgpa10` > \${gsgpa} and g.`sgpa10` <= \${lsgpa})) and g.`sscx` >= \${gssc} and g.`sscx` <= \${lssc} and g.`hscxii` >= \${ghsc} and g.`hscxii` <= \${lhsc} and ((b.`course` <> 'mtech' and g.`jee` >= \${gjee} and g.`jee` <= \${ljee}) or (g.`gradcent` >= \${ggrad} and g.`gradcent` <= \${lgrad} and g.`gate` >= \${ggate} and g.`gate` <= \${lgate})) and g.`backlogs` >= \${gback} and g.`backlogs` <= \${lback}";
			$qry = "g.`cgpa` >= \${gcgpa} and g.`cgpa` <= \${lcgpa} and (g.`sgpa1` = 0 or (g.`sgpa1` > \${gsgpa} and g.`sgpa1` <= \${lsgpa})) and (g.`sgpa2` = 0 or (g.`sgpa2` > \${gsgpa} and g.`sgpa2` <= \${lsgpa})) and (g.`sgpa3` = 0 or (g.`sgpa3` > \${gsgpa} and g.`sgpa3` <= \${lsgpa})) and (g.`sgpa4` = 0 or (g.`sgpa4` > \${gsgpa} and g.`sgpa4` <= \${lsgpa})) and (g.`sgpa5` = 0 or (g.`sgpa5` > \${gsgpa} and g.`sgpa5` <= \${lsgpa})) and (g.`sgpa6` = 0 or (g.`sgpa6` > \${gsgpa} and g.`sgpa6` <= \${lsgpa})) and (g.`sgpa7` = 0 or (g.`sgpa7` > \${gsgpa} and g.`sgpa7` <= \${lsgpa})) and (g.`sgpa8` = 0 or (g.`sgpa8` > \${gsgpa} and g.`sgpa8` <= \${lsgpa})) and (g.`sgpa9` = 0 or (g.`sgpa9` > \${gsgpa} and g.`sgpa9` <= \${lsgpa})) and (g.`sgpa10` = 0 or (g.`sgpa10` > \${gsgpa} and g.`sgpa10` <= \${lsgpa})) and g.`sscx` >= \${gssc} and g.`sscx` <= \${lssc} and g.`hscxii` >= \${ghsc} and g.`hscxii` <= \${lhsc} and (b.`course` <> 'mtech' or (g.`gradcent` >= \${ggrad} and g.`gradcent` <= \${lgrad})) and g.`backlogs` >= \${gback} and g.`backlogs` <= \${lback}";
			array_push($args, 'gcgpa', 'lcgpa', 'gsgpa', 'lsgpa', 'gssc', 'lssc', 'ghsc', 'lhsc', 'ggrad', 'lgrad', 'gjee', 'ljee', 'ggate', 'lgate', 'gback', 'lback' );
			
			switch($memory['placed']){
				case 1:
					$qry .= " and t.`mr`='' and t.`ordinary`='' and t.`dream`='' and t.`super`='' ";
					break;
				case 2:
					$qry .= " and (t.`mr`<>'' or t.`ordinary`<>'' or t.`dream`<>'' or t.`super`<>'') ";
					break;
				case 3:
					$qry .= " and (t.`ordinary`='' and t.`dream`='' and t.`super`='') ";
					break;
				case 4:
					$qry .= " and (t.`dream`='' and t.`super`='') ";
					break;
				case 5:
					$qry .= " and (t.`super`='') ";
					break;
				case 0:
				default:
					break;
			}
			
			if(is_numeric($memory['year'])){
				$qry .= " and b.`year`=\${year} ";
				array_push($args, 'year');
			}
			
			// !ALERT: No MySQL escape here
			if($memory['category']){
				$memory['category'] = "'".implode( "','", $memory['category'] )."'";
				$qry .= " and s.`category` in (".$memory['category'].") ";
				//array_push($args, 'category');
				//array_push($esc, 'category');
			}
			
			// !ALERT: No MySQL escape here
			if($memory['dept']){
				$memory['dept'] = "'".implode( "','", $memory['dept'] )."'";
				$qry .= " and b.`dept` in (".$memory['dept'].") ";
				//array_push($args, 'dept');
				//array_push($esc, 'dept');
			}
			
			// !ALERT: No MySQL escape here
			if($memory['course']){
				$memory['course'] = "'".implode( "','", $memory['course'] )."'";
				$qry .= " and b.`course` in (".$memory['course'].") ";
				//array_push($args, 'course');
				//array_push($esc, 'course');
			}
		
		}
		else {
			$qry = 'false';
		}
		
		if($memory['export'] == 'data'){
			$rel = '`students` s, `grades` g, `slots` t, `persons` p, `batches` b';
			$prj = "s.`name`, s.`rollno`, 
			(case b.`course` when 'btech' then 'B Tech' when 'idd' then (case b.`dept` when 'apc' then 'IMD' when 'apm' then 'IMD' when 'app' then 'IMD' else 'IDD' end) when 'mtech' then 'M Tech' else '' end) as `course`, s.`specialization`,
			(case b.`dept` when 'cer' then 'Ceramic Engineering' when 'che' then 'Chemical Engineering' when 'civ' then 'Civil Engineering' when 'cse' then 'Computer Engineering' when 'eee' then 'Electrical Engineering' when 'ece' then 'Electronics Engineering' when 'mec' then 'Mechanical Engineering' when 'met' then 'Metallurgical Engineering' when 'min' then 'Mining Engineering' when 'phe' then 'Pharmaceutical Engineering' when 'apc' then 'Industrial Chemistry' when 'apm' then 'Mathematics and Computing' when 'app' then 'Engineering Physics' when 'bce' then 'Bio-Chemical Engineering' when 'bme' then 'Bio-Medical Engineering' when 'mst' then 'Material Science & Technology' else '' end) as `dept`, b.`year`, s.`email`, p.`phone`, p.`address`, s.`resphone`, s.`resaddress`, s.`city`, p.`dateofbirth`, p.`gender`, s.`category`, s.`language`, s.`father`, s.`foccupation`, s.`mother`, s.`moccupation`, g.`cgpa`, g.`sscx`, g.`sscyear`, g.`sscboard`, g.`hscxii`, g.`hscyear`, g.`hscboard`, g.`gradcent`, g.`gradyear`, g.`gradboard`, g.`jee`, g.`gate`, g.`sgpa1`, g.`sgpa2`, g.`sgpa3`, g.`sgpa4`, g.`sgpa5`, g.`sgpa6`, g.`sgpa7`, g.`sgpa8`, g.`sgpa9`, g.`sgpa10`, g.`ygpa1`, g.`ygpa2`, g.`ygpa3`, g.`ygpa4`, g.`ygpa5`, g.`backlogs`, t.`mr`, t.`ordinary`, t.`dream`, t.`super`, s.`remarks`";
			$cnd = "where $qry and s.`batchid`=b.`batchid` and s.`grade`=g.`gradeid` and s.`slot`=t.`slotid` and p.`pnid`=s.`stdid` order by b.`year` desc, b.`dept` asc, b.`course` asc, s.`ustatus` asc, s.`rollno` asc";
		}
		elseif($memory['export'] == 'resumes'){
			$rel = '`students` s, `grades` g, `slots` t, `batches` b, `directories` d, `files` f';
			$prj = "concat(s.`rollno`, '_', s.`name`, '_Resume', substring(f.`name`, locate( '.', f.`name` )) ) as `asname`, d.`path` as `filepath`, f.`filename`, s.`resume` as `fresume`";
			$cnd = "where $qry and s.`batchid`=b.`batchid` and s.`slot`=t.`slotid` and s.`grade`=g.`gradeid` and f.`fileid`=s.`resume` and d.`dirid`=b.`resumes`";
		}
		elseif($memory['options'] == 'incomplete') {
			$qry .= " and ( s.`name` regexp '[0123456789]+' or length( p.`phone` ) <> 10 or p.`address` = '' or length( s.`resphone` ) <> 10 or s.`resaddress` = '' or s.`city`='' or p.`dateofbirth`='' or p.`dateofbirth`='0000-00-00' or p.`gender`='N' or s.`category`='' or s.`language`='' or s.`father`='' or s.`foccupation`='' or s.`mother`='' or s.`moccupation`='' or g.`cgpa`=0 or g.`sscx`=0 or g.`sscyear` = '' or g.`sscboard`='' or g.`hscxii`=0 or g.`hscyear`='' or g.`hscboard`=''  or ( b.`course`='mtech' and ( g.`gradcent`=0 or g.`gradyear`='' or g.`gradboard`='' or g.`gate`='' ) ) or ( b.`course` <> 'mtech' and g.`jee`='' ) ) ";
			$rel = '`students` s, `grades` g, `slots` t, `batches` b, `persons` p';
			$prj = 's.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, s.`category`, b.`btname`, b.`year`, g.`cgpa`, g.`sscx`, g.`hscxii`, g.`gradcent`, g.`jee`, g.`gate`, g.`backlogs`, t.`mr`, t.`ordinary`, t.`dream`, t.`super`';
			$cnd = "where $qry and s.`slot`=t.`slotid` and s.`grade`=g.`gradeid` and b.`batchid`=s.`batchid` and p.`pnid`=s.`stdid` order by b.`year` desc, b.`dept` asc, b.`course` asc, s.`ustatus` asc, s.`rollno` asc";
		}
		else {
			$rel = '`students` s, `grades` g, `slots` t, `batches` b';
			$prj = 's.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, s.`category`, b.`btname`, b.`year`, g.`cgpa`, g.`sscx`, g.`hscxii`, g.`gradcent`, g.`jee`, g.`gate`, g.`backlogs`, t.`mr`, t.`ordinary`, t.`dream`, t.`super`';
			$cnd = "where $qry and s.`slot`=t.`slotid` and s.`grade`=g.`gradeid` and b.`batchid`=s.`batchid` order by b.`year` desc, b.`dept` asc, b.`course` asc, s.`ustatus` asc, s.`rollno` asc";
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'id' => STUDENT_PORTAL_ID
		),
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => $args,
			'conn' => 'exconn',
			'relation' => $rel,
			'type' => 'student',
			'sqlprj' => $prj,
			'sqlcnd' => $cnd,
			'successmsg' => 'Students information given successfully',
			'escparam' => $esc,
			'check' => false,
			//'lsttrack' => true,
			'output' => array('result' => 'students'),
			'ismap' => !$memory['export'],
			'mapkey' => 'stdid',
			'mapname' => 'filter',
		));
		
		if($memory['export'] == 'data'){
			array_push($workflow, array(
				'service' => 'cbcore.data.export.service',
				'input' => array('data' => 'students'),
				'type' => 'csv',
				'default' => '"Student Name","Roll No","Course","Specialization","Department","Year of Passing","Email","Phone","Address (Current)","Phone (Residential)","Address (Permanent)","City","Date of Birth","Gender","Category","Mother Tongue","Father\'s Name","Father\'s Occupation","Mother\'s Name","Mother\'s Occupation","CGPA","X %","X Year of Passing","X Board","XII %","XII Year of Passing","XII Board","Graduation %","Graduation Year of Passing","Graduation Board","JEE AIR","GATE AIR","SGPA I","SGPA II","SGPA III","SGPA IV","SGPA V","SGPA VI","SGPA VII","SGPA VIII","SGPA IX","SGPA X","YGPA I","YGPA II","YGPA III","YGPA IV","YGPA V","Arrears","MR Slot","Ordinary Slot","Dream Slot","Super Dream Slot","Remarks"'."\r\n",
				'filename' => $name.'.csv',
				'output' => array('result' => 'csv')
			),
			array(
				'service' => 'storage.file.download.service',
				'filepath' => 'storage/private/exports/',
				'filename' => $name.'.csv',
				'mime' => 'application/vnd.ms-excel'
			));
		}
		
		if($memory['export'] == 'resumes'){
			array_push($workflow, array(
				'service' => 'storage.file.archive.service',
				'input' => array('filelist' => 'students'),
				'directory' => 'storage/private/exports/',
				'filename' => $name.'.zip',
			),
			array(
				'service' => 'storage.file.download.service',
				'filepath' => 'storage/private/exports/',
				'filename' => $name.'.zip',
				'mime' => 'application/zip'
			));
		}
		
		$memory[ 'admin' ] = 1;
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('students', 'year', 'course', 'dept', 'gcgpa', 'lcgpa', 'gsgpa', 'lsgpa', 'gssc', 'lssc', 'ghsc', 'lhsc', 'ggrad', 'lgrad', 'gjee', 'ljee', 'ggate', 'lgate', 'gback', 'lback', 'category', 'placed', 'options', 'admin', 'total', 'pgno', 'pgsz');
	}
	
}

?>