<?php	
	require_once 'ArrayTable.php';
	
	abstract class Table
	{
		abstract public function getName();

		abstract public function getRecordCount();

		abstract public function getFieldCount();

		abstract public function getFieldNumber($fieldName);

		abstract public function getFieldName($fieldNumber);

		abstract public function hasField($field);

		abstract public function getData($record, $field);
	}
?>
