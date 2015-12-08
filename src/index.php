<?php
session_start();
require_once 'XMLFeed.php';
require_once 'Layout.php';
	
	$program = "";
	
	if( !isset($_GET["prog"]) )
		$program = 'normal';
	else {
		$program = $_GET["prog"];
		//$_SESSION['prog'] = $program;
	}
	
	if( isset($_GET['eventsts']) ) {
		switch( $_GET["eventsts"] ){
	
			case "active":
				$eventStatus = "Active";
				break; 
			case "inactive":
				$eventStatus = "Inactive";
				break;
			case "blocked":
				$eventStatus = "Blocked";
				break;
			case "kompakt":
				$eventStatus = "Kompakt";
				break;
		}
	}
	
	if( isset($_GET['eventType']) ) {
		$eventType = $_GET['eventType'];
	}
	
	if( isset($_GET['league']) ) {
		$eventLeague = $_GET['league'];
	}
	
	if( isset($_GET['sport']) ) {
		$sportType = $_GET['sport'];
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
	
	if( empty($_SESSION['json']) ) {
		
		$result = shell_exec($command);
		$jsonData = json_decode($result);
		$_SESSION['json'] = $jsonData;
		
	}
	else {
		
		$jsonData = $_SESSION['json'];
		
	}
		
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Strict//en">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style.css" />
		<!--<link rel="stylesheet" href="css/evenstsstyle.css" />-->
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
					<a onclick="toggleLink(this.id)" id="full" href="./index.php?prog=normal">Full program</a>
				</td>
				<td class="menuBox">
					<a onclick="toggleLink(this.id)" id="red" href="./index.php?prog=slv">Reduced program (SLV)</a></div>
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
			<th class="menuBox"><a href="<?php printf('./index.php?prog='. $program .'&eventsts=inactive');?>">Inactive events</a></th>
			<th class="menuBox"><a href="<?php printf('./index.php?prog='. $program .'&eventsts=active');?>">Active events</a></th>
			<th class="menuBox"><a href="<?php printf('./index.php?prog='. $program .'&eventsts=blocked');?>">Blocked events</a></th>
			<th class="menuBox"><a href="<?php printf('./index.php?prog='. $program .'&eventsts=kompakt');?>">Kompakt events</a></th>
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
					$totalActiveEvents += $feed->getActiveEventsCount( $program, $arrayKey[0] );
					$totalInactiveEvents += $feed->getInactiveEventsCount( $program, $arrayKey[0] );
					$totalBlockedEvents += $feed->getBlockedEventsCount( $program, $arrayKey[0] );
					$totalKompaktEvents += $feed->getKompaktEventsCount( $program, $arrayKey[0] );
					/*
					 * display numbers per event type
					 */
					printf( "<tr>" );
					printf( "<td class\"tableValuesRowHeader\"><!--<a href=\"./index.php?sport=\". $sport .\">-->". $sport ."<!--</a>--></td>" );
					printf( "<td class=\"totalTableValues\">%s</td>", $feed->getAllEventPerGameCount( $program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getInactiveEventsCount( $program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getActiveEventsCount( $program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getBlockedEventsCount( $program, $arrayKey[0]) );
					printf( "<td class=\"tableValues\">%s</td>", $feed->getKompaktEventsCount( $program, $arrayKey[0] ) );
					printf("</tr>");
				}
				/*
				 * display total numbers per column
				 */
				printf( "<tr>" );
				printf( "<td>Total</td>" );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalInactiveEvents + $totalActiveEvents + $totalBlockedEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalInactiveEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalActiveEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalBlockedEvents );
				printf( "<td class=\"totalTableValues\">%s</td>", $totalKompaktEvents );
				printf("</tr>");
				
			?>	
		</table>
	
		<?php
		
			/*
			 * Building link to step back
			 */
			printf( "<table><tr style=\"text-align: left\"><td class=\"menuBox\">" );
			
			if( isset($program) && isset($eventStatus) &! isset($eventType) ) {
				
				if( $program == "normal" ) {
					 printf( "<a href=\"./index.php?prog=".$program."\">Full program</a>" ); 
				} 
				elseif( $program == "slv" ) {
					  printf( "<a href=\"./index.php?prog=".$program."\">Reduced program (SLV)</a>"); 
				}
				
				printf( " / ".$eventStatus." events" );
				printf( "</td></tr></table>" );
				
			}
			elseif( isset($program) && isset($eventStatus) && isset($eventType) &! isset($eventLeague) ) {
				
				if( $program == "normal" ) {
					 printf( "<a href=\"./index.php?prog=".$program."\">Full program</a>" ); 
				} 
				elseif( $program == "slv" ) {
					  printf( "<a href=\"./index.php?prog=".$program."\">Reduced program (SLV)</a>"); 
				}
				
				printf( " / <a href=\./index.php?prog=". $program ."&eventsts=". $_GET["eventsts"] .">".$eventStatus." events</a>" );
				
				printf( " / ".$eventType );
				printf( "</td></tr></table>" );
			}
			elseif( isset($program) && isset($eventStatus) && isset($eventType) && isset($eventLeague) ) {
				
				if( $program == "normal" ) {
					 printf( "<a href=\"./index.php?prog=". $program ."\">Full program</a>" ); 
				} 
				elseif( $program == "slv" ) {
					  printf( "<a href=\"./index.php?prog=". $program ."\">Reduced program (SLV)</a>"); 
				}
				printf( " / <a href=\./index.php?prog=". $program ."&eventsts=". $_GET["eventsts"] .">".$eventStatus." events</a>" );
				printf( " / <a href=\./index.php?prog=". $program ."&eventsts=". $_GET["eventsts"] ."&eventType=".$eventType.">".$eventType."</a>" );
				
				printf( " / ".$eventLeague );
				printf( "</td></tr></table>" );
			}
			/*elseif( isset($sportType) &! isset($_GET['prog']) &! isset($eventStatus) ) {
					
				printf( $sportType. "</td></tr>" );
				
				printf( "<tr><td><a href=\"./index.php?sport=". $sportType ."&prog=normal\">Full program</td>");
				printf( "<td><a href=\"./index.php?sport=". $sportType ."&prog=slv\">Reduced program (SLV)</td></tr></table>" );			
			}
			elseif( isset($sportType) && isset($_GET['prog']) &! isset($eventStatus) ) {
				
				if( $program == "normal" ) {
					printf( "<a href=\"./index.php?sport=" . $sportType . "\">" . $sportType ."</a>/ Full program");
				} 
				elseif( $program == "slv" ) {
					printf( "<a href=\"./index.php?sport=" . $sportType . "\">" . $sportType ."</a>/ Reduced program (SLV)");
				}
				
				printf( "</td></tr></table>" );
			}*/
			
			
			/*
			 * -----------------------------------------------------------------------------------------------------------------------------------------------
			 */
			/*
			 * Building links with event type per event status
			 */
			printf( "<table><tr id=\"programMenu\">" );
			
			foreach( $eventTypeArr as $sport ) {
				
				// get the key which is associated with the value
				$arrayKey = array_keys( $eventTypeArr, $sport );
				// we check if there are events which can be associated with the status
				$isEmpty = $feed->getEventWStatus( $program, $arrayKey[0], $_GET['eventsts'] );
				 
				if( !empty($isEmpty) ) {
					// let's build the link with the text to display it
					$link = "";
					if( isset($program) ) {
						
						$link .= "prog=". $program;
						$link .= "&";
					}
					if( isset($_GET['eventsts']) ) {
						
						$link .= "eventsts=". $_GET['eventsts'];
						$link .= "&";
					}
					
					$link .= "eventType=". $sport;
					printf("<td class=\"menuBox\"><a href=\"./index.php?" .$link. "\">". $sport ."</a></td>");
				} 
			}
			printf( "</tr></table>" );
			
			if( isset($eventType) ) {
				
				// get the key which is associated with the value
				$arrayKey = array_keys( $eventTypeArr, $eventType );
				
				printf("<table><!--<colgroup>
				<col span=\"1\" class=\"darkBG\" />
				<col span=\"1\" class=\"lightBG\" />
				<col span=\"1\" class=\"darkBG\" />
				<col span=\"1\" class=\"lightBG\" />
				<col span=\"1\" class=\"darkBG\" />
				<col span=\"1\" class=\"lightBG\" />
				<col span=\"1\" class=\"darkBG\" />
				<col span=\"1\" class=\"lightBG\" />
				</colgroup>
				<th>League</th>
				<th>Count</th>
				<th>League</th>
				<th>Count</th>
				<th>League</th>
				<th>Count</th>
				<th>League</th>
				<th>Count</th>-->");
				
				$arrayCount = count( $feed->getLeagues($program, $arrayKey[0]) );
				// we need to replace the eventType=American Football with the current event type
				// ugly workaround, since there might be situations where the string eventType=American Football might not be found
				$link = str_replace( "eventType=American Football", "eventType=". $eventType, $link );
				
				$link .= "&league=";
				
				if( $arrayCount <= 4 ) {
					printf( "<colspan>" );
					$count = 0;
					do {
						printf("<col span=\"1\" class=\"darkBG\" />
								<col span=\"1\" class=\"lightBG\" />" );
						$count++;
					} while( $count <= $arrayCount );
					printf( "<colspan>" );
					
					printf("<tr>");
					foreach( $feed->getLeagues($program, $arrayKey[0]) as $league ) {
						printf( "<td class=\"menuBox\"><a href=\"./index.php?". $link . $league . "\">". $league ."</td>" );
						printf( "<td class=\"totalTableValues\">" . count( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $league ) ) ."</td>" );
					}
					printf("</tr>");
				}
				else {
					
					$numRows = intval( ($arrayCount / 4) + ($arrayCount % 4) );
					$rowCnt = 0;
					$elementCnt = 0;
					$league = $feed->getLeagues( $program, $arrayKey[0] );
					
					do {
						printf("<tr>");
						
						for( $i = 0; $i <= 3; $i++ ) {
							
							$eleCnt = $elementCnt + $i;
							if( $league[$eleCnt] != "" ) {
								printf( "<td><a href=\"./index.php?". $link . $league[$eleCnt] . "\">". $league[$eleCnt] ."</td>" );
								printf( "<td class=\"tableValues\">" . count( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $league[$eleCnt]) ) ."</td>" );
							}
						}
						
						printf( "</tr>" );
						$elementCnt += 4;
						$rowCnt++;
						
					} while( $rowCnt < $numRows ); 
					
				}
				
				printf("</table>");
			}
			/*
 			 * ------------------------------------------------------------------------------------------------------------------------------------------------ 
 			 */

			if( isset($eventLeague) ) {
					
				printf("<table cellspacing=\"0\" cellpadding=\"1\">
							<colgroup>
							<col span='1' class='darkBG' />
							<col span='1' class='lightBG' />
							</colgroup>");
				printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
				$arrayKey = array_keys( $eventTypeArr, $eventType );
				foreach( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $eventLeague) as $leagueEvent) {
					
					printf( "<tr class=\"tableValuesTop\"><td>Event: </td><td><a href=\"./index.php?evtdetails=true&eventid=". $leagueEvent->{'ID'} ."\">". $leagueEvent->{'Descr'} ."</a></td></tr>" );
					printf( "<tr><td class=\"tableValues\">Event status: </td><td class=\"tableValues\">". $leagueEvent->{'EventStatus'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Country: </td><td class=\"tableValues\">". $leagueEvent->{'Country'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">League: </td><td class=\"tableValues\">". $leagueEvent->{'LeagueFullDescr'} ." / ". $leagueEvent->{'League'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Country: </td><td class=\"tableValues\">". $leagueEvent->{'Country'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Bet start date: </td><td class=\"tableValues\">". $leagueEvent->{'StartDate'} ."</td></tr>" );
					printf( "<tr class=\"tableValuesBottom\"><td>Kick off date: </td><td class=\"tableValuesBottom\">". $leagueEvent->{'Date'} ."</td></tr>" );
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
				}
				
				printf("</table>" );
			}
			/*
			 * -----------------------------------------------------------------------------------------------------------------------------------------------
			 */
			 if( isset($_GET['evtdetails']) && isset($_GET['eventid']) ) {
			 	if( strtolower($_GET['evtdetails']) == "true" ) {
			 		
			 		printf( "<table>" );
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
					
			 		$eventDetails = $feed->getEventDetails( $_SESSION['prog'], $_GET['eventid']);
			 		
					printf( "<tr><td class=\"tableValues\">Event Id: </td><td class=\"tableValues\">". $eventDetails->{'ID'} ."</td></tr>" );	
					printf( "<tr><td class=\"tableValues\">Event: </td><td class=\"tableValues\">". $eventDetails->{'Descr'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Event type: </td><td class=\"tableValues\">". $eventTypeArr[$eventDetails->{'EventType'}] ."&nbsp(". $eventDetails->{'EventType'} .")</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Event status: </td><td class=\"tableValues\">". $eventDetails->{'EventStatus'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Country: </td><td class=\"tableValues\">". $eventDetails->{'Country'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">League: </td><td class=\"tableValues\">". $eventDetails->{'LeagueFullDescr'} ." / ". $eventDetails->{'League'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Bet start date: </td><td class=\"tableValues\">". $eventDetails->{'StartDate'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Kickoff date: </td><td class=\"tableValues\">". $eventDetails->{'Date'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">FinalWithHandicap: </td><td class=\"tableValues\">". $eventDetails->{'FinalWithHandicap'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">First half score: </td><td class=\"tableValues\">". $eventDetails->{'FirsthalfScore'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Neutral: </td><td class=\"tableValues\">". $eventDetails->{'Neutral'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">PlayIndex: </td><td class=\"tableValues\">". $eventDetails->{'PlayIndex'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Priority: </td><td class=\"tableValues\">". $eventDetails->{'Priority'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">SE: </td><td class=\"tableValues\">". $eventDetails->{'SE'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">SevDelay: </td><td class=\"tableValues\">". $eventDetails->{'SevDelay'} ."</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Total games: </td><td class=\"tableValues\">". $eventDetails->{'TotalGames'} ."</td></tr>" );
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
					printf( "<tr><td class=\"tableValues\">Markets: </td></tr>" );
					printf( "<tr><td>&nbsp</td><td><table>");
					printf("<th>ID</th>");
					printf("<th>Market</th>");
					printf("<th>Descr:</th>");
					printf("<th>Odd</th>");
					foreach( (array) $feed->getEventDetails( $_SESSION['prog'], $_GET['eventid'])->{'outcomes'} as $market ) {
						printf("<tr><td class=\"tableValues\">". $market->{'ID'} ."</td><td class=\"tableValues\">". $market->{'Market'} ."</td><td class=\"tableValues\">". $market->{'Descr'} ."</td><td class=\"tableValues\">". $market->{'Odd'} ."</td><td>" ); 
					}
					
					printf( "</table></td></tr>");
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
						
					printf( "</table>" );
			 	}
			 }
			 								
		?>
		
	</body>

</html>
<?php
	exit();
?>
