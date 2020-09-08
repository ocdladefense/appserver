const Repository = (function() {


	function Repository(name) {
		this.name = name;
		this.sources = {};
		this.index = null;
	}
	
	
	function get(data) {
		var name = null == this.index ? data : this.index(data);
		if(!name) {
			throw new Error("INVALID_INDEX_ERROR: The index, "+name+", cannot be used in this repository.");
		}
		var callout =  this.defaultIndex(name);
		if(!callout) {
			throw new Error("REPOSITORY_NOT_FOUND_ERROR: The specified element could not be found in this repository.");
		}
		
		return callout;
	}
	
	
	function defaultIndex(name) {
		return this.sources[name];
	}
	
	
	// A custom index should return a name
	//  identifier; so then pass that name to
	//   the default index.
	function customIndex(data) {
		return this.defaultIndex(this.index(data));
	}
	
	
	function add(key,source) {
		this.sources[key] = source;
	}

	Repository.prototype = {
		get: get,
		add: add,
		setIndex: function(index, self) {
			this.index = !!self ? this.index.bind(self) : index.bind(this);
		},
		getIndex: function() {
			return this.index;
		},
		defaultIndex: defaultIndex,
		customIndex: customIndex
	};

	return Repository;
})();