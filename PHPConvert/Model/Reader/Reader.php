<?php	
	require_once 'DatabaseView.php';
	require_once 'Table.php';

	abstract class Reader
	{
		abstract function getDatabaseView($file);
	}
?>
