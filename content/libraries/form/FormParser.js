//'use strict'

const FormParser = (function() {
    let id;
    let overides = {};
    let dontParse = [];
    let resultsLimit = 0;
    let resultsOffset = 0;

    function FormParser() {}

    const setSettings = (settings) => {
        id = settings.formId;

        overides = settings.overides;

        dontParse = settings.dontParse;
    }

    const setResultsLimit = (limit) => {
        resultsLimit = limit;
    }

    const setResultsOffset = (offset) => {
        resultsOffset = offset;
    }

    const elements = (elementId) => {
        return !!elementId ? document.getElementById(elementId) : Array.from(document.getElementById(id).elements);
    };

    const values = (/*elementId*/) => {
        /*if(elementId) {
            return { elementId: { "value":elements(elementId).value, "tagName":element.tagName }};
        }*/
        let allValues = {};
        elements().forEach(element => {  
        allValues[element.id] = { "value": element.value, "tagName":element.tagName };
            for (let i in element.attributes) {
                let att = element.attributes[i];
                let attName = att.name;
                if (attName && attName.startsWith("data-")) 
                    allValues[element.id][attName] = att.value;{
                }
                
            }
        });
        return allValues;
    };
    
    const parseConditions = () => {
        let conditions = [];

        let formData = values();
        for(let formField in formData) {           
            let data = formData[formField];
            
            if (dontParse.includes(formField) || dontParse.includes(document.getElementById(formField).parentNode.id) || 
            (typeof(data.value) != "undefined" && data.value == null) || (typeof(data.value) == "string" && data.value.trim() == '')) {           
                continue;                          
            }
          
            if (overides[formField]) {
                conditions.push(overides[formField](data));
                continue;
            }

            let parserFunctions = [parseInsertCondition, parseWhereCondition, parseOrderByCondition, parseLimitCondition];
            for (let i in parserFunctions) {
                let parserFunction = parserFunctions[i];
                let condition = parserFunction(data);
                if (condition != null) {
                    conditions.push(condition);
                    break;
                }
            }
        }

        if (resultsLimit) {
            conditions.push(DBQuery.createLimitCondition(resultsLimit, resultsOffset));
        }

        return conditions;
    };

    const parseWhereCondition = (data) => {
        if (data["data-field"] && data.value) {
            return DBQuery.createCondition(data["data-field"], data.value, data["data-op"]);
        }
        return null;
    };

    const parseOrderByCondition = (data) => {
        if (data.value && data["data-desc"]) {
            return DBQuery.createSortCondition(data.value, data["data-desc"]);
        }
        return null;
    };

    const parseLimitCondition = (data) => {
        if (data["data-row-count"]) {       
            return DBQuery.createLimitCondition(data["data-row-count"], data["data-offset"]);
        }
        return null;
    };

    const parseInsertCondition = (data) => {
        if (data["data-field"] && data["data-row-id"]) {
            return DBQuery.createInsertCondition(data["data-field"], data.value, data["data-row-id"]);
        }
        return null;
    };

    let proto = {
        setSettings: setSettings,
        parseConditions: parseConditions,
        setResultsLimit: setResultsLimit,
        setResultsOffset: setResultsOffset
    }

    FormParser.prototype = proto;

    return FormParser;
})();