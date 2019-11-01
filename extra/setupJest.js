global.fetch = require('jest-fetch-mock');

 global.APIRequest = require("./test/api");

const jsdom = require("jsdom");
const { JSDOM } = jsdom;

global.setupDomDocument = function(html){
    var document = new JSDOM(html, {runScripts: "outside-only"}).window.document;

    global.window = document.defaultView;
    global.document = document;
    

    return document;
}
