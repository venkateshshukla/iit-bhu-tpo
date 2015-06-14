<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionListWorkflow
 *	@desc Returns all selections information in post
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param shlstid/id long int Shortlist/Visit ID [memory] optional default 0
 *	@param shlstname/name string Shortlist name [memory] optional default ''
 *	@param export boolean Is Export [memory] optional default false
 *	@param archive boolean Is Archive [memory] optional default false
 *	@param me boolean Is Opportunity [memory] optional default false
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return selections array Selections information [memory]
 *	@return shlstid long int Shortlist/Visit ID [memory]
 *	@return shlstname string Shortlist Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'shlstid' => false, 'id' => 0, 'shlstname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true, 'export' => false, 'archive' => false, 'me' => false),
			'set' => array('id', 'name', 'me')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['shlstid'] = $memory['shlstid'] ? $memory['shlstid'] : $memory['id'];
		$memory['shlstname'] = $memory['shlstname'] ? $memory['shlstname'] : $memory['name'];
		
		$qry = "l.`selid` in \${list}";
		
		if($memory['me']){
			$qry = "s.`stdid`=\${shlstid}";
			$rel = '`selections` l, `students` s, `visits` v';
			$prj = "l.`selid`, l.`visitid`, l.`resume`, l.`stage`, l.`name`, l.`batch`, s.`stdid`, s.`username`, v.`comuser`, v.`comid`, v.`vtype`, v.`year`, v.`visitdate`, (case (select `course` from `batches` where `btname`=l.`batch`) when 'btech' then v.`bpackage` when 'idd' then v.`ipackage` when 'mtech' then v.`mpackage` else '' end) as `package`";
			$cnd = "where $qry and l.`owner`=s.`owner` and v.`visitid`=l.`visitid` order by l.`selid` desc";
		}
		elseif($memory['export']){
			$rel = '`selections` l, `students` s, `grades` g, `persons` p, `batches` b';
			$prj = "l.`stage`, s.`name`, s.`rollno`, 
			(case b.`course` when 'btech' then 'B Tech' when 'idd' then 'IDD / IMD' when 'mtech' then 'M Tech' else '' end) as `course`, 
			(case b.`dept` when 'cer' then 'Ceramic Engineering' when 'che' then 'Chemical Engineering' when 'civ' then 'Civil Engineering' when 'cse' then 'Computer Engineering' when 'eee' then 'Electrical Engineering' when 'ece' then 'Electronics Engineering' when 'mec' then 'Mechanical Engineering' when 'met' then 'Metallurgical Engineering' when 'min' then 'Mining Engineering' when 'phe' then 'Pharmaceutical Engineering' when 'apc' then 'Industrial Chemistry' when 'apm' then 'Mathematics and Computing' when 'app' then 'Engineering Physics' when 'bce' then 'Bio-Chemical Engineering' when 'bme' then 'Bio-Medical Engineering' when 'mst' then 'Material Science & Technology' else '' end) as `dept`, b.`year`, s.`email`, p.`phone`, p.`address`, s.`resphone`, s.`resaddress`, p.`dateofbirth`, p.`gender`, s.`category`, s.`language`, s.`father`, s.`foccupation`, s.`mother`, s.`moccupation`, g.`cgpa`, g.`sscx`, g.`sscyear`, g.`hscxii`, g.`hscyear`, g.`jee`, g.`gate`, s.`graddetails`, g.`sgpa1`, g.`sgpa2`, g.`sgpa3`, g.`sgpa4`, g.`sgpa5`, g.`sgpa6`, g.`sgpa7`, g.`sgpa8`, g.`sgpa9`, g.`sgpa10`, g.`ygpa1`, g.`ygpa2`, g.`ygpa3`, g.`ygpa4`, g.`ygpa5`";
			$cnd = "where $qry and l.`owner`=s.`owner` and s.`grade`=g.`gradeid` and p.`pnid`=s.`stdid` and b.`batchid`=l.`batchid`";
		}
		elseif($memory['archive']){
			$rel = '`selections` l, `students` s, `directories` d, `files` f';
			$prj = "concat(s.`rollno`, '_', s.`name`, '_Resume.pdf') as `asname`, d.`path` as `filepath`, f.`filename`, (case l.`resume` when 0 then s.`resume` else l.`resume` end) as `fresume`";
			$cnd = "where $qry and l.`owner`=s.`owner` and f.`fileid`=(case l.`resume` when 0 then s.`resume` else l.`resume` end) and d.`dirid`=l.`resdir`";
		}
		else {
			$rel = '`selections` l, `students` s, `grades` g';
			$prj = 'l.`selid`, l.`visitid`, l.`resume`, l.`stage`, l.`name` as `lname`, l.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, g.`cgpa`, g.`sscx`, g.`hscxii`';
			$cnd = "where $qry and l.`owner`=s.`owner` and s.`grade`=g.`gradeid`";
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.list.workflow',
			'args' => array('shlstid'),
			'input' => array('id' => 'shlstid', 'shlstname' => 'shlstname'),
			'conn' => 'exconn',
			'relation' => $rel,
			'type' => 'selection',
			'sqlprj' => $prj,
			'sqlcnd' => $cnd,
			'successmsg' => 'Selections information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'selections'),
			'action' => 'list',
			'ismap' => !$memory['export'] && !$memory['archive'],
			'mapkey' => 'selid',
			'mapname' => 'selection',
			'saction' => 'add'
		));
		
		if($memory['export']){
			array_push($workflow, array(
				'service' => 'cbcore.data.export.service',
				'input' => array('data' => 'selections'),
				'type' => 'csv',
				'default' => "Selection Stage,Student Name,Roll No,Course,Department,Year of Passing,Email,Phone,Address (Current),Phone (Residential),Address (Permanent),Date of Birth,Gender,Category,Mother Tongue,Father's Name,Occupation,Mother's Name,Occupation,CGPA,X %,X Year of Passing,XII %,XII Year of Passing,JEE AIR, GATE AIR,Graduation Details,SGPA I,SGPA II,SGPA III,SGPA IV,SGPA V,SGPA VI,SGPA VII,SGPA VIII,SGPA IX,SGPA X,YGPA I,YGPA II,YGPA III,YGPA IV,YGPA V\r\n",
				'filename' => 'Selections.'.$memory['shlstname'].'.'.$memory['user'].'.csv',
				'output' => array('result' => 'csv')
			),
			array(
				'service' => 'storage.file.download.service',
				'filepath' => 'storage/private/exports/',
				'filename' => 'Selections.'.$memory['shlstname'].'.'.$memory['user'].'.csv',
				'mime' => 'application/vnd.ms-excel'
			));
		}
		
		if($memory['archive']){
			array_push($workflow, array(
				'service' => 'storage.file.archive.service',
				'input' => array('filelist' => 'selections'),
				'directory' => 'storage/private/exports/',
				'filename' => 'Selection.'.$memory['shlstname'].'.'.$memory['user'].'.zip',
			),
			array(
				'service' => 'storage.file.download.service',
				'filepath' => 'storage/private/exports/',
				'filename' => 'Selection.'.$memory['shlstname'].'.'.$memory['user'].'.zip',
				'mime' => 'application/zip'
			));
		}
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['uiadmin'] = $memory['admin'] || $memory['padmin'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selections', 'shlstid', 'shlstname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'uiadmin');
	}
	
}

?>