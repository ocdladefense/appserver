
function openNav() {
  
    document.getElementById("sidenav").classList.add("sidenav-mobile-open");
    menu.innerHTML = "&times;";
    menu.style.fontSize = "xx-large" ;
    document.getElementById("menu").onclick = closeNav;  
    
  }
  
  function closeNav() {
    document.getElementById("sidenav").classList.remove("sidenav-mobile-open");
    menu.innerHTML = "&#9776;";
    menu.style.fontSize = "20pt" ;
    document.getElementById("menu").onclick = openNav; 
    
  }

  window.addEventListener("resize",() =>{
    if(document.body.clientWidth > 800){
      document.getElementById("sidenav").classList.remove("sidenav-mobile-open");
      menu.innerHTML = "&#9776;";
      document.getElementById("menu").onclick = openNav; 
    }
    // if(document.body.clientWidth <= 800){

    // }
  });

  function addMobileMenuBtn(elementName,isCssClass){

    isCssClass = isCssClass != true ? false : true;
    elementName = elementName == null || elementName == "" ? "mobile-menu-btn": elementName;

    let parent;
    let mobileMenuBtn = document.createElement('span');
    mobileMenuBtn.id = "menu";
    mobileMenuBtn.innerHTML = "&#9776;";
    mobileMenuBtn.onclick = function() {openNav('55%');};


    if(isCssClass){
      parent = document.getElementsByClassName(elementName)[0];
    }
    else if (parent == null){

      parent = document.getElementById(elementName);

    }else if(parent == null){
      parent = document.createElement('div');
    }

    parent.appendChild(mobileMenuBtn);
    parent.style.display = "inline-block";
      
  }






  window.onload = function(){
    addMobileMenuBtn();

    var dropdown = document.getElementsByClassName("dropdown-btn");
    var i;
    
    for (i = 0; i < dropdown.length; i++) {
      dropdown[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var dropdownContent = this.nextElementSibling;
      if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
      } else {
        dropdownContent.style.display = "block";
      }
      });
    };
  }