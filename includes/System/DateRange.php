<?php

namespace System;

class DateRange {

    //Build and return an array of string dates, formated appropriately for building the urls
	public static function toStringArray($numDays, $format = "Y-n-j"){

		$urlDates = array();

		$step = $numDays > 0 ? "-1 day" : "+1 day";

		// Create a list of date strings to iterate over.
        $urlDate = new \DateTime();
        
		for($i = 0; $i < $numDays; $i++){

			$urlDates[] = $urlDate->format($format);
			$urlDate->modify($step);
		}

		return $urlDates;
	}

}