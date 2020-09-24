// /*
// Service worker
//  */ 

var SUPPLEMENT_PATH = "modules/webconsole/assets";
var networkStatus = true;

self.onmessage = function(e){
	var data = e.data;
	console.log("Service Worker message is: ",e);
	if(data.command == "connected") {
		networkStatus = data.message;
		console.log("Updated network status to: "+data.message);
	}
};

self.importScripts(SUPPLEMENT_PATH + "/worker/settings.js");
self.importScripts(SUPPLEMENT_PATH + "/lib/Server.js");
self.importScripts(SUPPLEMENT_PATH + "/lib/UrlParser.js");
self.importScripts(SUPPLEMENT_PATH + "/lib/http/HttpCache.js");
self.importScripts(SUPPLEMENT_PATH + "/lib/database/DatabaseIndexedDb.js");


const myServer = new Server(config.server);
myServer.setCache(new HttpCache(config.cache));
myServer.setDatabase(new DatabaseIndexedDb(config.database));



self.addEventListener("install", myServer.getInstaller());

self.addEventListener("activate",function(event) {
	event.waitUntil(clients.claim());
});

// Let the server object decide how to handle fetch events.
self.addEventListener("fetch", myServer);