/*

modal.renderHtml("<h1>Hello World!</h1>");
modal.show();

*/


var modal = {

    show: function(){
        $('body').addClass("hasModal");
				
				setTimeout(() => $("#modal").addClass("fullscreen"), 100);
    },
    
    
    hide: function() {
    
				$("#modal").removeClass("fullscreen")

        
        
				setTimeout(() => $('body').removeClass('hasModal'), 100);
    },
    
    
    render: function(vNode){
        document.getElementById('modal-content').innerHTML = "";
        document.getElementById('modal-content').appendChild(createElement(vNode));
    },

    
    renderHtml: function(html) {
        document.getElementById('modal-content').innerHTML = html;
    },
    
    
    html: function(html) {
			this.renderHtml(html);
    }
};


