var DatabaseArray = (function(){


	var autoIncrement = 0;

	function saveToDatabase(note){
			var today = new Date();
			today.getDate();
			app.databases["mydb"]["tables"]["notes"]
			console.log("THE BODY "+body);
			app.database["date"] = today;
			app.database["body"] = body;
			console.log(app.database);
	}


	var dbArray = {
		name: null,
		

		tables:{
			

		},

		note: {
			timeStamp:2999,
			body:"hello from mars"
		},

		addTable: function(name){
			this.tables[name] = {};
		},
	
	
		getTable: function(tableName){
				var table = this.tables[tableName];
				if(table == null){
					throw new Error("Table is undefined" + "  "+ tableName);
				}
				return table;
		},

		save: function(record, name){
				var newRecord;
				if(null == record.id){
					newRecord = this.addRecord(record,name);
					
				}
				else{
					newRecord = this.updateRecord(record,name)
				}
				return newRecord;
				
				
		},


		addRecord:function(record, name){
			record.id = autoIncrement++;
			record.created = Date.now();
			var table = this.getTable(name);
			table[record.id] = record;
			console.log("record added");			
			return record;
		},
		getRecords: function(tableName){
				return this.database[tableName];
		},
		persistTable: function(tableName){
				//grab pointer to local mySql database
		},
		//needs to update instead of replace
		updateRecord: function(record, name){
			var table = this.getTable(name);

			for(var fieldName in record){
				table[record.id][fieldName] = record[fieldName];
			}
			//table[record.id] = record;
			console.log("record updated");
			return record;
		},
		dumpTable:function(tableName){
				console.log(this.tables);
		},

		//define save method that pushes stuff onto the database array
	
		getDatabase: function(){
				return this.database;
		},

		saveToDatabase: function(record,name){
			app.databases["mydb"]["tables"][name.length +1] = record;
		}
	};
	
	dbArray.prototype = Database;
	
	
	function DatabaseArray(init){
		// set the schema; set the database name
		this.name = init.name;
		console.log(init.tables);

		for(var name in init.tables){
			this.addTable(name);
		}
	}
	
	DatabaseArray.prototype = dbArray;

	return DatabaseArray;
})();