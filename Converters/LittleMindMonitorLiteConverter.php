<?php
	class LittleMindMonitorLiteConverter extends Converter
	{
		public function convert($databaseView)
		{
			if(!$databaseView->hasTable("comma_separated"))
			{
				return false;
			}

			// Get the table now that we are sure it exists
			$table = $databaseView->getTable("comma_separated");

			// Get the fields we need
			$timestampField = $table->getFieldNumber("TimeStamp");
			//$qualityField = $table->getFieldNumber("Quality");
			$attentionField = $table->getFieldNumber("Attention");
			$meditationField = $table->getFieldNumber("Meditation");
			//$attention5sField = $table->getFieldNumber("Attention(ave5s");
			//$meditation5sField = $table->getFieldNumber("Meditation(ave5s)");
			//$attention10sField = $table->getFieldNumber("Attention(ave10s)");
			//$meditation10sField = $table->getFieldNumber("Meditation(ave10s)");

			// If one of our fields are missing we can't handle this file
			if($timestampField === false || $attentionField == false || $meditationField == false)
			{
				return false;
			}

			// These arrays hold our measurements
			$attentionMeasurements = array();
			$meditationMeasurements = array();

			$recordCount = $table->getRecordCount();
			$totalAttentionMeas = 0;	// Holds the sum of the past 60 measurements
			$totalMeditationMeas = 0;
			for($i = 0; $i < $recordCount; $i++)
			{
				if($i % 70 == 0)	// We summed 60 measurements, approx, a minute
				{
					$timestamp = strtotime($table->getData($i, $timestampField)) - 35;		// Set the timestamp right in the middle of the measurements
					$avgAttention = $totalAttentionMeas / 70;	// Calculate the average
					$avgMeditation = $totalMeditationMeas / 70;

					$totalAttentionMeas = 0;					// Reset the totals
					$totalMeditationMeas = 0;

					$attentionMeasurements[] = new Measurement($timestamp, $avgAttention);	// Store the measurements
					$meditationMeasurements[] = new Measurement($timestamp, $avgMeditation);
				}
				
				$attentionMeas = $table->getData($i, $attentionField);
				if($attentionMeas !== false && $attentionMeas != 0)
				{
					$totalAttentionMeas += $attentionMeas;
				}

				$meditationMeas = $table->getData($i, $meditationField);
				if($meditationMeas !== false && $meditationMeas != 0)
				{
					$totalMeditationMeas += $meditationMeas;
				}
			}

			return array(
					new MeasurementSet("eSense Attention", "Vital Signs", "%", "Little Mind Monitor", "MEAN", $attentionMeasurements),
					new MeasurementSet("eSense Meditation", "Vital Signs", "%", "Little Mind Monitor", "MEAN", $attentionMeasurements)
				);
		}
	}
?>