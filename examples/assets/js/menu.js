
function menuItem(name){
	var name = v("a",{href:"#"},name);
	return v("li",{},[name]);
}

function subMenu(name,submenu){

	var children = submenu.map(menuItem);
	var sub = v("ul",{className:"sub-menu"},children);
	var top = v("li",{},[v("a",{href:"#"},name)]);
	top.children.push(sub);
	return top;
}

function createMenu() {

	var children = [
		subMenu("file",["save","save-as","export"]),
		menuItem("salesforce"),
		menuItem("about")
	];
	
	return v("ul",{className:"main-menu"},children);
}