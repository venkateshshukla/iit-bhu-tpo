<?php 
require_once(SBSERVICE);

/**
 *	@class BatchListWorkflow
 *	@desc Returns all batches information in portal
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param portalid/id long int Portal ID [memory] optional default STUDENT_PORTAL_ID
 *	@param plname/name string Portal name [memory] optional default ''
 *	@param filter string Filter [memory] optional default false
 *	@param year string Year [memory] optional default false
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return batches array Batchs information [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal Name [memory]
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
class BatchListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'portalid' => false, 'id' => STUDENT_PORTAL_ID, 'plname' => false, 'name' => 'IIT (BHU) Varanasi', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true, 'filter' => false, 'year' => false, 'course' => false, 'export' => false, 'archive' => false),
			'set' => array('filter', 'year', 'course', 'id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['portalid'] = $memory['portalid'] ? $memory['portalid'] : $memory['id'];
		$memory['plname'] = $memory['plname'] ? $memory['plname'] : $memory['name'];
		$memory['dept'] = false;
		
		$args = $esc = array();
		$qry = '';
		if($memory['filter']){
			if(is_numeric($memory['filter'])){
				$memory['dept'] = false;
				if(in_array($memory['year'], array('cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'))){
					$memory['dept'] = $memory['year'];
					$qry .= "and b.`dept`='\${dept}' ";
					array_push($args, 'dept');
					array_push($esc, 'dept');
				}
				elseif(in_array($memory['year'], array('btech', 'idd', 'mtech'))){
					$memory['course'] = $memory['year'];
					$qry .= "and b.`course`='\${course}' ";
					array_push($args, 'course');
					array_push($esc, 'course');
				}
				
				$memory['year'] = $memory['filter'];
				$qry .= "and b.`year`=\${year} ";
				array_push($args, 'year');
				
				if(in_array($memory['course'], array('btech', 'idd', 'mtech'))){
					$memory['course'] = $memory['course'];
					$qry .= "and b.`course`='\${course}' ";
					array_push($args, 'course');
					array_push($esc, 'course');
				}
			}
			elseif(in_array($memory['filter'], array('cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'))){
				$memory['dept'] = $memory['filter'];
				$qry = "and b.`dept`='\${dept}' ";
				array_push($args, 'dept');
				array_push($esc, 'dept');
				
				if(in_array($memory['year'], array('btech', 'idd', 'mtech'))){
					$memory['course'] = $memory['year'];
					$memory['year'] = false;
					$qry .= "and b.`course`='\${course}' ";
					array_push($args, 'course');
					array_push($esc, 'course');
				}
				elseif(is_numeric($memory['year'])){
					$qry .= " and b.`year`=\${year} ";
					array_push($args, 'year');
				}
			}
			elseif(in_array($memory['filter'], array('btech', 'idd', 'mtech'))){
				$memory['course'] = $memory['filter'];
				$qry .= "and b.`course`='\${course}' ";
				array_push($args, 'course');
				array_push($esc, 'course');
			}
		}
		else {
			$qry .= " and false";
		}
		
		$name = 'Students-'.$memory['year'].'-'.$memory['dept'].'-'.$memory['course'].'.'.$memory['user'];
		
		if($memory['export']){
			$rel = '`students` s, `grades` g, `slots` t, `persons` p, `batches` b';
			$prj = "s.`name`, s.`rollno`, 
			(case b.`course` when 'btech' then 'B Tech' when 'idd' then (case b.`dept` when 'apc' then 'IMD' when 'apm' then 'IMD' when 'app' then 'IMD' else 'IDD' end) when 'mtech' then 'M Tech' else '' end) as `course`, s.`specialization`,
			(case b.`dept` when 'cer' then 'Ceramic Engineering' when 'che' then 'Chemical Engineering' when 'civ' then 'Civil Engineering' when 'cse' then 'Computer Engineering' when 'eee' then 'Electrical Engineering' when 'ece' then 'Electronics Engineering' when 'mec' then 'Mechanical Engineering' when 'met' then 'Metallurgical Engineering' when 'min' then 'Mining Engineering' when 'phe' then 'Pharmaceutical Engineering' when 'apc' then 'Industrial Chemistry' when 'apm' then 'Mathematics and Computing' when 'app' then 'Engineering Physics' when 'bce' then 'Bio-Chemical Engineering' when 'bme' then 'Bio-Medical Engineering' when 'mst' then 'Material Science & Technology' else '' end) as `dept`, b.`year`, s.`email`, p.`phone`, p.`address`, s.`resphone`, s.`resaddress`, s.`city`, p.`dateofbirth`, p.`gender`, s.`category`, s.`language`, s.`father`, s.`foccupation`, s.`mother`, s.`moccupation`, g.`cgpa`, g.`sscx`, g.`sscyear`, g.`sscboard`, g.`hscxii`, g.`hscyear`, g.`hscboard`, g.`gradcent`, g.`gradyear`, g.`gradboard`, g.`jee`, g.`gate`, g.`sgpa1`, g.`sgpa2`, g.`sgpa3`, g.`sgpa4`, g.`sgpa5`, g.`sgpa6`, g.`sgpa7`, g.`sgpa8`, g.`sgpa9`, g.`sgpa10`, g.`ygpa1`, g.`ygpa2`, g.`ygpa3`, g.`ygpa4`, g.`ygpa5`, g.`backlogs`, t.`mr`, t.`ordinary`, t.`dream`, t.`super`, s.`remarks`";
			$cnd = "where b.`batchid` in \${list} $qry and s.`batchid`=b.`batchid` and s.`grade`=g.`gradeid` and s.`slot`=t.`slotid` and p.`pnid`=s.`stdid` order by b.`year` desc, b.`dept` asc, b.`course` asc, s.`ustatus` asc, s.`rollno` asc";
		}
		elseif($memory['archive']){
			$rel = '`students` s, `batches` b, `directories` d, `files` f';
			$prj = "concat(s.`rollno`, '_', s.`name`, '_Resume', substring(f.`name`, locate( '.', f.`name` )) ) as `asname`, d.`path` as `filepath`, f.`filename`, s.`resume` as `fresume`";
			$cnd = "where b.`batchid` in \${list} $qry and s.`batchid`=b.`batchid` and f.`fileid`=s.`resume` and d.`dirid`=b.`resumes`";
		}
		else {
			$rel = '`batches` b';
			$prj = "b.`batchid`, b.`btname`, b.`resumes`, b.`notes`, b.`dept`, b.`course`,b.`year`";
			$cnd = "where b.`batchid` in \${list} $qry order by b.`year` desc, b.`dept` asc, b.`course` asc";
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'args' => $args,
			'conn' => 'exconn',
			'relation' => $rel,
			'type' => 'batch',
			'sqlprj' => $prj,
			'sqlcnd' => $cnd,
			'escparam' => $esc,
			'successmsg' => 'Batches information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'batches'),
			'ismap' => !$memory['export'] && !$memory['archive'],
			'action' => $memory['export'] || $memory['archive'] ? 'add' : 'list',
			'mapkey' => 'batchid',
			'mapname' => 'batch',
			'saction' => 'add'
		));
		
		if($memory['export']){
			array_push($workflow, array(
				'service' => 'cbcore.data.export.service',
				'input' => array('data' => 'batches'),
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
				'input' => array('filelist' => 'batches'),
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
		return array('batches', 'id', 'portalid', 'plname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'dept', 'year', 'course');
	}
	
}

?>