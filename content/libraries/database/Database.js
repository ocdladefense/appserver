const Database = (function(){

	var global = window;
	
	
	const connections = {};
	
	var database = {
		name: null,
	
		// Virtual
		getTable: function(tableName){},
		
		addRecord:function(record, name){},
		
		getRecords: function(tableName){},
		
		persistTable: function(tableName){},
		
		updateRecord: function(record, tableName){},
		
		dumpTable:function(tableName){},
	};
	
	function Database(init){
		// set the schema; set the database name
		this.name = init;
	}
	
	Database.prototype = database;
	Database.connect = function(init){
		var driver = init.driver;
		var conn;

		return new window[driver](init);
	};
	
	return Database;
})();