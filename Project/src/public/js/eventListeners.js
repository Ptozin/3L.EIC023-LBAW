let selectBtns = document.querySelectorAll('select');

for (btn of selectBtns) {
  btn.addEventListener('click', function() {
    const status = this.value;
    const id = this.getAttribute('purchaseid');
    const user_id = this.getAttribute('userid');
    
    let url = "/user/" + user_id;

    //document.querySelector('.msg').innerHTML = url;

    return updateUserPurchase(id, url, status);
  });
}


var expandBtns = $("button.expandProds");

for(idx=0; idx < expandBtns.length; idx++){
    expandBtns.eq(idx).click(function() {    
    if($(this).parent().parent().siblings().eq(1).is(":visible")){
      $(this).parent().parent().siblings().hide(600);
      $(this).parent().parent().siblings().eq(0).show(200);
      $(this).html("Show more");
    } else {
      $(this).parent().parent().siblings().show(600);
      $(this).html("Show less");
    }
  });
}
  

