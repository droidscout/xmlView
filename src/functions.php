<?php

class XMLFeed {
	
	private $jsonObj;
	
	var $plusEvents = array();
	var $kompaktEvents = array();
	var $activeEvents = array();
	var $inactiveEvents = array();
	var $blockedEvents = array();
	var $notInactiveEvents = array();
	var $totalEventsPerGame = array();
	
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
	private function getEvents( $program, $eventType, $value ) {
		$retVal = array();
		
		foreach( (array) $this->jsonObj->{$program} as $event ) {
			
			if( $event->{'EventStatus'} == $value && $event->{'EventType'} == $eventType )
				$retVal[$i++] = $event->{'EventStatus'};
			
		}
		
		return $retVal;
	}
	
	/*
	 * returns the number of active events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getActiveEventsCount( $program, $eventType ) {
		
		$this->activeEvents = $this->getEvents( $program, $eventType, "Active" );
		
		return count( $this->activeEvents );
	}
	
	/*
	 * returns the number of inactive events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getInactiveEventsCount( $program, $eventType ) {
		
		$this->inactiveEvents = $this->getEvents( $program, $eventType, "Inactive" );
		
		return count($this->inactiveEvents );
	}
	
	/*
	 * returns the number of blocked events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getBlockedEventsCount( $program, $eventType ) {
		
		$this->blockedEvents = $this->getEvents( $program, $eventType, "Blocked" );
		
		return count( $this->blockedEvents );
	}
	
	/*
	 * returns the number of compact events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getKompaktEventsCount( $program, $eventType ) {
		
		foreach( (array) $this->jsonObj->{$program} as $event ) {
			
			if( $event->{'PlayIndex'} != "0" && $event->{'EventType'} == $eventType )
				$this->kompaktEvents[$i++] = $event->{'PlayIndex'};
				
		}
		
		return count( $this->kompaktEvents );
	}
	
	/*
	 * returns the total number of events associated with a certain program
	 * @program: the program as String
	 * @eventType: event type (sport) as String
	 */
	public function getTotalEventPerGameCount( $program, $eventType ) {
		
		return $this->getActiveEventsCount($program, $eventType) + 
				$this->getInactiveEventsCount($program, $eventType) + 
					$this->getBlockedEventsCount($program, $eventType);
		
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
	}
}
