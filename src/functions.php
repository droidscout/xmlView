<?php
function countEvents( $array, $arrayKey, $progType, $keyValue ) {
	$number = 0;
	foreach( $array[$progType] as $element ) {
		// check if we have an array, yes -> call this function recursive
		if( is_array($element) ) {
			if( array_key_exists( $arrayKey, $element) ) {
				if( $element[$arrayKey] == $keyValue ) {
					$number++;
				}
			}
		}
	}
	return $number;
}

function countInactiveEvents( ) {
	
}
