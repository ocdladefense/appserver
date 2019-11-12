var modalPrototype = {
    modalSetup:function()
    {    
      this.modalContainer = document.createElement("div");
      this.modalContainer.setAttribute("class", "modal fade");
      this.modalContainer.setAttribute("id", "myModal");
      this.modalContainer.setAttribute("tabindex", "-1");
      this.modalContainer.setAttribute("role", "dialog");
      this.modalContainer.setAttribute("aria-labelledby", "myModalLabel");
      this.modalContainer.setAttribute("aria-hidden", "false");
    },
    
    attachModal:function()
    {   
      var body = document.body;
      body.appendChild(this.modalContainer);
    },
    
    showModal:function()
    {
      $('#myModal').modal({ show: false});
    
      $('#myModal').modal('show');     
    },

    hideModal:function()
    {
        $('#myModal').modal('hide');
    },

    loading:function()
    {
      $('body').addClass('loading');

    },

    stopLoading:function()
    {
      $('body').removeClass('loading');
    },
    
    content:function (html)
    {    
      this.modalContainer.innerHTML=html;
    },
    
    changeContent:function(html)
    {
        var modal_body= document.getElementsByClassName("modal-body")[0];
        modal_body.innerHTML = html;
    },

    

    fetchHtml:function(url)
    {
      return fetch(url)
      .then(function(response){
        return response.text();
      });
    }
};

function modal()
{
    this.modalContainer= null;
    this.modalSetup();
}
modal.prototype = modalPrototype;



