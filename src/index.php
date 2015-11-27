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
		'3' => "Ice Hockey",  
		'4' => "Tennis", 
		'5' => "Handball",
		'6' => "Baseball",
		'7' => "Volleyball",
		'8' => "Golf",
		'9' => "Polo",
		'10' => "Antepost",
		'11' => "American Football" 
		);
		
	$command = '/home/admin/tradermonitor/jsonprint.py';
	$result = shell_exec($command);
	$jsonData = json_decode($result);
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
				$totalActiveEvents = 0;
				$totalInactiveEvents = 0;
				$totalBlockedEvents = 0;
				$totalKomapktEvents = 0;
				
				foreach( $eventTypeArr as $sport ) {
					$feed = new XMLFeed( $jsonData );
					/*
					 * get key for specific sport type
					 */
					$arrayKey = array_keys( $eventTypeArr, $sport );
					/*
					 * calculate total values for each column
					 */
					$totalActiveEvents += $feed->getActiveEvents( $program, $arrayKey[0], "Active" );
					$totalInactiveEvents += $feed->getInactiveEvents( $program, $arrayKey[0], "Inactive" );
					$totalBlockedEvents += $feed->getBlockedEvents( $program, $arrayKey[0], "Blocked" );
					$totalKompaktEvents += $feed->getKompaktEvents( $program, $arrayKey[0] );
					/*
					 * display numbers per event type
					 */
					printf( "<tr>" );
					printf( '<td><a href="./index.php?sport='. $sport .'">%s</a></td>', $sport );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getTotalEventPerGame($program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getInactiveEvents($program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getActiveEvents($program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getBlockedEvents($program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getKompaktEvents( $program, $arrayKey[0] ) );
					printf("</tr>");
				}
				/*
				 * display total numbers per column
				 */
				printf("<tr>");
				printf( "<td>Total</td>" );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalInactiveEvents + $totalActiveEvents + $totalBlockedEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalInactiveEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalActiveEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalBlockedEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalKompaktEvents );
				printf("</tr>");
				exit();
			?>
			
		</table>
	
	</body>

</html>
