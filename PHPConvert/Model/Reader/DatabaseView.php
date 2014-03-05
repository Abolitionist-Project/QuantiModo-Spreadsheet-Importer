<?php	
	require_once 'ArrayDatabaseView.php';

	abstract class DatabaseView
	{
		abstract public function getTableCount();

		abstract public function getTableNumber($tableName);
		abstract public function getTableName($getTableNumber);

		abstract public function hasTable($table);

		abstract public function getTable($table);
	}
?>
