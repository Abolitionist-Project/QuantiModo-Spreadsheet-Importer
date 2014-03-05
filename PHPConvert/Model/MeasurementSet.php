<?php
	class MeasurementSet
	{
		public $variableName;
		public $categoryName;
		public $unitName;
		public $sourceName;
		public $combinationOperation;
		public $measurements;

		/*
		**	Holds a set of measurements and properties that go with the set
		**	$variableName	name of this variable
		**	$categoryName	name of the category this variable belongs in
		**	$unitName		name of the unit these measurements are in (abbreviated name)
		**	$sourceName		name of the source of these measurements
		**	$combinationOp.	what to do when two measurements are combined ("ADD" or "MEAN")
		**	$measurements 	array of Measurements
		*/
		function __construct($variableName, $categoryName, $unitName, $sourceName, $combinationOperation, $measurements)
		{
			$this->variableName = $variableName;
			$this->categoryName = $categoryName;
			$this->unitName = $unitName;
			$this->sourceName = $sourceName;
			$this->combinationOperation = $combinationOperation;
			$this->measurements = $measurements;
		}
	}
?>