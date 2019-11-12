// const APIRequest = require("./api");
const jsdom = require("jsdom");
const { JSDOM } = jsdom;
const cardData = require("../public/dataScripts");
const renderData = require("../public/renderScripts");

describe('testing api', () => {
  beforeEach(() => {
    fetch.resetMocks()
  })
 
  it('calls google and returns data to me', () => {
    fetch.mockResponseOnce(JSON.stringify({ data: '12345' }))
 
    //assert on the response
    APIRequest('google').then(res => {
      expect(res.data).toEqual('12345')
    })
 
    //assert on the times called and arguments given to fetch
    expect(fetch.mock.calls.length).toEqual(1)
    expect(fetch.mock.calls[0][0]).toEqual('https://google.com')
  })
})

describe('testing fetchListOfCards', () => {
  beforeEach(() => {
    fetch.resetMocks()
  })
 
  it('calls fetchListOfCards and returns a payment profile to me', () => {
    fetch.mockResponseOnce(JSON.stringify({ paymentProfiles:[{cardNumber:'1234678392', cardType:'Visa', customerPaymentProfileId:'1111111111'}] }))
    cardData.fetchListOfCards().then(res=>{
      expect(res.paymentProfiles[0].cardNumber).toEqual('1234678392')
      expect(res.paymentProfiles[0].cardType).toEqual('Visa')
      expect(res.paymentProfiles[0].customerPaymentProfileId).toEqual('1111111111')
    })
  })
})

describe('testing succesful response for postFormData', () => {
  beforeEach(() => {
    fetch.resetMocks()
  })

  it('calls postFormData and returns a succesfull response', () => {
    fetch.mockResponseOnce(JSON.stringify({orderResponse:[{charge: "succes", ccNumber: "1111111111", orderNumber: "123445677655", amount: "47.50"}]}))
    cardData.postFormData().then(res =>{
      expect(res.orderResponse[0].charge).toEqual("succes")

    })
  })
})

describe('testing failing response for postFormData', () => {
  beforeEach(() => {
    fetch.resetMocks()
  })

  it('calls postFormData and returns a failed response', () => {
    //need to figure out what exactly is being sent back on a failed response
    fetch.mockResponseOnce(JSON.stringify({orderResponse:[{amount: "50.00", chargeStatusResponseCode: "3", error: "Charge Credit Card ERROR :  Invalid response 3", orderNumber: null}]}))
    cardData.postFormData().then(res =>{
      expect(res.orderResponse[0].chargeStatusResponseCode).toEqual("3")

    })
  })
})
 


describe('testing getCards', () => {
  beforeEach(() => {
    fetch.resetMocks()
  })
  it('calls getCards and checks to see if the correct amount of option elements are created', () => {
    const { JSDOM } = require("jsdom");
    var paymentProfiles = {paymentProfileId:"124567768", cardNumber:'1234678392', cardType:'Visa', customerPaymentProfileId:'1111111111'};
    JSON.stringify(paymentProfiles);

    var document = setupDomDocument(`<!DOCTYPE html><html><body><form><select id="paymentProfileId"></select></form></body></html>`);
    var sel = document.querySelector("select");
    
    if(sel === null)
    {
      console.log("select is null");
    }
    console.log(sel);
    renderData.getCards(paymentProfiles);
    


  //   const dom = new JSDOM(`<body>
  //   <script>document.body.appendChild(document.createElement("option"));
  //           document.body.
  //                </script>
  // </body>`, { runScripts: "dangerously" });

  // var cardOption = (dom.window.document.querySelector("option"));

  // cardOption.setAttribute("value",paymentProfiles[profile].customerPaymentProfileId);




    // renderData.getCards(paymentProfiles);
    // expect(cardOption.length.toEqual("1"));
  })
})


 