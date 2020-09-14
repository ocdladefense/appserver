<?php

	function getClassName($base) {
		$className = ucwords($moduleName,"-\t\r\n\f\v");
		$className = str_replace("-","",$className)."Module";
		$moduleClass = new $className();
	}