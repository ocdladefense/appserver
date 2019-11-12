function testTheFetch(postUrl, testDataJson )
{
  fetch(postUrl,{
    method: 'POST', // or 'PUT'
    body: testDataJson, // data can be `string` or {object}!
    headers:{
      'Content-Type': 'application/json'
    }
  })
  .then(function(response) {
    return response.json();
  })
  .then(function(myJson) {
    console.log(JSON.stringify(myJson));
  });
}

module.exports = testTheFetch;