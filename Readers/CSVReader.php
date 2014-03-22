<?php
	class CSVReader extends Reader
	{
		function getDatabaseView($file)
		{
			$fileAsString = file_get_contents($file['tmp_name']);
			
			$tables = array(
					$this->toArrayTable("comma_separated", $this->parseCsv($fileAsString, ",")),
					$this->toArrayTable("semicolon_separated", $this->parseCsv($fileAsString, ";")),
					$this->toArrayTable("verticalbar_separated", $this->parseCsv($fileAsString, "|")),
					$this->toArrayTable("tab_separated", $this->parseCsv($fileAsString, "\t")),
				);
			$tableNames = array("comma_separated", "semicolon_separated", "verticalbar_separated", "tab_separated");

			return new ArrayDatabaseView($file["name"], $tableNames, $tables);
		}

		/*
		**	http://www.php.net/manual/en/function.str-getcsv.php
		*/
		private function parseCsv($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
		{
			return array_map(
				function ($line) use ($delimiter, $trim_fields) {
					return array_map(
						function ($field) {
							return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
						},
						$trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line)
					);
				},
				preg_split(
					$skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s',
					preg_replace_callback(
						'/"(.*?)"/s',
						function ($field) {
							return urlencode(utf8_encode($field[1]));
						},
						$enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string)
					)
				)
			);
		}

		/*
		**	Creates an ArrayTable from a CSV file converted to an array.
		**	Attempts to separate headers from the actual values.
		*/
		private function toArrayTable($tableName, $csvArray)
		{
			// Attempt to separate the headers from the values. This assumes a row with values contains either a date field, or a numeric value
			$fieldNames = array();
			$numHeaderLines = min(count($csvArray), 5);	// Check at most 5 lines for headers

			for($i = 0; $i < $numHeaderLines; $i++)
			{
				$row = $csvArray[$i];

				if(is_numeric($row[0]) || strtotime($row[0]) !== false)
				{
					break;
				}
				else
				{
					for($n = 0; $n < count($row); $n++)
					{
						$fieldNames[$n] = $row[$n];
					}
				}
			}

			$records = array_slice($csvArray, $i);

			// ArrayTable needs an array of field names, so we generate them if they don't exist
			if(empty($fieldNames))
			{	
				// If we have no records we just create an array with a single value, 0
				if(empty($records))
				{
					return new ArrayTable($tableName, array(0), $records);
				}
				// Otherwise take the number of columns, and name the fields after the column number
				else
				{
					return new ArrayTable($tableName, range(0, count($records[0]) - 1), $records);
				}
			}
			else
			{
				return new ArrayTable($tableName, $fieldNames, $records);
			}
		}
	}
?>