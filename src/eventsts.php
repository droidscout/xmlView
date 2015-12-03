<?php
session_start();
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
	
	if( empty($_SESSION['json']) ) {
		
		$result = shell_exec($command);
		$jsonData = json_decode($result);
		$_SESSION['json'] = $jsonData;
		
	}
	else {
		
		$jsonData = $_SESSION['json'];
		
	}
	
	$feed = new XMLFeed( $jsonData );
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Strict//en">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/eventstsstyle.css" />
		<script type="text/javascript" src="js/jsfunctions.js"></script>
		<title>XML- Viewer</title>
	</head>

	<body>
		<img src="./images/intralot.jpg" width="270" style="display: block; margin-left: auto; margin-right: auto" />
		<h1>XML-Viewer</h1>
		<h2><div>Date: <?php $date = date('Y-m-d H:i'); printf("%s", $date); ?></div></h2>
		<table>
			<tr id="programMenu">
				<td class="menuBox">
					<a href="./index.php?prog=normal">Full program</a>
				</td>
				<td class="menuBox">
					<a href="./index.php?prog=slv">Reduced program (SLV)</a></div>
				</td>
			</tr>
			<tr style="text-align: left">
				<td><?php $_SESSION['json'] = $jsonData; if( $program == "normal" ) { printf( "<a href=\"./index.php?prog=". $program ."\">Full program</a>" ); } 
							elseif( $program == "slv" ) {  printf( "<a href=\"./index.php?prog=". $program ."\">Reduced program (SLV)</a>"); } printf(" / ". $eventSts ." events"); 
					?></td>
			</tr>
		</table>
		
		<table>
			<tr id="eventTypeTable">
				<?php 	
					foreach( $eventTypeArr as $sport ) {
						
						$arrayKey = array_keys( $eventTypeArr, $sport );
						$isEmpty = $feed->getEventWStatus($program, $arrayKey[0], $eventSts);
						
						if( !empty($isEmpty) ) 
							printf("<td><a href=\"#" .$sport. "\">". $sport ."</a></td>"); 
					}					
				?>
			</tr>
		</table>
		<?php
			
			
			foreach( $eventTypeArr as $sport ) {
				/*
				 * get key for specific sport type
				 */
				$arrayKey = array_keys( $eventTypeArr, $sport );
						
				$isEmpty = $feed->getEventWStatus($program, $arrayKey[0], $eventSts);
				if( !empty($isEmpty) ) {
					
					printf("<table cellspacing=\"0\" cellpadding=\"0\">
							<colgroup>
							<col span='1' class='darkBG' />
							<col span='1' class='lightBG' />
							</colgroup>");
							
					printf("<a name=\"" .$sport. "\"></a><th><h2>". $sport ."</h2></th>");
				
					foreach( $feed->getEventWStatus($program, $arrayKey[0], $eventSts) as $event ) {
						printf( "<tr class=\"tableValuesTop\"><td>Event: </td><td>". $event->{'Descr'} ."</td></tr>" );
						printf( "<tr><td class=\"tableValues\">Event status: </td><td class=\"tableValues\">". $event->{'EventStatus'} ."</td></tr>" );
						printf( "<tr><td class=\"tableValues\">Country: </td><td class=\"tableValues\">". $event->{'Country'} ."</td></tr>" );
						printf( "<tr><td class=\"tableValues\">League: </td><td class=\"tableValues\">". $event->{'LeagueFullDescr'} ." / ". $event->{'League'} ."</td></tr>" );
						printf( "<tr><td class=\"tableValues\">Country: </td><td class=\"tableValues\">". $event->{'Country'} ."</td></tr>" );
						printf( "<tr><td class=\"tableValues\">Bet start date: </td><td class=\"tableValues\">". $event->{'StartDate'} ."</td></tr>" );
						printf( "<tr class=\"tableValuesBottom\"><td>Kick off date: </td><td class=\"tableValuesBottom\">". $event->{'Date'} ."</td></tr>" );
						printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
						
					}
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
					printf( "</table>" );
				}
			}

			exit();
		?>	

	</body>

</html>
