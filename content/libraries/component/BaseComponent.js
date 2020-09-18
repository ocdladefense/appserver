'use strict'
// Probably should be depricated
class BaseComponent {
    constructor() {

    }

    createVNode(type, props, children, component) {
        return {
            type: type,
            props: props,
            children: typeof children == "string" ? [children] : children,
            component: component
        };
    }

    createElement(vnode) {
		if(typeof vnode === "string") {
			return document.createTextNode(vnode);
		}
		if(vnode.type == "text") {
			return document.createTextNode(vnode.children);
		}
	
		let $el = document.createElement(vnode.type);
		
		for(let prop in vnode.props) {
            /*
            // if prop is an event listener, wire it to EventFramwork and don't set as attribute
            if(prop.includes('on')) {
                EventFramework.registerEventListener(prop.replace('on', ''), vnode.component);
                continue;
            }*/

			let html5 = "className" == prop ? "class" : prop;
			$el.setAttribute(html5, vnode.props[prop]);
		}
		
		if(vnode.children && vnode.children != [] && vnode.children.length > 0) {
			vnode.children.map(vnode.component.createElement)
				.forEach($el.appendChild.bind($el));
		}
		
		return $el;
    }
}


// const BaseComponent = (function(){

//     let baseComponent = {

//         myNewBaseFunction: function() {
//             alert('Working');
//         }
//     };

//     function BaseComponent() {

//     }

//     BaseComponent.prototype = baseComponent;
    
// })();
