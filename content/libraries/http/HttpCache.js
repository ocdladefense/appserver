const HttpCache = (function(){
    


		
    function HttpCache(init) {
        this.name = typeof init === "string" ? init : init.name;
        
        // Create an "init" cache group
        // that immediately caches its URLs
        var initGroup = {
        	urls: init.startUrls || [],
        	enabled: true
        };
        
        this.groups = {
        	init: initGroup
        };
        
        // this.enabled = init.enabled;
        
        if(!this.name) {
            throw new Error("No name found.");
        }
    }
    
    
    

    var httpCache = {

			// Name of the cache; used by Cache API
			//  to distinguish this cache from others
			name: null,


			/**
			 * Keep URLs to be cached 
			 *  organized into groups with different 
			 * 	purposes.  For example, the "init" group
			 * 	would be URLs that should be cached when the app
			 *  is installed.
			 */
			groups: {},



			putGroup: function(group, urls) {
					this.groups[group] = urls;
			},
			
			
      
      /**
       * Do things when the cache is first initialized.
       *  Called by Service Worker.
       */
      init: function() {
				self["caches"].open(this.name)
				.then((cache) => {
						console.log('Opened cache: ',this.name);
						return cache.addAll(this.groups.init.urls);
				})
      }, 
       
       
      /**
       * Determine if any requested path should
       *  participate in the cache workflow
       *  or if it should be fetched from the Network.
       */
      cacheable: function(path){
				for(var group in this.groups){
					// console.log(this.group);
					var urls = this.groups[group].urls;
					// console.log("Checking ",urls," for urls.");
					if(urls.includes(path)
						&& !!this.groups[group].enabled) {
						return true;
					}
				}
			
				
				return false;
      },



      /**
       * Called every time a URL is fetched.
       *  Use this to determine if a requested URL
       *   should be returned from the cache
       *   alternatively, if the requested URL is not in the cache
       * 		determine if it *should be cached (or not.)
       */
			handleEvent: function(event) {

				var url, scriptPath;
				
				url = new UrlParser(event.request.url);

				// console.log("Checking ",url.path," in cache.");
				if(!this.cacheable(url.path)) {
					// console.log("Could not find ",url.path, " in cache.");
					return false;
				}

				console.log("Url ",url.path," was set to be cached: ",url);
				
				
				event.respondWith(
					self["caches"].match(event.request)
					.then((response) => {
						// Cache hit - return response
						return response ? response : this.cacheRoutine(event);
					})
				);
			},


			
			
			
			exists: function(event) {
				caches.open(this.name)
				.then(function(cache) {
					cache.put(event.request, responseToCache);
				});
			},
			
			
			/**
			 * If we make it this far, then retrieve the URL
			 *  from the Network and make the response available in
			 *   the cache.
			 */
			cacheRoutine: function(event){
				
				return fetch(event.request).then(
					(response) => {
						// Check if we received a valid response
						if(!response || response.status !== 200 || response.type !== 'basic') {
							return response;
						}

						// IMPORTANT: Clone the response. A response is a stream
						// and because we want the browser to consume the response
						// as well as the cache consuming the response, we need
						// to clone it so we have two streams.
						var responseToCache = response.clone();

						caches.open(this.name)
						.then(function(cache) {
							cache.put(event.request, responseToCache);
						});

						return response;
					}
				);
			},

			add: function(url) {
				var req;
				
				if(typeof url === "string") {
					req = new Request(url);
				}
				
				fetch(req).then( 
					(resp) => {
						// Check if we received a valid response
						if(!resp || resp.status !== 200 || resp.type !== 'basic') {
							return resp;
						}

						// IMPORTANT: Clone the response. A response is a stream
						// and because we want the browser to consume the response
						// as well as the cache consuming the response, we need
						// to clone it so we have two streams.
						var responseToCache = resp.clone();

						caches.open(this.name)
							.then(function(cache) {
								cache.put(req, responseToCache);
								console.log(req.url, " was cached!");
							});

						return resp;
					}
				);
			}
		};



    HttpCache.prototype = httpCache;

    return HttpCache;
})();
