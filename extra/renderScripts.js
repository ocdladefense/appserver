function renderCards()
{
  return fetchListOfCards().then(function(customer){
    console.log(customer)
    validate(customer);
    getCards(paymentProfiles); 
  });
}




function getCards(paymentProfiles)
{
  var select = document.getElementById('paymentProfileId');
  console.log(paymentProfiles);
  for (var profile in paymentProfiles)
  {
    var cardOption = document.createElement('option');
    cardOption.setAttribute("value",paymentProfiles[profile].customerPaymentProfileId);
    var display = document.createTextNode("Card Number: "+ paymentProfiles[profile].cardNumber+ "  "+"Card Type: "+ paymentProfiles[profile].cardType);
    
    cardOption.appendChild(display);   
  select.appendChild(cardOption);
  }  
  
  
}

if(module && module.exports) //node.js
{
module.exports={renderCards:renderCards, 
                getCards: getCards};
}