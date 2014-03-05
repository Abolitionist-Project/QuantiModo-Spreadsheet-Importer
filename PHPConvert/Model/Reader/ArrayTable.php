<?php	
	class ArrayTable extends Table
	{
		private $name;
		private $fieldNames;
		private $records;

		private $fieldCount;
		private $recordCount;

		/*
		**	$name 		This table's name
		**	$fieldNames	The names of the fields in this table
		**	$records 	Two-dimensional array of records in this table
		*/
		function __construct($name, $fieldNames, $records) 
		{
			if($fieldNames == null)
			{
				throw new Exception("No field names were provided");
			}
			if($records == null)
			{
				throw new Exception("No records were provided");
			}
			if($name == null)
			{
				throw new Exception("No table name provided");
			}

			$this->name = $name;
			$this->fieldNames = $fieldNames;
			$this->records = $records;

			$this->fieldCount = count($fieldNames);
			$this->recordCount = count($records);
		}

		/*
		**	Returns the table name
		*/
		public function getName()
		{
			return $this->name;
		}

		/*
		**	Returns the number of records in this table
		*/
		public function getRecordCount()
		{
			return $this->recordCount;
		}

		/*
		**	Returns the number of fields (columns)
		*/
		public function getFieldCount()
		{
			return $this->fieldCount;
		}

		/*
		**	Returns the number of the field with a given name, or false if it doesn't exist
		*/
		public function getFieldNumber($fieldName)
		{
			for($i = 0; $i < $this->fieldCount; $i++)
			{
				if($fieldName == $this->fieldNames[$i])
				{
					return $i;
				}
			}
			return false;
		}

		/*
		**	Returns the name of the field at the given position
		*/
		public function getFieldName($fieldNumber)
		{
			if($this->hasField($fieldNumber))
			{
				return $this->fieldNames[$fieldNumber];
			}
			else
			{
				throw new Exception("Field at position " . $fieldNumber . " does not exist");
			}
		}

		/*
		**	Returns the field's position if this table has the given field, false if not.
		**	Accepts both the field name, as well as the field's position
		*/
		public function hasField($field)
		{
			if(is_integer($field))
			{
				if($field >= 0 && $field < $this->fieldCount)
				{
					return $field;
				}
				else
				{
					return false;
				}
			}
			else
			{
				for($i = 0; $i < $fieldCount; $i++)
				{
					if($this->fieldNames[$i] == $field)
					{
						return $i;
					}
				}
				return false;
			}
		}

		public function getData($record, $field)
		{
			if($record < 0 || $record >= $this->recordCount)
			{
				return false;
			}

			if(is_integer($field))
			{
				if($field < 0|| $field >= $this->fieldCount)
				{
					return false;
				}
			}
			else
			{
				$fieldNumber = $this->getFieldNumber($field);
				if($fieldNumber == -1)
				{
					return false;
				}
				$field = $fieldNumber;
			}

			try
			{
				return $this->records[$record][$field];
			}
			catch(Exception $e)
			{
				return false;
			}
			
		}
	}
?>
