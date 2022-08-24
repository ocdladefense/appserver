<?php


function cache_set($key, $val) {
	$val = var_export($val, true);
	// HHVM fails at __set_state, so just use object cast for now
	$val = str_replace('stdClass::__set_state', '(object)', $val);
	// Write to temp file first to ensure atomicity
	$tmp = CACHE_DIR ."/$key." . uniqid('', true) . '.tmp';
	file_put_contents($tmp, '<?php $val = ' . $val . ';', LOCK_EX);
	rename($tmp, CACHE_DIR."/$key");
 }


 function cache_get($key) {
    @include CACHE_DIR."/$key";
    return isset($val) ? $val : null;
}


function cache_delete($filename) {

	return unlink(CACHE_DIR . "/$filename");
}