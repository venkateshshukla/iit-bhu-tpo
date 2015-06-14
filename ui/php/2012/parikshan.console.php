
<?php 
	
	require_once(AYROOT. 'ui/php/2012/parikshan.conf.php');
	
	class Console {
		private $memory;
		
		public function __construct($keyid, $user, $config){
			$this->memory = $config;
			$this->memory['keyid'] = $keyid;
			$this->memory['user'] = $user;
		}
		
		public function tiles(){
			$result = <<<HEAD
			<ul class="hover-menu horizontal side ui-2012-parikshan">
				<img src="ui/img/poster/parikshan_left.png" style="height: 40px; margin-left: 10px;"><br /><br />
				<span class="event-portal">
HEAD;
			
			$LIVE = false;
			$BEFORE = $AFTER = '';
			
			if(Parikshan::$live){
				$key = Parikshan::$live;
				$round = Parikshan::$rounds[Parikshan::$live];
				$name = $round['name'];
				$time = $round['end'];
				$feedback = $round['feedback'] ? '<li><a class="ui" href="2012/feedback/'.$round['feedback'].'/Parikshan Trial Round/">Feedback</a></li>' : '';
				
				$result .= <<<TILE
				<span class="tilehead">$name</span>
				<li id="parikshan-live-round">
					<a href="#" class="launch"><span class="small">Ends in</span><br /><span class="timer"></span></a>
				</li>
				<li>
					<a class="navigate" href="!/view/#$key-questions/">Questions</a>
				</li>
				$feedback
				<script type="text/javascript">
					$(document).ready(function(){
						window.setTimeout(function(){
							Adhyayan.core.helper.portalTimer('#parikshan-live-round .timer', $time*1000, 'Round Ended', ' ', function(){
								if($('#$key-questions-form form').length){
									var time = new Date().getTime();
									if(confirm('The round has ended. Your answers shall now be submitted automatically. Continue ?')){
										var diff = new Date().getTime() - time;
										if(diff > 10*60*1000){
											alert('Sorry, the submission time has already ended.');
										}
										else {
											$('#$key-questions-form form').trigger('submit');
										}
									}
								}
							});
						}, 5000);
					});
				</script>
TILE;
			}
			else {
				if(Parikshan::$before){
					$key = Parikshan::$before;
					$round = Parikshan::$rounds[Parikshan::$before];
					$name = $round['name'];
					$time = $round['start'];
					//$feedback = $round['feedback'] ? '<li><a class="ui" href="2012/feedback/'.$round['feedback'].'/Parikshan Trial Round/">Feedback</a></li>' : '';
					
					$result .= <<<TILE
					<span class="tilehead">$name</span>
					<li id="parikshan-next-round">
						<a href="#" class="launch"><span class="small">Starts in</span><br /><span class="timer"></span></a>
					</li>
					<script type="text/javascript">
						$(document).ready(function(){
							window.setTimeout(function(){
								Adhyayan.core.helper.portalTimer('#parikshan-next-round .timer', $time*1000, 'Event Started', ' ');
							}, 5000);
						});
					</script>
TILE;
				}
				
				foreach(Parikshan::$after as $key){
					$round = Parikshan::$rounds[$key];
					$eid = $round['eid'];
					$name = $round['name'];
					$time = $round['start'];
					$scoreboard = $round['result'] ? '<li><a class="ui" href="2012/scoreboard/'.$eid.'/Parikshan Trial Round/">Scoreboard</a></li>' : '';
					$solutions = $round['solutions'] ? '<li><a class="navigate" href="!/view/#'.$key.'-solutions/">Solutions</a></li>' : '';
					$feedback = $round['feedback'] ? '<li><a class="ui" href="2012/feedback/'.$round['feedback'].'/Parikshan Trial Round/">Feedback</a></li>' : '';
					
					$result .= <<<TILE
					<span class="tilehead">$name</span>
					$scoreboard
					$solutions
					$feedback
TILE;
				}
			}
			
			$result .= <<<TAIL
						</span>
				<br /><br />
			</ul>
TAIL;
			return $result;
		}
		
		public function html(){
			$result = '';
			if(Parikshan::$live){
				$result .= $this->header(Parikshan::$live.'-questions', Parikshan::$rounds[Parikshan::$live]['name'].' Questions');
				$result .= $this->html_live(Parikshan::$live, Parikshan::$rounds[Parikshan::$live], $this->memory);
				$result .= $this->footer();
			}
			else {
				foreach(Parikshan::$after as $key){
					$round = Parikshan::$rounds[$key];
					$eid = $round['eid'];
					$name = $round['name'];
					
					if($round['solutions']){
						$result .= $this->header($key.'-solutions', $name.' Solutions');
						$result .= $this->html_after($key, $round, $this->memory);
						$result .= $this->footer();
					}
				}
			}
			
			return $result;
		}
		
		private function header($id, $title){
			return <<<HEADER
				<div id="$id" class="tile-content ui-2012-parikshan">
					<img src="ui/img/poster/parikshan_left.png" />
					<br />
					<p class="title-desc">A lot of us would like to move mountains, but few of us are willing to practice on small hills. -- Anonymous</p>
					<br />
					
					<p class="head">$title</p>
HEADER;
		}
		
		private function footer(){
			return <<<FOOTER
				</div>
FOOTER;
		}
		
		public function html_live($key, $round, $memory){
			if($memory['keyid'] == -1)
				return '<div class="error">You must be loggedin to take the trial round.</div>';
			
			$eid = $round['eid'];
			
			$service = array(
				'service' => 'transpera.relation.select.workflow',
				'args' => array('keyid'),
				'conn' => 'ayconn',
				'relation' => '`submissions`',
				'sqlprj' => '`subid`',
				'sqlcnd' => "where `eid`=$eid and `keyid`=\${keyid}",
				'type' => 'submission',
				'successmsg' => 'Your submission was successfully accepted. Thanks for participating',
				'output' => array('result' => 'submissions'),
				'ismap' => false
			);
			
			$result = Snowblozm::run($service, $memory);
			if($result['valid']){
				return '<div class="success">Your submission was successfully accepted. Thanks for participating.</div>';
			}
				
			$min = $round['questions']['min'];
			$total = $round['questions']['total'];
			
			$service = array(
				'service' => 'transpera.relation.select.workflow',
				'conn' => 'ayconn',
				'relation' => '`questions`',
				'sqlprj' => '`qstid`, `statement`, `option1`, `option2`, `option3`, `option4`',
				'sqlcnd' => "where `qstid`>$min order by `qstid` asc limit $total",
				'type' => 'question',
				'successmsg' => 'Questions information given successfully',
				'output' => array('result' => 'questions'),
				'ismap' => false
			);
			
			$result = Snowblozm::run($service, $memory);
			if(!$result['valid'])
				return '<div class="error">Error loading questions.</div>';
			
			$questions = $result['questions'];
			shuffle($questions);
			$time = Parikshan::$now;
			
			$result = <<<FHEAD
			<div id="$key-questions-form">
			<form action="run.php" method="post" class="navigate" id="_write:sel._$key-questions-form:ln._refresh">
				<input type="hidden" name="service" value="parikshan" />
				<div class="submission"><input type="submit" value="Evaluate Now" /><span class="status"></span></div>
				<br />
				<div id="$key-$time-questions" class="parikshan">
FHEAD;
			
			$qno = 0;
			$total = count($questions);
			
			foreach($questions as $row){
				$qno++;
				$qid = $row['qstid'];
				$question = $row['statement'];
				$opt1 = $row['option1'];
				$opt2 = $row['option2'];
				$opt3 = $row['option3'];
				$opt4 = $row['option4'];
				
				$button = ($qno == $total) ? '
					<input type="button" name="previous_'.$qno.'" onclick="return previous('.$qno.')" value ="Previous" />
					<input type="submit" value="Evaluate" />
				' : (($qno == 1) ? '
					<input type="button" name="next_'.$qno.'" onclick="return next('.$qno.')" value ="Next" />
					<input type="submit" value="Evaluate" />
					<p class="small">Questions Remaining : '.($total - $qno).'</p>
				' : '<input type="button" name="next_'.$qno.'" onclick="return next('.$qno.')" value ="Next" />
					<input type="button" name="previous_'.$qno.'" onclick="return previous('.$qno.')" value ="Previous" />
					<input type="submit" value="Evaluate" />
					<p class="small">Questions Remaining : '.($total - $qno).'</p>'
				);
				
				$result .= <<<ROW
				<div><table id="question-$qno" style="display:none;">
					<tbody>
						<tr ><td><input type="hidden" name="questions[]" value="$qid" /><p class="qno">Question #$qno</p></td></tr>
						<tr><td><div class="question">$question</div></td></tr>
						<tr><td><label class="opt"><input type="radio" name="q_$qid" value="1" />$opt1</label></td></tr>
						<tr><td><label class="opt"><input type="radio" name="q_$qid" value="2" />$opt2</label></td></tr>
						<tr><td><label class="opt"><input type="radio" name="q_$qid" value="3" />$opt3</label></td></tr>
						<tr><td><label class="opt"><input type="radio" name="q_$qid" value="4" />$opt4</label></td></tr>
						<tr><td><div class="tail">$button</div></td></tr>
					</tbody>
				</table></div>
ROW;
			}
			
			$result .= <<<FFOOT
				</div>
			</form>
			</div>
			<script type="text/javascript">
				var next = function(qno){
					$('#question-'+qno).hide();
					qno++;
					$('#question-'+qno).fadeIn(500);
					return false;
				}
				
				var previous = function(qno){
					$('#question-'+qno).hide();
					qno--;
					$('#question-'+qno).fadeIn(500);
					return false;
				}
				
				$(document).ready(function(){
					$('#question-1').fadeIn(500);
				});
			</script>
FFOOT;
			return $result;
		}
		
		public function html_after($key, $round, $memory){
			$eid = $round['eid'];
			$min = $round['questions']['min'];
			$total = $round['questions']['total'];
			
			$service = array(
				'service' => 'transpera.relation.select.workflow',
				'args' => array('keyid'),
				'conn' => 'ayconn',
				'relation' => '`questions`',
				'sqlprj' => "`qstid`, `statement`, `option1`, `option2`, `option3`, `option4`, `answer`, `explanation`, (select `data` from `submissions` where `keyid`=\${keyid} and `eid`=$eid and `pid`=`qstid`) as `data`",
				'sqlcnd' => "where `qstid`>$min order by `qstid` asc limit $total",
				'type' => 'question',
				'successmsg' => 'Questions information given successfully',
				'output' => array('result' => 'questions'),
				'ismap' => false
			);
			
			$result = Snowblozm::run($service, $memory);
			if(!$result['valid'])
				return '<div class="error">Error loading solutions.</div>';
			
			$questions = $result['questions'];
			
			$result = <<<FHEAD
			<div id="$key-questions-form">
				<div id="$key-questions" class="parikshan">
FHEAD;
			
			$qno = 0;
			$total = count($questions);
			
			foreach($questions as $row){
				$qno++;
				$qid = $row['qstid'];
				$question = $row['statement'];
				$opt1 = $row['option1'];
				$opt2 = $row['option2'];
				$opt3 = $row['option3'];
				$opt4 = $row['option4'];
				$ans = $row['answer'];
				$explanation = $row['explanation'];
				$data = $row['data'];
				
				$labels = array('normal', 'normal', 'normal', 'normal');
				$labels[$ans-1] = 'green';
				
				if($data && $data != $ans){
					$labels[$data-1] = 'red';
				}
				
				$label1 = $labels[0];
				$label2 = $labels[1];
				$label3 = $labels[2];
				$label4 = $labels[3];
				
				$button = ($qno == $total) ? '
					<input type="button" name="previous_'.$qno.'" onclick="return previous('.$qno.')" value ="Previous" />
				' : (($qno == 1) ? '
					<input type="button" name="next_'.$qno.'" onclick="return next('.$qno.')" value ="Next" />
					<p class="small">Questions Remaining : '.($total - $qno).'</p>
				' : '<input type="button" name="next_'.$qno.'" onclick="return next('.$qno.')" value ="Next" />
					<input type="button" name="previous_'.$qno.'" onclick="return previous('.$qno.')" value ="Previous" />
					<p class="small">Questions Remaining : '.($total - $qno).'</p>'
				);
				
				$result .= <<<ROW
				<div><table id="$key-question-$qno" style="display:none;">
					<tbody>
						<tr ><td><p class="qno">Question #$qno</p></td></tr>
						<tr><td><div class="question">$question</div></td></tr>
						<tr><td><label class="$label1">$opt1</label></td></tr>
						<tr><td><label class="$label2">$opt2</label></td></tr>
						<tr><td><label class="$label3">$opt3</label></td></tr>
						<tr><td><label class="$label4">$opt4</label></td></tr>
						<tr><td><div class="explanation">$explanation</div></td></tr>
						<tr><td><div class="tail">$button</div></td></tr>
					</tbody>
				</table></div>
ROW;
			}
			
			$result .= <<<FFOOT
				</div>
			</div>
			<script type="text/javascript">
				var next = function(qno){
					$('#$key-question-'+qno).hide();
					qno++;
					$('#$key-question-'+qno).fadeIn(500);
					return false;
				}
				
				var previous = function(qno){
					$('#$key-question-'+qno).hide();
					qno--;
					$('#$key-question-'+qno).fadeIn(500);
					return false;
				}
				
				$(document).ready(function(){
					$('#$key-question-1').fadeIn(500);
				});
			</script>
FFOOT;
			return $result;
		}
	}
	
?>
	