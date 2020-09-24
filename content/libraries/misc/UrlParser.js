const UrlParser = (function() {
    
    function UrlParser(url){
        let parts = url.split("://");
    
        this.protocol = parts[0];
    
        var otherParts = parts[1].split("/");
    
        this.domain = otherParts.splice(0,1);
    
        this.pathParts = otherParts;
        
        this.path = otherParts.join("/");
    }
    
    
    var proto = {
        protocol: "http",
        
        domain: null,
        
        path: null,
        
        queryString: null,
    
        getLastPathPart: function(){
            return this.pathParts[this.pathParts.length-1];
        }
    };
    
    
    UrlParser.prototype = proto;
    
    
    return UrlParser;
})();
