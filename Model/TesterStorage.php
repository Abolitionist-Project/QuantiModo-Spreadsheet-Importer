<?php
	class TesterStorage extends Storage
	{

		function __construct()
		{
		}

		public function init()
		{
			// Nothing needs to be done here.
		}

		public function storeMeasurements($userId, $measurementSets)
		{
			//TODO validate measurements
			echo " [INFO] Storing measurements for user " . $userId . "\n";

			$timestampUpperLimit = round(microtime(true)) + 604800;	// Set upper limit to a week in the future
			$timestampLowerLimit = 946684800;						// Lower limit is equal to 01/01/2000

			$numMeasurements = 0;
			foreach($measurementSets as $measurementSet)
			{
				if(get_class($measurementSet) !== 'MeasurementSet')
				{
					echo " [ERROR] Not a valid measurement set\n";
					break;
				}

				if(!isset($measurementSet->unitName))
				{
					echo " [ERROR] Missing unit in measurementSet\n";
					break;
				}

				if(!isset($measurementSet->sourceName))
				{
					echo " [ERROR] Missing source in measurementSet\n";
					break;
				}

				if(!isset($measurementSet->variableName))
				{
					echo " [ERROR] Missing variable in measurementSet\n";
					break;
				}

				if(!isset($measurementSet->categoryName))
				{
					echo " [ERROR] Missing category in measurementSet\n";
					break;
				}

				if(!isset($measurementSet->combinationOperation))
				{
					echo " [ERROR] Missing combination operation in measurementSet\n";
					break;
				}
				if($measurementSet->combinationOperation != "SUM" && $measurementSet->combinationOperation != "MEAN")
				{
					echo " [ERROR] Invalid combination operation " . $measurementSet->combinationOperation . ", Must be 'SUM' or 'MEAN'\n";
					break;
				}

				foreach($measurementSet->measurements as $measurement)
				{
					if(gettype($measurement) != 'object')
					{
						echo " [ERROR] Not a valid measurement object (type = " . gettype($measurement) . ")\n";
						break;
					}
					if(get_class($measurement) !== 'Measurement')
					{
						echo " [ERROR] Not a valid measurement object (class = " . get_class($measurement) . ")\n";
						break;
					}

					// If this measurement is more than a week in the future throw an error
					if(!is_numeric($measurement->timestamp))
					{
						echo " [ERROR] timestamp is not a number: " . $measurement->timestamp . "\n";
						break;
					}
					if($measurement->timestamp > $timestampUpperLimit)
					{
						echo " [ERROR] timestamp too far in future: " . $measurement->timestamp . "\n";
						break;
					}
					if($measurement->timestamp < $timestampLowerLimit)
					{
						echo " [ERROR] timestamp too far in past: " . $measurement->timestamp . "\n";
						break;
					}

					if(!isset($measurement->value) || !is_numeric($measurement->value))
					{
						echo " [ERROR] Missing or invalid value for measurement\n";
						break;
					}

					if(isset($measurement->duration) && !is_numeric($measurement->duration))
					{
						echo " [ERROR] Invalid duration for measurement\n";
						break;
					}

					$numMeasurements++;
				}

				echo " [INFO] Done verifying measurementset with " . count($measurementSet->measurements) . " measurements: \n";
				echo "        Var: " . $measurementSet->variableName . "\n";
				echo "        Src: " . $measurementSet->sourceName . "\n";
				echo "        Uni: " . $measurementSet->unitName . "\n";
				echo "        Cat: " . $measurementSet->categoryName . "\n";
				echo "        Com: " . $measurementSet->combinationOperation . "\n";
				echo " [INFO] Please make sure the unit and category exist in production before deploying this converter\n";
			}

			return $numMeasurements;
		}
	}
?>
