

/**
 * domReady dependency for Salesforce Community Apps.
 *
 *  The document state is handy for 
 *  detecting when the document has loaded and
 *  is ready for DOM manipulation.
 *
 *    Examples:
 *
 *     domReady( () => alert('Page has loaded.'); );
 *
 */


const domReady = (function(fn) {
    let _global = window || global;
    
	if(typeof define != "undefined") {
		define([],fn);
	} else {
		return fn();
	}
})(function(){

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

	return domReady;
	
});







// let remoting = ForceRemoting.invokeAction(null);
// let thePromise =
const ForceRemoting = (function(fn) {
    let _global = window || global;
    
	if(typeof define != "undefined") {
		define([],fn);
	} else {
		return fn(_global);
	}
})(function(g){

    // Enable of disable logging; facilitate log() shorthand call.
	let DEBUG = false;
    let console = g && g.console ? g.console : {log:function(){},error:function(){},info:function(){}};
    // console.log = DEBUG ? console.log : function(){void(0)};
	var log = function(m){ DEBUG && console.log(m);};

    
	function mergeArgs(source,dest){
		dest = dest || [];
        source = source instanceof Array ? source : [source];
		for(var i = 0; i<source.length; i++){
			dest[i] = source[i];
		}
		return dest;
	}
	
	
	function invokeAction(ns) {
		return function(controller,method,theArgs) {
			return new Promise(function(resolve,reject){
				var fn, cb, nArgs;
			
				try {
                    fn = !!ns ? window[ns][controller][method] : window[controller][method];
			
					if(!fn) throw new Error('This controller/method pair does not exist.');
			
					cb = function(result,event){
						if (event.status) {
							resolve(result);
						} else {
							console.info('An error occurred for the event, ',event,'. The args were: ',nArgs);
							var message = 'Error when executing '+event.method+': '+event.message;
							reject(message);
						}
					};


                    nArgs = typeof theArgs === "undefined" ? [] : mergeArgs(theArgs);
                    
					nArgs.push(cb,{buffer:false,escape:false});

					fn.apply(fn,nArgs);

				} catch(e) {
					console.error(e);
					reject(e.message);
				}
			});
		};
	}
	
    

    
	return {
		invokeAction: invokeAction
	};

});


 
    



const Device = (function(){
    
    function iOS() {
    
      var iDevices = [
        'iPad Simulator',
        'iPhone Simulator',
        'iPod Simulator',
        'iPad',
        'iPhone',
        'iPod'
      ];
    
      if (!!navigator.platform) {
        while (iDevices.length) {
          if (navigator.platform === iDevices.pop()){ return true; }
        }
      }
    
      return false;
    }
    




    return {
        iOS: iOS  
    }; 
})();





const FileCsv = (function(){
  
    /**
     * Generate a client-side file for iOS devices.
     * 
     * Accepts content to be used as the content for the file.
     */ 
    function evergreenToCsv(blob, filename){

        let download = document.createElement('a');
        let url = URL.createObjectURL(blob);
        download.href = url;
        download.setAttribute("download", filename);
            
        return download;
    }
        
    
    /**
     * Generate a CSV file for iOS devices.
     * 
     */
    function iosToCsv(blob, filename){
        
        var reader = new FileReader();
        
        reader.onload = function(e) {
            console.log(e);
             var bdata = btoa(reader.result);
             var datauri = 'data:text/csv;base64,' + bdata;
             window.open(datauri);
             newWindow = setTimeout(function() {
                     newWindow.document.title = filename;
             }, 10);
        };
        
        
        reader.readAsBinaryString(blob);
        
    
        /*
        var reader = new FileReader();

        reader.onload = function(e){
            window.location.href = reader.result;
        }
        
        reader.readAsDataURL(content);
        */
        return reader;
    }
    
        

   function getAsCsv(nodes, contentCallback, headerNodes){

        let csvRowFormatter = function(row){ return '"'+row.join('","')+'"';};
        
        let content = [...nodes].map(contentCallback);

        if(headerNodes){
        	let labels = [...headerNodes].map(cb).map(csvRowFormatter);
        }

        
        let rows = content.map(csvRowFormatter);
        let csv = rows.join("\n");
        
        let blob = new Blob([csv],{type: "text/csv;charset=utf-8;"});
       
       
        return blob;
    }


    return {
        fromNodes: getAsCsv,
        evergreenToCsv: evergreenToCsv,
        iosToCsv: iosToCsv
    };
    
})();

