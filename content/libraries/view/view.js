// define(["libFetch"],function(xhrFetch){

function elem(elementName, attributes, text)
{
    var element = document.createElement(elementName);
    
    if(text != null)
    {
        element.appendChild(document.createTextNode(text));
    }

    for(var prop in attributes)
    {
        var propName = prop == "className" ? "class" : prop;
        
        element.setAttribute(propName, attributes[prop]);
    }

    return element;
}




function linkContainer(link){
    var tableCellLink = vNode("a", {href:link},"View/download material");
    var tableCellLinkContainer = vNode("div",{className:"Rtable-cell link"},[tableCellLink]);

    return tableCellLinkContainer;
}


/*TODO
figure out what to do when our text value is undefined. 
For example, when JSX evaluates a variable and its value is undefined and its value is used as a text node.

*/
function vNode(name,attributes,...children){
		let joined = [];
		if(children.length == 0 || null == children[0] || typeof children[0] == "undefined") {
			joined = [];
		} else if(children.length == 1 && typeof children[0] == "string") {
			joined = children;
		} else {
			//children = Array.isArray(children) ? children : [children];
			//console.log(children);
			//flatten method?
			for(var i = 0; i<children.length; i++) {
				if(Array.isArray(children[i])) {
					joined = joined.concat(children[i]);
				} else {
					joined.push(children[i]);
				}
			}
		}
		  
    var vnode =  {    
			type: name,
			props: attributes,
			children: joined
    };
    
    return vnode;
}

