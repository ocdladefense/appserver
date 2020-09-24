var kbd = {


 buffer: [],
 
 handleEvent: function(event){
 	var keyName, modifierKey;
 	
	keyName = event.key, modifierKey = event.ctrlKey || event.metakey;
    
	 if(this.isKeyCombo(keyName, modifierKey)){
			var shortcutEvent = new CustomEvent("ShortcutEvent", {
					detail: {
						originalEvent: event,
						keyName: keyName
					}
			});
		
			event.preventDefault();			
			document.dispatchEvent(shortcutEvent);
			console.log(shortcutEvent.detail);
	}
		
	this.key(keyName);
    //event.preventDefault();  //Prevents any typing what so ever!
    this.execute(event.key, event.ctrlKey || event.metakey);
 },

 key: function(keyName){
    this.buffer.push(keyName);
 },

 isKeyCombo: function(keyName, modifierKey){
     return keyName != "Control" && modifierKey;
 }

}
