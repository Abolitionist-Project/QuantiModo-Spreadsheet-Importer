<?php
	class HTMLTableReader extends Reader
	{
		function getDatabaseView($file)
		{
			$fileAsString = file_get_contents($file['tmp_name']);
			
			if($this->containsTable($fileAsString))
			{
				$DOM = new DOMDocument;
				$DOM->loadHTML($fileAsString);

				$tableNames = array();
				$tables = array();

				$tableElements = $DOM->getElementsByTagName('table');
				$tableNum = 0;
				foreach($tableElements as $table)
				{
					$newTableName = strval($tableNum);
					$tables[] = $this->parseTable($table, $newTableName);
					$tableNames[] = $newTableName;

					$tableNum++;
				}

				$titleElements = $DOM->getElementsByTagName('title');
				if(empty($titleElements))
				{
					return new ArrayDatabaseView($file["name"], $tableNames, $tables);
				}
				else
				{
					return new ArrayDatabaseView($titleElements->item(0)->nodeValue, $tableNames, $tables);
				}
			}
			else
			{
				return null;
			}
		}

		/*
		**	True if the string contains table tags, false if not
		*/
		private function containsTable($html)
		{
			$tag = "table";
			return preg_match('/<'.$tag.'[^>]*>/i', $html) != 0 && preg_match('/<\/'.$tag.'>/i', $html) != 0;
		}

		/*
		**	Processes a table DOM element
		*/
		private function parseTable($table, $tableName)
		{
			$items = $table->getElementsByTagName('tr');
			$records = array();
			foreach ($items as $node)
			{
				$records[] = $this->parseTableRow($node->getElementsByTagName('td'));
			}

			$items = $table->getElementsByTagName('th');
			$fieldNames = array();
			foreach ($items as $node)
			{
				$fieldNames[] = $node->nodeValue;
			}

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

		/*
		**	Processes a tr DOM element
		*/
		private function parseTableRow($elements)
		{
			$records = array();
			foreach ($elements as $element)
			{
				$records[] = $this->stripOuterTags('td', $element->ownerDocument->saveHTML($element));
			}
			return $records;
		}

		/*
		**	Removes the specified tags from the input string. Used to get rid of the <td> tags returned by saveHTML
		*/
		private function stripOuterTags($tag, $string)
		{
			$string = preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
			$string = preg_replace('/<\/'.$tag.'>/i', '', $string);
			return $string;
		} 
	}
?>