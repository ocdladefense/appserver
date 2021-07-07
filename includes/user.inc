<?php 

namespace Session;

function get_current_user($connectedApp = null, $flow = null){

    $connectedApp = "ocdla-sandbox-2.0";
    $flow = "webserver";

    return \Session::get($connectedApp,$flow, "user");
}



function user_get_initials() {

	return "G";
}


function is_admin_user(){

    return defined("ADMIN_USER") && ADMIN_USER === true;
}

function user_is_admin($user){

    return $user->data["user_type"] == "STANDARD";
}
