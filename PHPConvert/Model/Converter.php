<?php	
	abstract class Converter
	{		
		function __construct() 
		{

		}

		abstract public function convert($databaseView);
	}
?>
