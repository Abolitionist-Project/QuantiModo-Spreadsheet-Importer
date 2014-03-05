<?php	
	require_once 'Reader/Reader.php';

	require_once 'Readers/CSVReader.php';

	class ReaderManager
	{	
		private $readers;

		function __construct() 
		{
			$this->readers = array();

			$this->readers[] = new CSVReader();	// CSVReader has to be the last reader
		}

		public function getDatabaseView($file)
		{
			foreach($this->readers as $reader)
			{
				$databaseView = $reader->getDatabaseView($file);
				if($databaseView != null)
				{
					return $databaseView;
				}
			}

			return null;
		}
	}
?>
