<?php	
	class ArrayDatabaseView extends DatabaseView
	{
		private $tableNames;
		private $tables;

		private $tableCount;

		function __construct($tableNames, $tables) 
		{
			if($tableNames == null)
			{
				throw new Exception("No table names were provided");
			}
			if($tables == null)
			{
				throw new Exception("No tables were provided");
			}

			$this->tableNames = $tableNames;
			$this->tables = $tables;

			$this->tableCount = count($tables);
		}

		/*
		**	Returns the number of tables in this DatabaseView
		*/
		public function getTableCount()
		{
			return $this->tableCount;
		}

		/*
		**	Returns the position of a table with this name, or false if it doesn't exist
		*/
		public function getTableNumber($tableName)
		{
			for($i = 0; $i < $tableCount; $i++)
			{
				if($tableName == $this->tableNames[$i])
				{
					return $i;
				}
			}
			return false;
		}

		/*
		**	Returns the name of the table at the given position
		*/
		public function getTableName($tableNumber)
		{
			if($this->hasTable($tableNumber))
			{
				return $this->tableNames[$tableNumber];
			}
			else
			{
				throw new Exception("Table at position " . $tableNumber . " does not exist");
			}
		}

		/*
		**	Returns true if this table exists in this view, false if not
		*/
		public function hasTable($table)
		{
			if(is_integer($table))
			{
				if($table >= 0 && $table < count($this->tableCount))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				foreach($this->tableNames as $tableNames)
				{
					if($tableNames == $table)
					{
						return true;
					}
				}
				return false;
			}
		}

		/*
		**	Returns the table at the given position, or with the given name
		*/
		public function getTable($table)
		{
			if(is_integer($table))
			{
				if(!$this->hasTable($table))
				{
					throw new Exception("Table at position " . $table . " does not exist");
				}
			}
			else
			{
				$tablePosition = $this->getTableName($table);
				if($tablePosition == -1)
				{
					throw new Exception("Table " . $table . " does not exist");
				}
				$table = $tablePosition;
			}
			return $this->tables[$table];
		}
	}
?>
