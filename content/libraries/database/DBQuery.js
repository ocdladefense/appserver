const DBQuery = (function() {

    function DBQuery() { }

    DBQuery.createCondition = function(field, value, op = "=") { 
        return {"type":"condition", "field":field, "op":op, "value":value};
    };

    DBQuery.createSortCondition = function(field, desc = false) {
        return {"type":"sortCondition", "field":field, "desc":desc};
    };

    DBQuery.createLimitCondition = function(rowCount, offset = 0) {
        return {"type":"limitCondition", "rowCount": rowCount, "offset": offset}
    };

    DBQuery.createInsertCondition = function(field, value, rowId) {
        return {"type":"insertCondition", "field": field, "value": value, "rowId": rowId}
    };

    DBQuery.createTerms = function(value) {
        let punctuationless = value.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()@\+\?><\[\]\+]/g, ' '); // could cause problems with dates
        let extraSpaceRemoved = punctuationless.replace(/ +(?= )/g,'');
        return extraSpaceRemoved.split(' ').filter(Boolean);
    };

    return DBQuery;

})();