const User = (function(){
    
    
    /*
     * 
     * 
        
        
        app.set("region", app.get("region") || 'US - Heritage');
        app.set("regionAccess", customerUser["RegionLeader__r.Name"]);
        app.set("chapterAccess", customerUser["ChapterLeader__r.Name"]);
        app.set("region", customerUser["ChapterLeader__r.Region__r.Name"] || app.get("region"));
        app.set("chapter", app.get("chapterAccess"));
    	app.set("startRegion", app.get("regionAccess") || app.get("region"));
    */
    
    var proto = {
        isCustomerUser: function(){
            return !this.isStaffUser;
        },
        
        isStaffUser: function(){
            return this.isStaffUser;
        },
        
        get: function(attr){
            return this.data[attr];
        },
        
        addPurchasedProduct(videoId) {
        	this.products.push(videoId);
        },
        
        hasPurchasedProduct(videoId) {
        	return this.products.includes(videoId);
        }
    };
    
    
    function User(init){
			this.data = {
				FirstName: "Jane",
				LastName: "Doe"
			};
			
			this.products = [];
    }
    
    
    User.prototype = proto;
    
    
    User.extend = function(p) {
        
        const userProto = p;
        
        var User = function(init){
					this.data = init;  
        };
        
        for(var prop in proto)  {
            userProto[prop] = proto[prop];
        }
        
        User.prototype = userProto;
        
        return User;
    };
    
    
    return User;
})();