
const Route = (function() {


	function Route(path,params) {
		this.path = path;
		this.params = params;
	}
	
	Route.prototype = {
		getPath: function() { return this.path; },
		getParams: function() { return this.params; }
	};

	return Route;
})();