<?php 

namespace Session;


function get_current_user($connectedApp = null, $flow = null){

    return \Session::get($connectedApp, $flow, "user");
}



function user_get_initials() {

	return "TU";
}


function user_is_admin($user = null){

    return (defined("ADMIN_USER") && ADMIN_USER === true) || $user->data["user_type"] == "STANDARD";
}


function user_is_member($user = null){

    return $user->data["user_type"] != "STANDARD";
}
