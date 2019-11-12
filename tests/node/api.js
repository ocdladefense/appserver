function APIRequest(who) {
    if (who === 'google') {
      return fetch('https://google.com').then(res => res.json())
    } else {
      return 'no argument provided'
    }
  }

//   function MockFetchCall(url,data){
//       if (url === '' && data === '' ){
//       return fetch('').then(res => res.json())
//      } else{
//          return'no arguement provided'
//      }
//   }


  module.exports=APIRequest;

// module.exports=MockFetchCall;