vnode = vNode;



	DEBUG = false;
	
	var log = function(m){
		DEBUG && console.log(m);
	};
	
	var documents = {
		
	};
	
	var cache = {
		'order-card': null
	};
	
	var css = function(css){
		for(var prop in css){
			this.styles[prop] = css[prop];
		}
		this.root.setAttribute("style",getAsInlineStyles(this.styles));
	};
	
	var getAsInlineStyles = function(css){
		var styles = [];
		for(var prop in css){
			styles.push([prop,css[prop]].join(":"));
		}
		return styles.join(";")+";";
	};

	function register(documentId,url){
		cache[documentId] = url;
	}

		
	
	function loadDocument(uri){
		return xhrFetch.fetch(uri);
		// return fetch(uri).then(response => response.text());
	}
	
	
	function loadXml(uri){
		return loadDocument(uri);
		/*return fetch(uri)
		.then( response=>response.text())
		.then( (textHtml)=> {
			const parser = new DOMParser();
			const htmlDocument = parser.parseFromString(textHtml, "text/html");
			return htmlDocument;
		});
		*/
	}
	



	window.createElement = function createElement(vnode){
		if(typeof vnode === "string") {
			return document.createTextNode(vnode);
		}
		if(vnode.type == "text") {
			return document.createTextNode(vnode.children);
		}
		if(typeof vnode.type == "function") {
			let temp = vnode.type(vnode.props);
			return createElement(temp);
		}
	
		var $el = document.createElement(vnode.type);
		

		for(var prop in vnode.props) {
			var html5 = "className" == prop ? "class" : prop;
			if (vnode.props[prop] === null) {
				continue;
			}
			$el.setAttribute(html5,vnode.props[prop]);
		}
		
		if(null != vnode.children) {
			vnode.children.map(createElement)
				.forEach($el.appendChild.bind($el));
		}
		
		return $el;
	};

	/**
	 * Method for virtual nodes
	 *
	 * @see-also https://medium.com/@deathmood/how-to-write-your-own-virtual-dom-ee74acc13060
	 */

	window.render = function render($container, newNode) {
		let $containerClone = $container.cloneNode(false);
		let $parent = $container.parentNode;

		$newNode = createElement(newNode);
		$containerClone.appendChild($newNode);

		$parent.replaceChild($containerClone, $container);
	}
	
	  
	  window.updateElement = function updateElement($parent, newNode, oldNode, index = 0) {
		if (!oldNode) {
		  $parent.appendChild(createElement(newNode));
		} else if (!newNode) {
			if (!$parent.childNodes[index]) {
				$parent.removeChild($parent.childNodes[$parent.childNodes.length-1]);
			} else {
				$parent.removeChild($parent.childNodes[index]);
			}
		} else if (changed(newNode, oldNode)) {
		  $parent.replaceChild(
			createElement(newNode),
			$parent.childNodes[index]
		  );
		} else if (newNode.type) {
		  const newLength = newNode.children.length;
		  const oldLength = oldNode.children.length;
		  for (let i = 0; i < newLength || i < oldLength; i++) {
			
			updateElement(
			  $parent.childNodes[index],
			  newNode.children[i],
			  oldNode.children[i],
			  i
			);
		  }
		} 
	  }

	  window.changed = function changed(node1, node2) {
		return typeof node1 !== typeof node2 ||
			   typeof node1 === 'string' && node1 !== node2 ||
			   node1.type !== node2.type ||
			   propsChanged(node1, node2);
	  }
	
	  window.propsChanged = function propsChanged(node1, node2) {
			let node1Props = node1.props;
			let node2Props = node2.props;

			if (typeof node1Props != typeof node2Props) {
				return true;
			}

			if (!node1Props && !node2Props) {
				return false;
			}

			let aProps = Object.getOwnPropertyNames(node1Props);
			let bProps = Object.getOwnPropertyNames(node2Props);
		
			
			if (aProps.length != bProps.length) {
				return true;
			}
		
			for (let i = 0; i < aProps.length; i++) {
				let propName = aProps[i];
		
				if (node1Props[propName] !== node2Props[propName]) {
					return true;
				}
			}
			return false;
		}
	
	

	/**
	 * Method to parse any XML-like string
	 *
	 * @see-also https://stackoverflow.com/questions/14340894/create-xml-in-javascript/34047092
	 */
	function parseComponent(tpl) {
		var container = "<html><head></head><body><div>"+tpl+"</div></body></html>";
		var parser = new DOMParser();
		var doc = parser.parseFromString(container,"text/html");
		// body.innerHTML = container;
		var body = doc.body;
		var first = body.firstChild;
		
		return first;
	}
	
	window.parseComponent = parseComponent;
	
	function tree(args){
		var strings = [];
		var root = arguments[0];
		var calc = [];
		for(var i = 1; i < arguments.length; i++){
			var currentBranch = i === 1 ? root : arguments[i-1];
			var arg = arguments[i];
			if("function" == typeof arg) {
				calc.push(arg[i](args[args.length-1]));
			} else if(arg instanceof Array) {
				currentBranch.children = arg;
			} else {
				currentBranch.children.push(arg);
			}
			
		}
		
		return root;
	}
	
	function DomTree(){
		var root = tree.apply(null,arguments);
		
		return createElement(root);
	}
	
	window.tree = tree;
	window.DomTree = DomTree;
	
	var nodeList = function(nodeName,items,cb) {
		var list = [];
		items.forEach( (item) => {
			var node, args, props;
			args = [nodeName];
			props = cb(item);
			for(var i = 0; i<props.length; i++){
				args.push(props[i]);
			}
			args.push(item.textContent);
			node = tag.apply(null,args);
			list.push(node);
		});
		
		return list;
	};
	
	window.nodeList = nodeList;
	
	function componentProps(){
		var props = {};
		for(var i = 0; i< arguments.length; i++){
			var arg = arguments[i];
			var prop = String.prototype.split.call(arg,"="); // key value pairs
			if(prop.length > 1) {
				props[prop[0]] = prop[1];
			} else {
				props[prop[0]] = null; // properties like selected, disabled, etc.
			}
		}
		
		return props;
	}
	

	
	function tag(){
		var nodeName, props, content;
		nodeName = Array.prototype.splice.call(arguments,0,1)[0];
		if(arguments.length > 1) {
			content = Array.prototype.splice.call(arguments,arguments.length-1)[0];
		}

		props = componentProps.apply(null,arguments);
		return vNode(nodeName,props,content);
	}
	
	window.tag = tag;
	

	
	var div = function() {
		Array.prototype.unshift.call(arguments,"div");
		return tag.apply(null,arguments);
	};
	
	var span = function(props) {
		Array.prototype.unshift.call(arguments,"span");
		return tag.apply(null,arguments);
	};
	
	var ul = function() {
		Array.prototype.unshift.call(arguments,"ul");
		return tag.apply(null,arguments);
	};
	
	var li = function(props) {
		Array.prototype.unshift.call(arguments,"li");
		return tag.apply(null,arguments);
	};

	window.div = div;	
	window.span = span;
	window.ul = ul;
	window.li = li;
	


	
	function convert(elem){
		/*const Node.ELEMENT_NODE;
		const Node.TEXT_NODE;
		const Node.DOCUMENT_NODE;
		const Node.DOCUMENT_TYPE_NODE;
		const Node.DOCUMENT_FRAGMENT_NODE;
		const Node.COMMENT_NODE;
		const Node.CDATA_SECTION_NODE;*/
		var vNode = {
			type: elem.nodeName.toLowerCase(),
			props: props(elem.attributes)
		};
		
		if(Node.TEXT_NODE == elem.nodeType) {
			return vNode;
		}
		
		else {
			vNode['children'] = [];
		}
		
		
		if(elem.childNodes && elem.childNodes.length > 0) {
			for(var i = 0; i < elem.childNodes.length; i++){
				vNode.children.push(convert(elem.childNodes.item(i)));
			}
		}
		
		return vNode;
	}
	
	function props(props){
		var p = {};
		for(var i = 0; i<props.length; i++){
			var attr = props.item(i);
			p["class" == attr.nodeName ? "className" : attr.nodeName] = attr.nodeValue;
			// console.log(props.item(i));
		}
		
		return p;
	}
	
	
	function loadTemplate(uri){
		return loadXml(uri)
		.then( function(doc) {
			return doc.body.innerHTML;
		})
	}
	

/*	
	return {
		register: register,
		parse: parse,
		loadDocument: loadDocument,
		loadXml: loadXml,
		loadTemplate: loadTemplate,
		getAsInlineStyles: getAsInlineStyles,
		css: css,
		createElement: createElement,
		parseComponent: parseComponent,
		tree: tree,
		DomTree: DomTree,
		tag: tag,
		div: div,
		span: span,
		ul: ul,
		li: li
	};
		
});

*/