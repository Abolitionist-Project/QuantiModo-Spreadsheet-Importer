<?php
	class QuantimodoStorage extends Storage
	{
		public $measurementsDb;

		private $variables;
		private $variableCategories;
		private $units;
		private $sources;
		
		public function init()
		{
			$this->initConnectDb();
			$this->initMeasurementsDb();

			$this->loadSources();
			$this->loadUnits();
			$this->loadVariableCategories();
			$this->loadVariables();
		}

		public function initConnectDb()
		{
			try 
			{
				$this->connectDb = new PDO('mysql:host=' . CONNECT_DB_HOST . ';dbname=' . CONNECT_DB_NAME . ';charset=utf8', CONNECT_DB_USER, CONNECT_DB_PASSWORD);
				$this->connectDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->connectDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			} 
			catch(Exception $e) 
			{
				echo "Could not connect to the connectors database.";
				die;
			}
		}

		public function initMeasurementsDb()
		{
			try 
			{
				
				$this->measurementsDb = new PDO('mysql:host=' . MEASUREMENTS_DB_HOST . ';dbname=' . MEASUREMENTS_DB_NAME . ';charset=utf8', MEASUREMENTS_DB_USER, MEASUREMENTS_DB_PASSWORD);
				$this->measurementsDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->measurementsDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			} 
			catch(Exception $e) 
			{
				echo "Could not connect to the measurements database.";
				die;
			}
		}

		/*
		**	Loads IDs for all sources in the measurements database
		*/
		private function loadSources()
		{
			$query = "
				SELECT
					id,
					name
				FROM `sources`
				";
			$q = $this->measurementsDb->prepare($query);
			$q->execute();

			while($row = $q->fetch(PDO::FETCH_ASSOC))
			{
				$this->sources[$row['name']] = $row['id'];
			}
		}

		/*
		**	Loads IDs for all units in the measurements database
		*/
		private function loadUnits()
		{
			$query = "
				SELECT
					id,
					`abbreviated-name`
				FROM `units`
				";
			$q = $this->measurementsDb->prepare($query);
			$q->execute();

			while($row = $q->fetch(PDO::FETCH_ASSOC))
			{
				$this->units[$row['abbreviated-name']] = $row['id'];
			}
		}

		/*
		**	Loads IDs for all variable categories in the measurements database
		*/
		private function loadVariableCategories()
		{
			$query = "
				SELECT
					id,
					name
				FROM `variable_categories`
				";
			$q = $this->measurementsDb->prepare($query);
			$q->execute();

			while($row = $q->fetch(PDO::FETCH_ASSOC))
			{
				$this->variableCategories[$row['name']] = $row['id'];
			}
		}

		/*
		**	Loads IDs for all variables in the measurements database
		*/
		private function loadVariables()
		{
			$query = "
				SELECT
					id,
					name
				FROM `variables`
				";
			$q = $this->measurementsDb->prepare($query);
			$q->execute();

			while($row = $q->fetch(PDO::FETCH_ASSOC))
			{
				$this->variables[$row['name']] = $row['id'];
			}
		}

		/*
		**	Creates a new variable
		*/
		public function storeVariable($name, $categoryId, $unitId, $combinationOperation)
		{
			$query = "
				INSERT INTO `variables` (name, `variable-category`, `default-unit`, `combination-operation`) 
				VALUES (:name, :categoryId, :unitId, :combinationOperation)	
				ON DUPLICATE KEY UPDATE
					`variable-category` = VALUES(`variable-category`),
					`default-unit` = VALUES(`default-unit`),
					`combination-operation` = VALUES(`combination-operation`)
				";		

			$q = $this->measurementsDb->prepare($query);

			$q->bindParam(":name", $name);
			$q->bindParam(":categoryId", $categoryId);
			$q->bindParam(":unitId", $unitId);
			$q->bindParam(":combinationOperation", $combinationOperation);

			$q->execute();

			$id = $this->measurementsDb->lastInsertId('id');
			echo " [INFO] Created variable with id: " . $id . "\n";
			$this->variables[$name] = $id;
			return $this->measurementsDb->lastInsertId('id');
		}

		/*
		**	Creates a new source
		*/
		public function storeSource($name)
		{
			$query = "
				INSERT INTO `sources` (name) 
				VALUES (:name);
				";		

			$q = $this->measurementsDb->prepare($query);

			$q->bindParam(":name", $name);

			$q->execute();

			$id = $this->measurementsDb->lastInsertId('id');
			echo " [INFO] Created source with id: " . $id . "\n";
			$this->sources[$name] = $id;
			return $id;
		}

		//TODO: return error here
		public function storeMeasurements($userId, $measurementSets)
		{
			echo " [INFO] Storing measurements for user " . $userId . "\n";

			$timestampUpperLimit = round(microtime(true)) + 604800;	// Set uppper limit to a week in the future
			$timestampLowerLimit = 1262300400;						// Lower limit is equal to 01/01/2010

			$this->measurementsDb->beginTransaction();
		
			// Construct query
			$query = "
				INSERT INTO `measurements` (user, variable, source, timestamp, value, unit, duration)
				VALUES(:user, :variable, :source, :timestamp, :value, :unit, :duration)
				ON DUPLICATE KEY UPDATE value = VALUES(value), unit = VALUES(unit), duration = VALUES(duration);
				"; 
			$q = $this->measurementsDb->prepare($query);

			$numMeasurements = 0;
			foreach($measurementSets as $measurementSet)
			{
				if(get_class($measurementSet) !== 'MeasurementSet')
				{
					echo " [ERROR] Not a valid measurement set\n";
					continue;
				}

				// Get unit for this set of measurements
				if(!array_key_exists($measurementSet->unitName, $this->units))
				{
					echo " [ERROR] unit " . $measurementSet->unitName . " doesn't exist\n";
					continue;
				}
				$unitId = $this->units[$measurementSet->unitName];


				// The source these variables will be stored with
				if(!array_key_exists($measurementSet->sourceName, $this->sources))
				{
					echo " [INFO] Creating source " . $measurementSet->sourceName . "\n";
					$sourceName = $this->storeSource($measurementSet->sourceName);
					if($sourceName == -1)
					{
						echo " [ERROR] Can't create source";
						continue;
					}
					continue;
				}
				$sourceName = $this->sources[$measurementSet->sourceName];

				// Variable for this set
				if(!array_key_exists($measurementSet->variableName, $this->variables))
				{
					echo " [INFO] Creating variable " . $measurementSet->variableName . "\n";

					if(!array_key_exists($measurementSet->categoryName, $this->variableCategories))
					{
						echo " [ERROR] Can't find category " . $measurementSet->categoryName . ", can't create variable\n";
						continue;
					}
					$categoryId = $this->variableCategories[$measurementSet->categoryName];

					// Convert combinationOperation the the number stored in the database
					if($measurementSet->combinationOperation === "SUM")
					{
						$measurementSet->combinationOperation = 0;
					}
					else if($measurementSet->combinationOperation == "MEAN")
					{
						$measurementSet->combinationOperation = 1;
					}
					else
					{
						echo " [ERROR] Invalid combination operation " . $measurementSet->combinationOperation . ", can't create variable\n";
						continue;
					}
					// Finally add the variable and retrieve the id to store the measurements with
					$variableId = $this->storeVariable($measurementSet->variableName, $categoryId, $unitId, $measurementSet->combinationOperation);
					if($variableId == -1)
					{
						echo " [ERROR] Can't create variable";
						continue;
					}
				}
				$variableId = $this->variables[$measurementSet->variableName];

				// Loop through all measurements to store them
				foreach($measurementSet->measurements as $measurement)
				{


					$q->bindParam(":user", $userId);
					$q->bindParam(":variable", $variableId);
					$q->bindParam(":source", $sourceName);
					$q->bindParam(":unit", $unitId);
					$q->bindValue(":timestamp", $measurement->timestamp / 60);
					$q->bindParam(":value", $measurement->value);
					$q->bindValue(":duration", $measurement->duration);
					
					$q->execute();

					$numMeasurements++;
				}
			}

			$this->measurementsDb->commit();

			return $numMeasurements;
		}
	}
?>
