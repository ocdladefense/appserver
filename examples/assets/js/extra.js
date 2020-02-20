// document.addEventListener('DOMContentLoaded',function(e){ doMenu(menu); },false);

var mockCustomer = {

	FirstName: 'Jose',
	
	LastName: 'Bernal',
	
	Email: 'jbernal.web.dev@gmail.com',
	
	EmailRender: function(){
		return 'email.render@gmail.com';
	},

	render:function(prop){
		var rMethod = prop+'Render';
		return this[rMethod] ? this[rMethod]() : this[prop];
	},
	
};



function showModal(){
	// do a fetch of the template
	
	// convert the template to a string using the XML.serialize() method
	var form = fetch('../templates/ccForm.html');
	form.then(response => response.text())
	.then((text) => {
		var xml = (new window.DOMParser()).parseFromString(text, "text/html"); //"text/xml"
		console.log(xml);
		document.getElementById('modal-content').innerHTML = parse(text,mockCustomer);
	})
	.then(()=>{
	
		document.body.setAttribute('class','hasModal');
	
		document.getElementById('cc-purchase-form').addEventListener('submit',function(e){return false;},false);
		document.getElementById('cancel').addEventListener('click',function(e){return cancel();},false);
		document.getElementById('purchase').addEventListener('click',function(e){return purchase();},false);
	});
}






function doXml(menu){
	var domstring,
	
	xml;
	
	domstring = "<html><body><div id='menu'>"+renderMenu(menu)+"</div></body></html>";
	
	// console.log(domstring);
	
	xml = (new window.DOMParser()).parseFromString(domstring, "text/html");
	
	document.getElementById('header').appendChild(xml.body.firstChild);//documentElement.body);
	return xml;
}



function purchase(){
	alert('OK, will charge your card now.');
		document.getElementById('modal-content').innerHTML = "<h3>Here's the document you requested:</h3><p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from &quote;de Finibus Bonorum et Malorum&quote; by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p>";			
}

function cancel(){
	document.body.setAttribute('class','');
		document.getElementById('modal-content').innerHTML = "You don't have access to this resource.";
}