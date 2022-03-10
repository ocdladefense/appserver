<?php 

namespace Session;


function get_current_user() {

    $user = \Session::getUser();

    if($user == null) $user = new \User();

    return $user;
}

function is_admin(){

    return (defined("ADMIN_USER") && ADMIN_USER === true);
}