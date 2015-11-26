<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Strict//en">

<?php
require_once 'functions.php';
	
	$program = "";
	if( !isset($_GET["prog"]) )
		$program = 'normal';
	else {
		$program = $_GET["prog"];
	}
	
	$eventTypeArr = array(
		'1' => "Football", 
		'2' => "Basketball", 
		'5' => "Tennis", 
		'7' => "Ice Hockey", 
		'8' => "Handball", 
		'10' => "Antepost",
		'0' => "Total" 
		);
		
	$command = '/home/admin/tradermonitor/jsonprint.py';
	$result = shell_exec($command);
	$jsonData = json_decode($result, true);
	//var_dump($jsonData);
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style.css" />
		<title>XML- Viewer</title>
	</head>

	<body>
		<img src="./images/intralot.jpg" width="270" style="display: block; margin-left: auto; margin-right: auto" />
		<h1>XML-Viewer</h1>
		
		<table>
			<tr id="programMenu">
				<td class="menuBox">
					<a href="./index.php?prog=normal">Full program</a>
				</td>
				<td class="menuBox">
					<a href="./index.php?prog=slv">Reduced program (SLV)</a>
				</td>
			</tr>
		</table>
		
		<table>
			<colgroup>
				<col span="1" class="darkBG" />
				<col span="1" class="lightBG" />
				<col span="1" class="darkBG" />
				<col span="1" class="lightBG" />
				<col span="1" class="darkBG" />
				<col span="1" class="lightBG" />
			</colgroup>
			<th>Sport type</th>
			<th>All events</th>
			<th>Inactive events</th>
			<th>Active events</th>
			<th>Blocked events</th>
			<th>Kompakt events</th>
			<?php
				foreach( $eventTypeArr as $sport ) {
					//create table row tag
					printf( "<tr>" );
					// create first column 
					printf( '<td><a href="./index.php?sport='. $sport .'">%s</a></td>', $sport );
					$arrayKey = array_keys($eventTypeArr, $sport);
					printf( "<td>%s</td>", countEvents($jsonData, 'EventType', $program, $arrayKey[0]) );
					//printf( "<td>%s</td>", countInactiveEvents($jsonData, 'EventStatus', $program, 'Inactive') );
					printf( "<td>&nbsp</td>" );
					printf( "<td>&nbsp</td>" );
					printf( "<td>&nbsp</td>" );
					printf( "<td>&nbsp</td>" );
					printf("</tr>");
				}
			?>
		</table>
	</body>

</html>
