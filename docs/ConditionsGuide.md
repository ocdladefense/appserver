## Conditions as used in DBQuery, QueryBuilder, and FormParser
Last Updated: 10/5/2020

**DBQuery**

There are 4 types of conditions defined in DBQuery:

 - **Condition** Used for WHERE clause 
   - Parameters: `(field, value, op = “=”)` 
   - JSON: `{"type":"condition", "field":field, "op":op, "value":value}`
 - **SortCondition** Used for ORDER BY clause 
   - Parameters: `(field, desc = false)` 
   - JSON: `{"type":"sortCondition", "field":field, "desc":desc}`
 - **LimitCondition** Used for LIMIT clause 
   - Parameters: `(rowCount, offset = 0)` 
   - JSON: `{"type":"limitCondition", "rowCount": rowCount, "offset": offset}`
 - **InsertCondition** Used for INSERT or UPDATE statements 
   - Parameters: `(field, value, rowId)`
   - JSON: `{"type":"insertCondition", "field": field, "value": value, "rowId": rowId}`

**QueryBuilder**

QueryBuilder constructs SQL statements from Conditions.

The function **fromObject()** takes an array of conditions or a single condition and processes them based their type property. Note: If the condition is of type “condition” and its value property is “ALL” the condition is ignored.

The function **compile()** returns a SQL string based on what type property had been set for QueryBuilder.

 - If the type is “delete” conditions of type “condition” are used.
 - If the type is “insert” conditions of type “insertCondition” are
   used.
 - If the type is “update” conditions of type “insertCondition” and
   “condition” are used.
 - If the type isn’t set, QueryBuilder assumes a SELECT statement is
   being requested and conditions of type “condition”, “sortCondition”,
   and “limitCondition” are used.

Extra notes on using conditions to create SQL

 - **Where clause:** Multiple conditions of type “condition” can be used to create a WHERE clause. Each condition will be separated by “AND”
   unless one of the conditions passed in is actually an array of
   conditions in which case that subarray will be wrapped in parentheses
   and each condition inside will be separated by “OR”.
 - **Insert statement:** The columns of an INSERT statement are filled by each unique field property from all conditions of type “insert
   Condition”. The values of an INSERT are filled by value properties
   from the conditions. Values will be arranged by their corresponding
   fields. A single INSERT statement that inserts multiple rows can be
   created by using the rowId property of the conditions. Every
   condition that has a matching rowId will be inserted in the same row.
   The value of the rowId property has no real world meaning or any
   connection to the database. All that matters is that each rowId
   matches for each condition that is meant to insert a value into the
   same row.
 - **Update statement:** Every condition of type “insertCondition” must have a matching value in the rowId property.

**FormParser**

Conditions of all types can be created from the attributes of HTML elements.

FormParser decides what type of condition should be constructed based on which attributes the HTML element has.

For each element to be evaluated, FormParser attempts to create conditions in the following order. If the element doesn't have the correct attributes it moves on to next condition type.

 - **InsertCondition:** The attributes data-field and data-row-id must be
   present and have values.
 - **Condition:** The attributes data-field and value must be present and
   have values.
 - **SortCondition:** The attributes value and data-desc must be present and
   have values.
 - **LimitCondition:** The attribute data-row-count must be present and have
   a value.
