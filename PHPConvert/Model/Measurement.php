<?php
	class Measurement
	{
		public $timestamp;
		public $value;
		public $duration;

		/*
		**	Holds a single measurement
		**	$timestamp		the time this measurement took place, or was started (epoch)
		**	$value			name of the category this variable belongs in
		**	$duration		name of the unit these measurements are in (abbreviated name)
		*/
		function __construct($timestamp, $value, $duration = null)
		{
			$this->timestamp = $timestamp;
			$this->value = $value;
			$this->duration = $duration;
		}
	}
?>