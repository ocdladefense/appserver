var modal = {

    show: function(){
        $('body').toggleClass('hasModal');
    },
    hide: function(){
        $('body').removeClass('hasModal');
    },
    render: function(vNode){
        document.getElementById('modal-content').innerHTML = "";
        document.getElementById('modal-content').appendChild(createElement(vNode));
    }
};