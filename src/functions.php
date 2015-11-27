<?php

class XMLFeed {
	
	private $jsonObj;
	
	var $plusEvents = array();
	var $kompaktEvents = array();
	var $activeEvents = array();
	var $inactiveEvents = array();
	var $blockedEvents = array();
	var $totalEventsPerGame = array();
	
	public function __construct( $array ) {
		$this->jsonObj = $array;
	}
	
	private function getEvents( $program, $eventType, $value ) {
		$retVal = array();
		
		foreach( (array) $this->jsonObj->{$program} as $event ) {
			
			if( $event->{'EventStatus'} == $value && $event->{'EventType'} == $eventType )
				$retVal[$i++] = $event->{'EventStatus'};
			
		}
		
		return $retVal;
	}
	
	public function getActiveEvents( $program, $eventType ) {
		
		$this->activeEvents = $this->getEvents( $program, $eventType, "Active" );
		
		return count( $this->activeEvents );
	}
	
	public function getInactiveEvents( $program, $eventType ) {
		
		$this->inactiveEvents = $this->getEvents( $program, $eventType, "Inactive" );
		
		return count($this->inactiveEvents );
	}
	
	public function getBlockedEvents( $program, $eventType ) {
		
		$this->blockedEvents = $this->getEvents( $program, $eventType, "Blocked" );
		
		return count( $this->blockedEvents );
	}
	
	public function getKompaktEvents( $program, $eventType ) {
		
		foreach( (array) $this->jsonObj->{$program} as $event ) {
			
			if( $event->{'PlayIndex'} != "0" && $event->{'EventType'} == $eventType )
				$this->kompaktEvents[$i++] = $event->{'PlayIndex'};
				
		}
		
		return count( $this->kompaktEvents );
	}

	public function getTotalEventPerGame( $program, $eventType ) {
		
		return $this->getActiveEvents($program, $eventType) + $this->getInactiveEvents($program, $eventType) + $this->getBlockedEvents($program, $eventType);
		
	}
	
	
	function __destruct() {
		$this->jsonObj = null;
		$this->activeEvents = null;	
	}
}
