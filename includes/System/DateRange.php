<?php

namespace System;

class DateRange {

    //Build and return an array of string dates, formated appropriately for building the urls
	public static function toStringArray($numDays, $startDate = null, $format = "Y-m-d"){

		$urlDate = $startDate == null ? new \DateTime() : new \DateTime($startDate);

		$step = $numDays > 0 ? "-1 day" : "+1 day";
        
		$urlDates = array();
		for($i = 0; $i < $numDays; $i++){

			$urlDates[] = $urlDate->format($format);
			$urlDate->modify($step);
		}

		return $urlDates;
	}

}