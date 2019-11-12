var expect = require('chai').expect;
var jest = require('jest');
global.fetch = require('jest-fetch-mock');
var math = require('../math');
var datascripts = require('../public/dataScripts');

// var testTheFetch = require('../functionsToTest');

describe('addTwoNumbers()', function () {
  it('should add two numbers', function () {
    
    // 1. ARRANGE
    var x = 5;
    var y = 1;
    var sum1 = x + y;

    // 2. ACT
    var sum2 = math.add(x, y);

    // 3. ASSERT
    expect(sum2).to.be.equal(sum1);

  });
});

describe('multiplyTwoNumbers()', function () {
  it('should multiply two numbers', function () {
    
    // 1. ARRANGE
    var x = 5;
    var y = 1;
    var sum1 = x * y;

    // 2. ACT
    var sum2 = math.multiply(x, y);

    // 3. ASSERT
    expect(sum2).to.be.equal(sum1);

  });
});

//datascripts.fetchListOfCards();






