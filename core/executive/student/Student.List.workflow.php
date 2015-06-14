<?php 
require_once(SBSERVICE);

/**
 *	@class StudentListWorkflow
 *	@desc Returns all students information in portal container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param batchid/id long int Portal ID [memory] optional default STUDENT_PORTAL_ID
 *	@param btname/name string Portal name [memory] optional default ''
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@param filter string Filter [memory] optional default false
 *	@param year string Year [memory] optional default false
 *	@param course string course [memory] optional default false
 *
 *	@return students array Students information [memory]
 *	@return batchid long int Portal ID [memory]
 *	@return btname string Portal name [memory]
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
class StudentListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('batchid' => false, 'id' => STUDENT_PORTAL_ID, 'btname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true, 'export' => false, 'archive' => false),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['batchid'] = $memory['batchid'] ? $memory['batchid'] : $memory['id'];
		$memory['btname'] = $memory['btname'] ? $memory['btname'] : $memory['name'];
		
		$name = 'Students-'.$memory['btname'].'.'.$memory['user'];
		
		if($memory['export']){
			$rel = '`students` s, `grades` g, `slots` t, `persons` p, `batches` b';
			$prj = "s.`name`, s.`rollno`, 
			(case b.`course` when 'btech' then 'B Tech' when 'idd' then (case b.`dept` when 'apc' then 'IMD' when 'apm' then 'IMD' when 'app' then 'IMD' else 'IDD' end) when 'mtech' then 'M Tech' else '' end) as `course`, s.`specialization`,
			(case b.`dept` when 'cer' then 'Ceramic Engineering' when 'che' then 'Chemical Engineering' when 'civ' then 'Civil Engineering' when 'cse' then 'Computer Engineering' when 'eee' then 'Electrical Engineering' when 'ece' then 'Electronics Engineering' when 'mec' then 'Mechanical Engineering' when 'met' then 'Metallurgical Engineering' when 'min' then 'Mining Engineering' when 'phe' then 'Pharmaceutical Engineering' when 'apc' then 'Industrial Chemistry' when 'apm' then 'Mathematics and Computing' when 'app' then 'Engineering Physics' when 'bce' then 'Bio-Chemical Engineering' when 'bme' then 'Bio-Medical Engineering' when 'mst' then 'Material Science & Technology' else '' end) as `dept`, b.`year`, s.`email`, p.`phone`, p.`address`, s.`resphone`, s.`resaddress`, s.`city`, p.`dateofbirth`, p.`gender`, s.`category`, s.`language`, s.`father`, s.`foccupation`, s.`mother`, s.`moccupation`, g.`cgpa`, g.`sscx`, g.`sscyear`, g.`sscboard`, g.`hscxii`, g.`hscyear`, g.`hscboard`, g.`gradcent`, g.`gradyear`, g.`gradboard`, g.`jee`, g.`gate`, g.`sgpa1`, g.`sgpa2`, g.`sgpa3`, g.`sgpa4`, g.`sgpa5`, g.`sgpa6`, g.`sgpa7`, g.`sgpa8`, g.`sgpa9`, g.`sgpa10`, g.`ygpa1`, g.`ygpa2`, g.`ygpa3`, g.`ygpa4`, g.`ygpa5`, g.`backlogs`, t.`mr`, t.`ordinary`, t.`dream`, t.`super`, s.`remarks`";
			$cnd = "where s.`stdid` in \${list} $qry and s.`batchid`=b.`batchid` and s.`grade`=g.`gradeid` and s.`slot`=t.`slotid` and p.`pnid`=s.`stdid` order by b.`year` desc, b.`dept` asc, b.`course` asc, s.`ustatus` asc, s.`rollno` asc";
		}
		elseif($memory['archive']){
			$rel = '`students` s, `batches` b, `directories` d, `files` f';
			$prj = "concat(s.`rollno`, '_', s.`name`, '_Resume', substring(f.`name`, locate( '.', f.`name` )) ) as `asname`, d.`path` as `filepath`, f.`filename`, s.`resume` as `fresume`";
			$cnd = "where s.`stdid` in \${list} $qry and s.`batchid`=b.`batchid` and f.`fileid`=s.`resume` and d.`dirid`=b.`resumes`";
		}
		else {
			$rel = '`students` s';
			$prj =  's.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, s.`specialization`, s.`category`, s.`resphone`, s.`resaddress`, s.`city`, s.`resume`, s.`home`, s.`interests`, s.`remarks`, s.`ustatus`';
			$cnd = "where s.`stdid` in \${list} order by s.`ustatus`, s.`rollno`";
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'batchid', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => $rel,
			'sqlprj' => $prj,
			'sqlcnd' => $cnd,
			'type' => 'person',
			'successmsg' => 'Students information given successfully',
			'output' => array('entities' => 'students'),
			'istate' => false, 
			'ismap' => !$memory['export'] && !$memory['archive'],
			'action' => $memory['export'] || $memory['archive'] ? 'add' : 'list',
			'mapkey' => 'stdid',
			'mapname' => 'student'
		));
		
		if($memory['export']){
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
		
		if($memory['archive']){
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
		
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('students', 'id', 'batchid', 'btname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>