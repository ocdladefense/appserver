var url ="../header-test";

function fetchTheHeaderInfo(){
    fetch(url,{headers:{'Accept':'application/json; charset=utf-8'}}).then(function(response){
        return response.text();
    }).then(function(body){
        console.log(body);
    })
}