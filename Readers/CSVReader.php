<?php
	class CSVReader extends Reader
	{
		function getDatabaseView($file)
		{
			$handle =  $handle = fopen($file['tmp_name'], "r");

			$fieldNames = array();	// One dimensional array of field names
			$records = array();		// Two dimensional array containing all records

			// Loop until we found all headers
			while(($row = fgetcsv($handle)) !== FALSE) 
			{
				if(is_numeric($row[0]))
				{
					//echo "Cell at pos 0 numeric (" . $row[0] . "), done with headers\n";
					$records[] = $row;
					break;
				}
				else if(strtotime($row[0]) !== false)
				{
					//echo "Cell at pos 0 is a date (" . $row[0] . "=" . strtotime($row[0]) . "), done with headers\n";
					$records[] = $row;
					break;
				}

				for($i = 0; $i < count($row); $i++)
				{
					//echo "Set header " . $row[$i] . " at pos" . $i . "\n";
					$fieldNames[$i] = $row[$i];
				}
			}

			while(($row = fgetcsv($handle)) !== FALSE) 
			{
				//print_r($row);
				$records[] = $row;
			}

			$tableNames = array($file['name']);
			$tables = array(new ArrayTable($file['name'], $fieldNames, $records));

			return new ArrayDatabaseView($tableNames, $tables);
		}
	}
?>