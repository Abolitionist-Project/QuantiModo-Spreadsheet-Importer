<?php
	class MedHelperConverter extends Converter
	{
		const MEASUREMENT_FIELD_NUMBER = 1;	// The field number that contains the measurement

		// Positions of interesting data in a record row
		const MEASUREMENT_VALUE_POSITION = 0;
		const MEASUREMENT_UNIT_POSITION = 3;
		const MEASUREMENT_VARIABLE_POSITION = 4;

		public function convert($databaseView)
		{
			if($databaseView->getName() != "MedHelper")
			{
				return false;
			}

			// These arrays hold our measurementsets, indexed by VARIABLENAME.UNIT
			$measurementSets = array();

			$tableCount = $databaseView->getTableCount();
			for($i = 0; $i < $tableCount; $i++)
			{
				$table = $databaseView->getTable($i);

				$recordCount = $table->getRecordCount();
				for($n = 0; $n < $recordCount; $n++)
				{
					$rawMeasurement = $table->getData($n, MedHelperConverter::MEASUREMENT_FIELD_NUMBER);

					$dateStartPos = strpos($rawMeasurement, '<');
					if($dateStartPos === false)
					{
						continue;	// No timestamp, so we can't handle this record
					}
					else
					{
						$rawMeasurement = strip_tags($rawMeasurement);
						$valuePart = substr($rawMeasurement, 0, $dateStartPos);
						$timestampPart = substr($rawMeasurement, $dateStartPos);

						$measurementTimestamp = strtotime($timestampPart);

						$splitValuePart = explode(" ", $valuePart, 5);	// Split the value segment, limit to 5 segments so that our medicine name isn't split
						$measurementValue = $splitValuePart[MedHelperConverter::MEASUREMENT_VALUE_POSITION];
						$measurementVariable = $splitValuePart[MedHelperConverter::MEASUREMENT_VARIABLE_POSITION];
						$measurementUnit = strtolower($splitValuePart[MedHelperConverter::MEASUREMENT_UNIT_POSITION]);

						//echo $rawMeasurement;
						//echo $measurementVariable . ", " . $measurementValue . $measurementUnit . " at" . $timestampPart . "\n\n";

						if(isset($measurementSets[$measurementVariable . $measurementUnit])) 
						{
							$measurementSets[$measurementVariable . $measurementUnit]->measurements[] = new Measurement($measurementTimestamp, $measurementValue);
						}
						else
						{
							$newMeasurement = new Measurement($measurementTimestamp, $measurementValue);
							$newMeasurementSet = new MeasurementSet($measurementVariable, "Medications", $measurementUnit, "MedHelper", "SUM", array($newMeasurement));
							$measurementSets[$measurementVariable . $measurementUnit] = $newMeasurementSet;
						}
					}
				}
			}

			return $measurementSets;
		}
	}
?>