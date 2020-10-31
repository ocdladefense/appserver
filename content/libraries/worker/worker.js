

var wkr = {

	init: function(){
		onmessage = function(e){
			console.log(e);
		};
	},
	
	status: function(){
		console.log("I am the Worker!");
		console.log(this);
	}
};

wkr.init();



