/*
Server prototype

A server should provide access to one or more services (caching, database, etc.)
The server constructor should be able to take a configuration objects ( a definition of what the server will do.)
 */ 


const Server = (function(){


    // Private variables.
    const SOFTWARE_VERSION = 0.01;



    // Instance properties
    var server = {
        name: null,

        cache: null,

        database: null,

        services: {},

				hasService: function(name){
					return !!(this.services[name]);
				},

        version: null,

        
        register: function(registration) {
            console.log(this.version);
        },

				handleEvent: function(e) {
					this.cache.handleEvent(e);
				},

        getInstaller: function() {
            // Perform install steps
            var fn = () => {
            	console.log("Wait until event fired!");
            	this.cache.init();
            };
            return (function(event) {
							event.waitUntil(fn());
						
							// Perform database init
							var request = this.database.init();
            }).bind(this);
        },

        setCache: function(cache) {
            this.cache = cache;
        },

        setDatabase: function(database) {
            this.database = database;
        },
        getDatabase: function(){
            return this.database;
        }
        

    };


    // Constructor
    function Server(init){
        this.version = init.version || 0;

    }

    Server.prototype = server;

    // Public/static 
    Server.time = function(){
        return Date.now();
    };


    



    return Server;

})();
