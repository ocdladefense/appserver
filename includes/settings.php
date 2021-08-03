<?php

$sessionLengthInHours = 24;
$sessionLengthInSeconds = $sessionLengthInHours * 60 * 60;

// Set the amount of time before a loading page times out.  We wanted to extend ours, because we have long running queries etc.
ini_set("max_execution_time","18000");

// Set the amount of time before the session clean up takes place.  We extend ours because the default of 24 minutes is not enough.
ini_set("session.gc_maxlifetime", $sessionLengthInSeconds);

// Set the amount of time before the session cache expires.  We extend ours because the default of 24 minutes is not enough.
ini_set("session.cache_expire", $sessionLengthInSeconds);

//  These two settings set the probability that the session clean up takes place between session initilization.  Currently 50% probability.
ini_set("session.gc_probability", "50");
ini_set("session.gc_divisor", "100");

//  Set the cookie lifetime to 24 hours.
$currentCookieParams = session_get_cookie_params();
$currentCookieParams["lifetime"] = $sessionLengthInSeconds;
session_set_cookie_params($currentCookieParams);