define([],function(){

	var domReady = function(fn,capturing){
		capturing = null == capturing ? true : capturing;
		
		var READY_STATES = ["complete","interactive"];
		var NOT_READY_STATES = ["loading"];
	
		if(READY_STATES.indexOf(document.readyState) != -1){
			fn();
		} else {
			window.addEventListener('DOMContentLoaded',fn,capturing);
		}
	};

	window.domReady = domReady;

	return {
		domReady: domReady
	};
	
});