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
		//'6' => "Baseball",
		'7' => "Volleyball",
		//'8' => "Golf",
		//'9' => "Polo",
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
			<tr>
				<td>
					
				<form action="index.php">
					<label>Search for Event-Id:</label>
					<input type="hidden" name="evtdetails" value="true"/>
					<input type="search" id="" name="eventid" />
					<button>Go</button>
				</form>
				</td>
			</tr>
		</table>
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
					printf( "<td class=\"marketBox\"><!--<a href=\"./index.php?sport=\". $sport .\">-->". $sport ."<!--</a>--></td>" );
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
				printf( "<td class=\"marketBox\">Total</td>" );
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
			
			
			/*
			 * -----------------------------------------------------------------------------------------------------------------------------------------------
			 */
			/*
			 * Building links with event type per event status
			 */
			printf( "<table><tr id=\"programMenu\">" );
			if( isset($program) && isset($_GET['eventsts']) ) {
				foreach( $eventTypeArr as $sport ) {
					
					// get the key which is associated with the value
					$arrayKey = array_keys( $eventTypeArr, $sport );
					// we check if there are events which can be associated with the status
					$eventsWithStatus= $feed->getEventWStatus( $program, $arrayKey[0], $_GET['eventsts'] );
					$kompaktEvents = $feed->getKompaktEvents( $program, $arrayKey[0] ); 
					
					if( !empty($eventsWithStatus) ) {
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
					else if( !empty($kompaktEvents) ) {
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
			}
			printf( "</tr></table>" );
			
			// section for rendering the count per league table
			if( isset($eventType) ) {
				
				// get the key which is associated with the value
				$arrayKey = array_keys( $eventTypeArr, $eventType );
				
				printf("<table>");
				
				$arrayCount = count( $feed->getLeagues($program, $arrayKey[0]) );
				
				$link = "prog=" .$program. "&eventsts=" .$_GET['eventsts']. "&eventType=" .$_GET['eventType']. "&league=";
				$tableLayout = new Layout();
				$tableLayout->createColumnStyle( $arrayCount ); 
				
				// we only want a pair of four columns to be rendered, so we need to do some magic to display the columns
				// with no more than four column pairs 
				if( $arrayCount <= 4 ) {

					printf("<tr>");
					foreach( $feed->getLeagues($program, $arrayKey[0]) as $league ) {
						printf( "<td class=\"leagueBox\"><a href=\"./index.php?". $link . $league . "\">". $league ."</td>" );
						printf( "<td class=\"counter\">" . count( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $league ) ) ."</td>" );
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
								printf( "<td class=\"leagueBox\"><a href=\"./index.php?". $link . $league[$eleCnt] . "\">". $league[$eleCnt] ."</td>" );
								printf( "<td class=\"counter\">" . count( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $league[$eleCnt]) ) ."</td>" );
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
			// section for rendering the event overview details. This section makes it possible to jump to the overall details of an event including 
			// the markets
			if( isset($eventLeague) ) {
					
				printf("<table>
							<colgroup>
							<col span='1' class='darkBG' />
							<col span='1' class='lightBG' />
							</colgroup>");
				printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
				$arrayKey = array_keys( $eventTypeArr, $eventType );
				
				foreach( $feed->getEventsPerLeague($program, $arrayKey[0], $eventStatus, $eventLeague) as $leagueEvent) {
					
					printf( "<tr><td class=\"marketBox\">Event: </td><td class=\"eventHeader\"><a href=\"./index.php?evtdetails=true&eventid=". $leagueEvent->{'ID'} ."\">".$leagueEvent->{'Descr'} ."</a></td></tr>" );
					printf( "<tr><td class=\"marketBox\">Event ID: </td><td class=\"marketBox\">". $leagueEvent->{'ID'}."</a></td></tr>" );
					printf( "<tr><td class=\"marketBox\">Event status: </td><td class=\"marketBox\">". $leagueEvent->{'EventStatus'} ."</td></tr>" );
					printf( "<tr><td class=\"marketBox\">League: </td><td class=\"marketBox\">". $leagueEvent->{'LeagueFullDescr'} ." / ". $leagueEvent->{'League'} ."</td></tr>" );
					printf( "<tr><td class=\"marketBox\">Kick off date: </td><td class=\"marketBox\">". $leagueEvent->{'Date'} ."</td></tr>" );
					printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
				}
				
				printf("</table>" );
			}
			/*
			 * -----------------------------------------------------------------------------------------------------------------------------------------------
			 */
			 // display the overall details of one specific event which was clicked in the section before
			 if( isset($_GET['evtdetails']) && isset($_GET['eventid']) ) {
			 	if( strtolower($_GET['evtdetails']) == "true" ) {
			 		
					$eventDetails = $feed->getEventDetails( $program, $_GET['eventid']);
					
					if( !empty($eventDetails) ) {
						// display the event details
				 		printf( "<table><colgroup>
								<col span='1' class='darkBG' />
								<col span='1' class='lightBG' />
								</colgroup>" );
						printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Event Id: </td><td class=\"marketBox\">". $eventDetails->{'ID'} ."</td></tr>" );	
						printf( "<tr><td class=\"marketBox\">Event: </td><td class=\"marketBox\">". $eventDetails->{'Descr'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Event type: </td><td class=\"marketBox\">". $eventTypeArr[$eventDetails->{'EventType'}] ."&nbsp(". $eventDetails->{'EventType'} .")</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Event status: </td><td class=\"marketBox\">". $eventDetails->{'EventStatus'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Country: </td><td class=\"marketBox\">". $eventDetails->{'Country'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">League: </td><td class=\"marketBox\">". $eventDetails->{'LeagueFullDescr'} ." / ". $eventDetails->{'League'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Bet start date: </td><td class=\"marketBox\">". $eventDetails->{'StartDate'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Kickoff date: </td><td class=\"marketBox\">". $eventDetails->{'Date'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">FinalWithHandicap: </td><td class=\"marketBox\">". $eventDetails->{'FinalWithHandicap'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">First half score: </td><td class=\"marketBox\">". $eventDetails->{'FirstHalfScore'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Second half score: </td><td class=\"marketBox\">". $eventDetails->{'SecondHalfScore'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Neutral: </td><td class=\"marketBox\">". $eventDetails->{'Neutral'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">PlayIndex: </td><td class=\"marketBox\">". $eventDetails->{'PlayIndex'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Priority: </td><td class=\"marketBox\">". $eventDetails->{'Priority'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">SE: </td><td class=\"marketBox\">". $eventDetails->{'SE'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">SevDelay: </td><td class=\"marketBox\">". $eventDetails->{'SevDelay'} ."</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Total games: </td><td class=\"marketBox\">". $eventDetails->{'TotalGames'} ."</td></tr>" );
						printf( "<tr><td>&nbsp</td><td>&nbsp</td></tr>" );
						printf( "<tr><td class=\"marketBox\">Markets: </td></tr>" );
						printf( "<tr><td>&nbsp</td><td><table><colgroup>
								<col span='1' class='darkBG' />
								<col span='1' class='lightBG' />
								<col span='1' class='darkBG' />
								<col span='1' class='lightBG' />
								</colgroup>");
						printf("<th>ID</th>");
						printf("<th>Market</th>");
						printf("<th>Descr:</th>");
						printf("<th>Odd</th>");
						// render markets
						foreach( (array) $feed->getEventDetails( $program, $_GET['eventid'])->{'outcomes'} as $market ) {
							printf("<tr><td class=\"marketBox\">". $market->{'ID'} ."</td><td class=\"marketBox\">"
							. $market->{'Market'} ."</td><td class=\"marketBox\">". $market->{'Descr'} ."</td>
							<td class=\"marketBox\">". $market->{'Odd'} ."</td><td>" ); 
						}
						printf( "</table></td></tr>");
						
						printf( "</table>" );
					}
					else {
						printf( "<table><tr><td>No Event found with Id: ". $_GET['eventid'] ."</td></tr></table>" );		
					}
			 	}
			 }
			 								
		?>
		
	</body>

</html>
<?php
	exit();
?>
