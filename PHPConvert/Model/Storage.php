<?php
	abstract class Storage
	{
		public $connectDb;

		abstract public function init();

		abstract public function storeMeasurements($userId, $measurementSets);
	}
?>
