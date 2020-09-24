
function menuItem(obj,routeName){
	if(typeof obj === "string")
	{
		obj = {
			name: obj,
			route: routeName || null
		};
	}
	var name = obj.name;
	var routeName = obj.route;
		if(routeName == null){
			routeName ="";
		}
	var name = vnode("a",{href:"#",id:name,"data-route":routeName},name);
	return vnode("li",{},[name]);
}
	

function subMenu(name,submenu){

	var children = submenu.map(menuItem);
	var sub = vnode("ul",{className:"sub-menu"},children);
	var top = vnode("li",{},[vnode("a",{href:"#"},name)]);
	top.children.push(sub);
	return top;
}

function createMenu() {

	var children = [
		subMenu("file",[{name:"save"},{name:"save-as"},{name:"export"},{name:"add document"},{name:"add webpage"},{name:"logout"}]),
		
		// menuItem("salesforce"),

		subMenu("materials", [{name:"show all",route:"database"},{name:"Registered events"}]),
		subMenu("case reviews", [{name:"Recent"},{name:"2019 Case Reviews"},{name:"2018 Case Reviews"}]),
		subMenu("Books Online",[{name:"Defending Sex Cases"}]),
		subMenu("Notes",[{name:"show all",route:"database"},{name:"New..",route:"new-note"}]),
		/*subMenu("SiteStatus",[
			{name:"show-all", route:"all-site-statuses"},
			{name:"checksite...",route:"site-status-check-site"},
			{name:"Load Sites", route:"site-status-load-sites"}
		]),
		*/
		menuItem("about","about")					
	];
	
	return vnode("ul",{className:"main-menu"},children);
}