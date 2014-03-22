<?php	
	require_once 'Converter.php';

	require_once 'Converters/LittleMindMonitorLiteConverter.php';
	require_once 'Converters/MedHelperConverter.php';
	
	class ConverterManager
	{	
		private $converters;

		function __construct() 
		{
			$this->converters = array();

			$this->converters[] = new MedHelperConverter();
			$this->converters[] = new LittleMindMonitorLiteConverter();
		}

		public function convert($databaseView)
		{
			foreach($this->converters as $converter)
			{
				$measurementSets = $converter->convert($databaseView);
				if($measurementSets != null)
				{
					return $measurementSets;
				}
			}

			return null;
		}
	}
?>
