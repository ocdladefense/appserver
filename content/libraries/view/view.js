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



function vNode(name,attributes,children){
		if(null == children || typeof children == "undefined") {
			children = [];
		} else if(typeof children == "string" ) {
			children = [children];
		} else {
			children = Array.isArray(children) ? children : [children];
		}
		
    var vnode =  {    
			type: name,
			props: attributes,
			children: children
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
	
		var $el = document.createElement(vnode.type);
		

		for(var prop in vnode.props) {
			var html5 = "className" == prop ? "class" : prop;
			$el.setAttribute(html5,vnode.props[prop]);
		}
		
		if(null != vnode.children) {
			vnode.children.map(createElement)
				.forEach($el.appendChild.bind($el));
		}
		
		return $el;
	};
	
	

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