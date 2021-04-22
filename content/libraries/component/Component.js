const Component = (function(){
    
    function nodeLocator(containers){
        if(!containers || containers.length ==0) {
            console.log("DOM_ERROR: No valid elements to match event.");
            return (function(node){return null;});
        }
        
        return (function(node) {
            // console.log("Checking ",node);
            for(var i = 0; i<containers.length; i++){
                if(containers[i] == node || containers[i].contains(node)) {
                    console.log("Found container!");
                    return containers[i];
                }
            }
            
            return null;
        });
    }
    
    function renderAt(target, elems, append) {
    
        elems = Array.isArray(elems) ? elems : [elems];
        // Assume that target is a Node if it isn't a DOM_STRING_SELECTOR.
        let t = typeof target === "string" ? document.querySelector(target) : target;
    
        if(!append){
        	t.innerHTML = "";
        }
        
        elems.forEach(function(elem) {
            t.appendChild(Render.createElement(elem));
        });
    }
    
    
    
    
    function render(def,target,remotingArgs, args) {
        let REMOTING_WITH_NS = 3;
        let REMOTING_NO_NS = 2;
        
        let comp = def.apply(null, (Array.isArray(args) ? args : [args]));
        
        let NS_NULL = null; // Represents a Visualforce Remoting context with no namespace.
        let forceRemotingContext = comp.data.length == REMOTING_WITH_NS ? ForceRemoting.invokeAction(comp.data.shift()) : ForceRemoting.invokeAction(NS_NULL);
    
        let callout = forceRemotingContext(comp.data[0],comp.data[1],remotingArgs);
        
    
        var fn = function(data) { 
            let vnodes = [];
            
            if(comp.preRender) {
                vnodes.push(comp.preRender(data));
            }
            
            for(var item in data){
                vnodes.push(comp.render(item,data));
            }
            
            if(comp.button) {
                vnodes.push(Render.vNode("button", comp.button , comp.button.value));
            }
    
            var container = Render.vNode("div", {id: comp.id}, vnodes);
            var label = Render.vNode("h2", {className: "title"}, comp.label);
            renderAt(target,[label,container]);
        };
        
        return callout.then(fn);
    }
        
    
    function appendAll(elements, selector) {
        target = document.querySelector(selector) || document.body;
        elements.forEach(element => {
            target.appendChild(element);
        });
    }
            
    function toggleFeature(className, show) {
        show = show || false;
        
        let bclasses = document.body.getAttribute("class");
        let existing = bclasses == null ? [] : bclasses.split(" ");
        
        if(existing.includes(className)){
        
            existing = existing.filter(function(item) { return item != className;});
            
            
        } else if(!show){
            existing.push(className);
        }
        if(show){
            existing.push(className);
        }
    
        document.body.setAttribute("class",existing.join(" "));
    }  
                   

    
        
    return {
        render: render,
        renderAt: renderAt,
        nodeLocator:  nodeLocator,
        appendAll: appendAll,
        toggleFeature: toggleFeature
    };
    
})();
                 
                 
   





let SiteModal = (function(){
    
    let modals = {};
    
    function addModal(name,modal){
     	modals[name] = modal;
    }
    
    function getModal(name){
        return modals[name];
    }
     
    
    
     function SiteModal(){
         
        
    }
    
    
    
    SiteModal.fromSelector = function(sel){

        let root = document.querySelector(sel);
        
        if(!root) throw new Error("MODAL_DOM_ERROR: Selector "+sel+" isn't a valid node.");
        
		let modal = modals[sel] || new SiteModal();
        modal.root = root;
        modals[sel] = modal;
        return modal;
    };
 
     
     SiteModal.prototype = {
         
         addLoadingIcon: function() {
             
             let loadingIcon = document.createElement('div');
             loadingIcon.setAttribute('id', 'loading-icon');
             this.root.appendChild(loadingIcon);
             
             let icon = document.getElementById('loading-icon');
             icon.classList.add('loading');
         },
         
         
         
         removeLoadingIcon: function() {
             let loadingIcon = document.getElementById('loading-icon');
             this.root.removeChild(loadingIcon);
         }
                                 
    };   


    SiteModal.prototype.show = function(){
      	// Component.toggleFeature("has-modal");
      	Component.toggleFeature("show-modal");
    };
    
    SiteModal.prototype.hide = function(){
	    document.body.classList.remove("show-modal");
    };
                   
                   
    
    SiteModal.prototype.loadAsync = function(thenable) {
        this.addLoadingIcon();
    
        return thenable.then(() => {
			this.removeLoadingIcon();           
        });
    
    };   
            
    SiteModal.close = function(){
        for(var m in modals){
            modals[m].hide();
        }
    };


	return SiteModal;
})();


                 

     
 const Picker = (function(){
                     
                     
                     
     function Picker(rootSelector, itemSelector){
		this.callbacks = [];
		this.rootSelector = rootSelector;
        this.itemSelector = itemSelector;            
        this.root = null;
		this.items = null;
            
        if(!!rootSelector){
			this.root = this.getRoot(this.rootSelector);
            this.items = this.root.querySelectorAll(this.itemSelector);
            this.getTarget = Component.nodeLocator(this.items);
        }
    }
      
            
            
    Picker.defer = function(root,items){
        let picker = new Picker();
        picker.rootSelector = root;
        picker.itemSelector = items;
        return picker;
    };

     
    Picker.prototype.respondTo = function(props){
		this.targetProperty = props["datasetVarName"];
    };
    
    Picker.prototype.getRoot = function(selector){
          
    	let root = document.querySelector(selector);
    	if(null == root) throw new Error("DOM_ERROR: No elements match "+selector);
        return root;   
    };  
            
    Picker.prototype.refresh = function(){
		this.root = this.getRoot(this.rootSelector);
            
        this.items = this.root.querySelectorAll(this.itemSelector);
        this.getTarget = Component.nodeLocator(this.items);
    };
            
            
	Picker.prototype.click = function(fn){
		this.callbacks.push(fn);        
		document.addEventListener("click", this);
	};
                             
                             
     Picker.prototype.handleEvent = function(e){

        if(!this.root) {
            console.log("DOM_WARNING: Root of this picker is not set.");
            return false;
        }
            
        console.log("Root is: ",this.root);
        if(!this.root.contains(e.target)) return false;

            
        var target = this.getTarget(e.target);
        
              
        if(!target || !target.dataset || !target.dataset[this.targetProperty]) return false;
         
        let value = target.dataset[this.targetProperty];
		
            
        this.callbacks.forEach((cb) => {
            cb(value);
        });
                             
		this.render(target);
     };
    
            
       
    Picker.prototype.select = function(value){
		let target = null;
            
        this.items.forEach((item) => {
			if(!item || !item.dataset || !item.dataset[this.targetProperty]) return;
            var attr = item.dataset[this.targetProperty];
           	if(attr == value) target = item;
        });  
            
		if(!!target) this.render(target);
    };              
                        
            
     Picker.prototype.render = function(selected){
     
         // Remove any previously selected item styling
         this.items.forEach(child => {
             child.classList.remove('item-selected');
        });
         
        selected.classList.add('item-selected');                 
    };


	return Picker;
 })();