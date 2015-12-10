<?php
class Layout {
	
	public function __construct() {}
	
	public function createColumnStyle( $column ) {
		
		$counter = 0;
		printf( "<colgroup>" );
		while( $counter <= $column ) {
			printf("<col span=\"1\" class=\"darkBG\" />
					<col span=\"1\" class=\"lightBG\" />" );
			$counter++;
		}
		printf( "</colgroup>" );
		
	}
	
	public function createTableRows( ) {
		for( $i = 0; $i < $row; $i++ ) {
			printf( "<tr>" );
			for( $j = 0; $j < $column; $j++ ) {
				printf( "<td>" .$tableContent . "</td>" );
			}
			printf( "</tr>" );
		}
	} 
	
	public function __destruct() {
		
	}
}

?>