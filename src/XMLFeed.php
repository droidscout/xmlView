<?php

class XMLFeed {
	
	private $jsonObj;
	
	var $plusEvents = array();
	var $kompaktEvents = array();
	var $activeEvents = array();
	var $inactiveEvents = array();
	var $blockedEvents = array();
	var $cancelledEvents = array();
	var $payoutEvents = array();
	var $notInactiveEvents = array();
	var $totalEventsPerGame = array();
	var $eventsWEventStatus = array();
	var $startedEvents = array();
	/*
	 * class constructor
	 * @array: JSON object for further process
	 */
	public function __construct( $array ) {
		$this->jsonObj = $array;
	}
	
	/*
	 * get the events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 * @value: value for the event status (Active, Inactive, Blocked)
	 */
	private function getEvents( $program, $eventType ) {
		
		$retVal = array();

		foreach( (array) $this->jsonObj->{$program} as $event ) {
			
			if( $event->{'EventType'} == $eventType ) {	
				$retVal[$i++] = $event;
			}
		}

		return $retVal;
	}
	
	private function getLeagueEventsWStatus( $program, $eventType, $eventStatus, $league ) {
		$retVal = array();
		
		foreach( $this->getEvents($program, $eventType) as $event ) {
			if( $event->{'EventStatus'} == $eventStatus && $event->{'League'} == $league ) {
				$retVal[$i++] = $event;
			}
		}
		
		return $retVal;
	}
	
	/*
	 * returns the number of active events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getActiveEventsCount( $program, $eventType ) {
		
		if( count($this->activeEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				
				if( $eventArray->{'EventStatus'} == "Active" ) {
					
					$this->activeEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->activeEvents );
	}
	
	/*
	 * returns the number of inactive events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getInactiveEventsCount( $program, $eventType ) {
		
		if( count($this->inactiveEvents) == 0) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				
				if( $eventArray->{'EventStatus'} == "Inactive" ) {
					
					$this->inactiveEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count($this->inactiveEvents );
	}
	
	/*
	 * returns the number of blocked events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getBlockedEventsCount( $program, $eventType ) {
		
		if( count($this->blockedEvents) == 0) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( $eventArray->{'EventStatus'} == "Blocked" ) {
					$this->blockedEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->blockedEvents );
	}
	
	/*
	 * returns the number of compact events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getKompaktEventsCount( $program, $eventType ) {
		
		if( count($this->kompaktEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( $eventArray->{'PlayIndex'} != "0" && strtolower($eventArray->{'EventType'}) == strtolower($eventType) ) {
					$this->kompaktEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->kompaktEvents );
	}
	
	public function getCancelledEventsCount( $program, $eventType ) {
		
		if( count($this->cancelledEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( strtolower($eventArray->{'EventStatus'}) == "cancelled" ) {
					$this->cancelledEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->cancelledEvents );
		
	}
	
	public function getPayoutEventsCount( $program, $eventType ) {
		
		if( count($this->payoutEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( strtolower($eventArray->{'EventStatus'}) == "payable" ) {
					$this->payoutEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->payoutEvents );
		
	}
	
	public function getStartedEventsCount( $program, $eventType ) {
		
		if( count($this->startedEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( strtolower($eventArray->{'EventStatus'}) == "started" ) {
					$this->startedEvents[$i++] = $eventArray;
				}
			}
		}
		
		return count( $this->startedEvents );
		
	}
	
	/*
	 * returns the number of compact events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getKompaktEvents( $program, $eventType ) {
		$retVal = array();
		if( count($this->kompaktEvents) == 0 ) {
			foreach( $this->getEvents($program, $eventType) as $eventArray ) {
				if( $eventArray->{'PlayIndex'} != "0" && $eventArray->{'EventType'} == $eventType ) {
					$retVal[$i++] = $eventArray;
				}
			}
		}

		return $retVal;
	}
	
	/*
	 * returns the total number of events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getAllEventPerGameCount( $program, $eventType ) {
		
		return $this->getActiveEventsCount( $program, $eventType ) + 
				$this->getInactiveEventsCount( $program, $eventType ) + 
					$this->getBlockedEventsCount( $program, $eventType ) +
						$this->getCancelledEventsCount( $program, $eventType ) + 
							$this->getPayoutEventsCount( $program, $eventType ) + 
								$this->getStartedEventsCount( $program, $eventType );
		
	}
	
	/*
	 * returns the number of events which don't have the event status "Inactive" associated with a certain program
	 * @program: the program as String
	 */
	public function getNoInactiveEvents( $program ) {
		
		foreach( (array) $this->jsonObj->{$program} as $event ) {
			if( $event->{'EventStatus'} != "Inactive" ) {
				$this->notInactiveEvents[$i++] = $event{'EventStatus'};
			}
		}
		
		return count( $this->notInactiveEvents );
		
	}
	

	public function getEventWStatus( $program, $eventType, $eventStatus ) {
		$retVal = array();
		if( strtolower($eventStatus) == "kompakt" ) {
			foreach( $this->getEvents($program, $eventType) as $event ) {
				if( strtolower($event->{'PlayIndex'}) != "0" ) {
					$retVal[$i++] = $event;
				}
			}
		}
		else {
			foreach( $this->getEvents($program, $eventType) as $event ) {
				if( strtolower($event->{'EventStatus'}) == strtolower($eventStatus) ) {
					$retVal[$i++] = $event;
				}
			}
		}
		return $retVal;
	}
	
	public function getLeagues( $program, $eventType ) {
		
		$retVal = array();
		foreach( $this->getEvents($program, $eventType) as $event ) {
			if( !in_array($event->{'League'}, $retVal) )
				$retVal[$i++] = $event->{'League'};		
		}
	
		sort( $retVal, SORT_STRING );
		return $retVal;
	}

	public function getEventsPerLeague( $program, $eventType, $eventStatus, $league ) {
		
		$retVal = array();
		foreach( $this->getEventWStatus($program, $eventType, $eventStatus) as $event ) {
			if( $event->{'League'} == $league ) {
				$retVal[$i++] = $event;
			}
		}

		return $retVal;
	}
	
	public function getEventDetails( $program, $id ) {
		
		foreach( $this->jsonObj->{$program} as $event) {
			if( $event->{'ID'} == $id )
				return $event;
		}
	}

	
	/*
	 * destructor for this class
	 * frees any resources
	 */
	function __destruct() {
		$this->jsonObj = null;
		$this->activeEvents = null;
		$this->inactiveEvents = null;
		$this->blockedEvents = null;
		$this->kompaktEvents = null;
		$this->cancelledEvents = null;
		$this->payoutEvents = null;
		$this->startedEvents = null;
	}
}
