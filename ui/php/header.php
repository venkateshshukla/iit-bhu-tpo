<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<base href="<?php echo ROOTPATH ?>/"  />
		<link rel='shortcut icon' href="ui/img/executive/itbhu.png"/>
		<!--<link rel="stylesheet" type="text/css" href="ui/css/jquery.jtweetsanywhere-1.3.1.css" />-->
		<title>Training &amp; Placement Cell, IIT (BHU) Varanasi</title>
		<link rel="stylesheet" type="text/css" href="ui/css/datatable/jquery.dataTables.css" />
		<link rel="stylesheet" type="text/css" href="ui/css/executive-ui.css" />
		<script charset="utf-8" type="text/javascript" src="ui/js/jquery-1.6.1.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/jquery.tmpl.min.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/jquery.cookie.min.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/json2.min.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/jquery.expander.min.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/jquery-firespark.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/executive-ui.min.js"></script>
		<script type="text/javascript" src="ui/js/jquery.dataTables.min.js"></script>
		<!--<script charset="utf-8" type="text/javascript" src="ui/js/jquery-vaultguard.js"></script>
		<script charset="utf-8" type="text/javascript" src="ui/js/md5-min.js"></script>-->
		<script type="text/javascript">
			Executive.root = '<?php echo ROOTPATH; ?>';
			Executive.year = '?/<?php echo $YEAR; ?>/';
		</script>
	</head>

	<body>
		<div class="body">
		<span id="load-status"><?php echo $STATUS; ?></span>

		<div class="header-container">
			<div class="page">
				<table class="layout"><tbody>
					<tr>
						<td style="width: 100px; text-align: left;"><img src="ui/img/executive/iitbhu_80.png" /></td>
						<td>
							<p class="iitbhu">Indian Institute of Technology (BHU) Varanasi</p>
							<p class="tpc">Training and Placement Cell</p>
							<p class="head4">
								<a href="mailto:tpo@iitbhu.ac.in">tpo@iitbhu.ac.in</a> |
								<a href="mailto:ittpo@sify.com">ittpo@sify.com</a> |
								<a href="tel:+915422307007">+91-542-2307007</a> |
								<a href="tel:+915422368160">+91-542-2368160</a>
							</p>
						</td>
					</tr>
				</tbody></table>

				<div class="mainmenu">
					<?php echo $MAINMENU; ?>

					<?php echo $STATEMENU; ?>
				</div>
				<div class="clearfloat"></div>
			</div>
		</div>
