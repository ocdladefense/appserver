var DatabaseIndexedDb = (function(){


	var isQueryOp = function(op) {
		return false;
	};
	
	// Default transaction success handler; no need to repeat this handler.
	var txSuccess = function(resolve,results,event) {
		resolve(results);
		// resolve(event.target);
	};
	
	// Default transaction error handler; no need to repeat this handler.
	var txError = function(reject,reason) {
		reject(reason);
	};



	var database = {
		name: null,

		version: null,
		
		conn: null,

		stores:[],
		
		schemas: [],
	
		/**
		 * Cache open connections; if the connection has not been opened 
		 *  then return a Promise that can be consumed without having
		 * to repeat the connection code in each function.
		 */
		open: function(){
			if(!!this.conn) return this.conn;
			this.conn = new Promise( (resolve,reject) => {
				var request = indexedDB.open(this.name, this.version);
				request.onsuccess = (event) => {
					resolve(event.target.result);
				};
				request.onerror = (reason) => {
					reject(reason);
				};
			});
			
			return this.conn;
		},
		

		startTransaction: function(stores, records, method){
			var mode = ["put","add","delete"].includes(method) ? "readwrite" : "read";
			stores = Array.isArray(stores) ? stores : [stores];
			records = Array.isArray(records) ? records : [records];

			return this.open().then( (db) => {
				var tx = db.transaction(stores, mode);
				var s = tx.objectStore(stores);
				var results = [];
				
				records.forEach( (record) => {
					var r = s[method](record);
					r.onsuccess = (event) => { console.log(event); results.push(event.target.result); };
				});
				
				return new Promise( (resolve,reject) => {
					tx.commit();
					tx.oncomplete = txSuccess.bind(tx,resolve,results);
					tx.onerror = txError.bind(tx,reject);
				});
			});
			
		},


		add: function(store,records){
			records = Array.isArray(records) ? records : [records];
			return this.startTransaction(store,records,"add");
		},
		
		/**
		 * To update objects incrementally, we have to open a read transaction 
		 *  and get the object first then pass an updated version 
		 *   of the object using the IndexedDb put method.
		 */
		update: function(store,records) {
			return this.startTransaction(store,records,"put");
		},
		
		delete: function(store,records) {
			return this.startTransaction(store,records,"delete");
		},


		/**
		 * Helper function to either add a new object in a store
		 *  or to update it.  Depends on whether a key is provided.
		 *  If we're doing an update then query for the previous object in
		 *   order to do an incremental update.
		 */
		save: function(store,record){
			var result;
			if(null == record.id){
				delete record.id;
				result = this.add(store,record);
			}
			else {
				if(typeof record.id === "string") {
					record.id = parseInt(record.id);
				}
				result = this.getOne(store,record.id).then( (rnew) => {
					for(var prop in record) {
						rnew[prop] = record[prop];
					}
					return this.update(store,rnew);
				});
			}
			//result is a promise
			return result;
		},



		get: function(store,key,index) {
			
			if(!index) {	
				return this.getOne(store,key);
			} else {
				return this.query(store,key,index);
			}
		},
		
		
		/**
		 * Find an object in the store by its primary key.
		 *  Resolves to the object stored at that key.
		 */
		getOne: function(store,key){
		
			return this.open().then( (db) => {
				var tx = db.transaction([store],"readonly");
				var objectStore = tx.objectStore(store);
				var request = objectStore.get(key);
		
				return new Promise( (resolve,reject) => {
					request.onerror = function(event) {
						// Handle errors!
					};
					request.onsuccess = function(event) {
						resolve(event.target.result);
					};
				});
			
			});

		},
		
		query: function(obj){
			console.log("Object is, ",obj);
			var store = obj.store;
			var index = obj.index;
			var value = obj.value;

			return this.open().then( (db) => {
				var tx, objectStore, theIndex, singleKeyRange;
				
				try {
					tx = db.transaction([store],"readonly");

					objectStore = tx.objectStore(store);

					theIndex = objectStore.index(index);
						//request = theIndex.get(key);
					singleKeyRange = IDBKeyRange.only(value);
				} catch(e) {
					return Promise.reject(e.message);
				}
			
				return new Promise( (resolve,reject) => {
					var data = [];
					var request = theIndex.openCursor(singleKeyRange);
					request.onsuccess = function(event) {
						var cursor = event.target.result;
						if (cursor) {
							data.push(cursor.value);
							cursor.continue();
						} else {
							resolve(data);
						}
					};
				});

			});	
		},

		
		init: function() {

			var request = indexedDB.open(this.name, this.version);

			request.onerror = function(event) {
				// Do something with request.errorCode!
			};

			request.onsuccess = function(event) {
				// console.log(request.onsuccess);
			};

			request.onupgradeneeded = (event) => {
				console.log("Upgrading database");

				var db = event.target.result;

				this.schemas.forEach((store) => {
					var objectStore;
					if(!store.autoIncrement){
						objectStore = db.createObjectStore(store.name, {keyPath:store.keyPath});
					}
					else{
						objectStore = db.createObjectStore(store.name,{keyPath:store.keyPath, autoIncrement:true});
					}
					store.indexes.forEach(function(index){
						objectStore.createIndex(index.name,index.path,index.options);
					});
				});
			};

			return request;
		},
	};


	
	
	function DatabaseIndexedDb(init){
		// set the schema; set the database name
		// Database name is the minimum so throw an error if it's not defined.
		if(typeof init === "string") {
			this.name = init;
		} else {
			this.name = init.name;
			this.version = init.version;
			this.stores = init.stores;
			this.schemas = init.schemas; // Used specifically to create/upgrade IndexedDb database.
		}
		
		if(!this.name) throw new Error("DATABASE_INITIALIZATION_ERROR: No database name provided.");
	}
	
	DatabaseIndexedDb.prototype = database;
	
	return DatabaseIndexedDb;
})();