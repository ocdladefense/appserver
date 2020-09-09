const Callout = (function() {



	function Callout(fn) {
		this.source = fn || null;
	}
	
	
	
	
	function send(params) {
		return Promise.resolve(this.source.call(null, params));
	}
	
	Callout.prototype = {
		send: send
	};



	return Callout;
})();