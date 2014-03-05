<?php
	require_once 'Model/ConverterManager.php';		// Holds all convertors (convertor defined in PHPConvert/Convertor)
	require_once 'Model/ReaderManager.php';			// Holds all readers (reader defined in PHPConvert/Reader/Reader)
	require_once 'Model/Storage.php';				// Defines a storage location
	require_once 'Model/Measurement.php';			// Holds a single measurement (timestamp, value, duration)
	require_once 'Model/MeasurementSet.php';		// Holds measurements with all properties that go with them
	
	class PHPConvert
	{
		public $currentUserId;

		public $converterManager;
		public $readerManager;
		public $storage;
		
		/*
		**	Construct a new PHPConvert instance
		**	$storage 			Storage location (must extend Storage)
		*/
		function __construct($userId, $storage) 
		{
			$currentUserId = $userId;

			$this->storage = $storage;
			$this->storage->init();

			$this->converterManager = new ConverterManager();
			$this->readerManager = new ReaderManager();
		}

		function handleFile($file)
		{
			$databaseView = $this->readerManager->getDatabaseView($file);
			if($databaseView == null)
			{
				echo "No reader could read this data";
				return;
			}

			$measurementSets = $this->converterManager->convert($databaseView);
			if($measurementSets == null)
			{
				echo "No converter could convert this file";
				return;
			}

			$this->storage->storeMeasurements($this->currentUserId, $measurementSets);
		}
	}
?>
