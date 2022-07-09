<?php
foobar();
// print session_save_path();




function foobar() {
    $days = 30;
    $hours = 24;
    $length = $days * $hours * 60 * 60;
    session_name(SESSION_NAME);
    ini_set("session.auto_start", 0);
    ini_set("session.serialize_handler", "php_serialize");
    ini_set("session.session.use_strict_mode", 1);
    session_save_path(SESSION_DIR);
    // Set the amount of time before a loading page times out.  We wanted to extend ours, because we have long running queries etc.
    ini_set("max_execution_time","18000");

    // Set the amount of time before the session clean up takes place.  We extend ours because the default of 24 minutes is not enough.
    ini_set("session.gc_maxlifetime", $length);

    // Set the amount of time before the session cache expires.  We extend ours because the default of 24 minutes is not enough.
    # ini_set("session.cache_expire", $sessionLengthInSeconds);

    //  These two settings set the probability that the session clean up takes place between session initilization.  Currently 50% probability.
    # ini_set("session.gc_probability", "50");
    # ini_set("session.gc_divisor", "100");

    //  Set the cookie lifetime to 24 hours.
    $params = session_get_cookie_params();
    
    $params["lifetime"] = $length;
    // $params["httponly"] = true;
    // $params["samesite"] = "Strict";
    // $params["domain"]   = "appdev.ocdla.org";

    session_set_cookie_params($params);

    // var_dump(session_get_cookie_params());
    // exit;
}
