function updateAdminInfo(flag) {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let url = "/profile";

    let infoList = {};
    infoList.flag = flag;
    
    $.ajax({
        type: "GET",
        url: url,
        data: infoList,
        dataType: 'text',
        success: function (data) {
            //let final = JSON.parse(data);
            if(flag == "users" && document.getElementById("adminUsersList").hidden == true){
                document.getElementById("adminUsersList").hidden = false;
                document.getElementById("adminReviewsList").hidden = true;
                document.getElementById("adminPurchasesList").hidden = true;
                document.getElementById("adminHistUsersBtn").style.backgroundColor="#ddd";
                document.getElementById("adminHistUsersBtn").style.textShadow="1px 1px 1px #ababab";
                document.getElementById("adminHistRevBtn").style.backgroundColor="rgb(240, 240, 240)";
                document.getElementById("adminHistPurchBtn").style.backgroundColor="rgb(240, 240, 240)";
            }
            else if(flag == "reviews" && document.getElementById("adminReviewsList").hidden == true){
                document.getElementById("adminReviewsList").hidden = false;
                document.getElementById("adminUsersList").hidden = true;
                document.getElementById("adminPurchasesList").hidden = true;
                document.getElementById("adminHistUsersBtn").style.backgroundColor="rgb(240, 240, 240)";
                document.getElementById("adminHistRevBtn").style.backgroundColor="#ddd";
                document.getElementById("adminHistRevBtn").style.textShadow="1px 1px 1px #ababab";
                document.getElementById("adminHistPurchBtn").style.backgroundColor="rgb(240, 240, 240)";
            }
            else if(flag == "purchases" && document.getElementById("adminPurchasesList").hidden == true){
                document.getElementById("adminPurchasesList").hidden = false;
                document.getElementById("adminUsersList").hidden = true;
                document.getElementById("adminReviewsList").hidden = true;
                document.getElementById("adminHistUsersBtn").style.backgroundColor="rgb(240, 240, 240)";
                document.getElementById("adminHistRevBtn").style.backgroundColor="rgb(240, 240, 240)";
                document.getElementById("adminHistPurchBtn").style.backgroundColor="#ddd";
                document.getElementById("adminHistPurchBtn").style.textShadow="1px 1px 1px #ababab";
            }

        },
        error: function (data) {
            let final = JSON.parse(data.responseText);
            alert("Error: " + final.Message);
            console.log('Error: ', data);
        }
    });
    return false;
}

function updateUserInfo(flag, id) {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let url = "/profile";

    let infoList = {};
    infoList.flag = flag;
    infoList.id = id;
    
    $.ajax({
        type: "GET",
        url: url,
        data: infoList,
        dataType: 'text',
        success: function (data) {
            if(flag == 'userPurchases' && document.getElementById("purchasesList").hidden == true){
                document.getElementById("purchasesList").hidden = false;
                document.getElementById("reviewsList").hidden = true;
                document.getElementById("histPurchBtn").style.backgroundColor="#ddd";
                document.getElementById("histPurchBtn").style.textShadow="1px 1px 1px #ababab";
                document.getElementById("histRevBtn").style.backgroundColor="rgb(240, 240, 240)";
            }
            else if(flag == 'userReviews' && document.getElementById("reviewsList").hidden == true){
                document.getElementById("reviewsList").hidden = false;
                document.getElementById("purchasesList").hidden = true;
                document.getElementById("histRevBtn").style.backgroundColor="#ddd";
                document.getElementById("histRevBtn").style.textShadow="1px 1px 1px #ababab";
                document.getElementById("histPurchBtn").style.backgroundColor="rgb(240, 240, 240)";
            }
            //probably this is to delete later
            else if(flag == 'userProductsPurchase' && document.getElementById("productsPurchaseList").hidden == false){
                document.getElementById("productsPurchaseList").hidden = true;
            }
            else if(flag == 'userProductsPurchase' && document.getElementById("productsPurchaseList").hidden == true){
                document.getElementById("productsPurchaseList").hidden = false;
            }

        },
        error: function (data) {
            let final = JSON.parse(data.responseText);
            alert("Error: " + final.Message);
            console.log('Error: ', data);
        }
    });
    return false;
}

//no needed anymore
function adminOrderUpdate(){
    const status = document.getElementById("changeStatus").value;
    const id = document.getElementById("changeStatus").getAttribute('purchaseid');
    const user_id = document.getElementById("changeStatus").getAttribute('userid');

    let url = "/user/" + user_id;

    return updateUserPurchase(id, url, status);
}

//tive de tirar algumas cenas para debug que aco que estava a crashar o resto
function updateUserPurchase(id, url, status) {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let purchase = {};
    purchase.id = id;
    purchase.status = status;
    
    $.ajax({
        type: "PUT",
        url: url,
        data: purchase,
        dataType: 'text',

    });
    return false;
}

function cancelOrder() {
    if(confirm("Are you sure you want to cancel your order?\nThis action can't be undone.") == true){
        const status = "Canceled";
        const id = document.getElementById("cancelOrderButton").getAttribute('purchaseid');
        
        url = "/profile";

        updateUserPurchase(id, url, status);

        const statusOutput = document.getElementById("currentStatus");
        statusOutput.innerHTML = "Status - Canceled";
        const cancelButton = document.getElementById("cancelOrderContainer");
        cancelButton.remove();
    }

    return false;
}

/*
function clearDiv(div) {
    while (div.firstChild) {
        div.removeChild(div.lastChild);
    }
    return true;
}
*/

/*
function showUsers(userList) {
    let usersdiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(usersdiv);
    for(let i = 0; i < userList.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        a.href = "/user/" + userList[i].id;
        a.innerHTML = userList[i].name + " id: " + userList[i].id;
        const p = document.createElement("p");
        p.appendChild(a);
        usersdiv.appendChild(p);
    }
    return false;
}
*/

/*
function showReviews(reviewList) {
    let reviewsDiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(reviewsDiv);
    for(let i = 0; i < reviewList.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        a.href = "/product/" + reviewList[i].id_product;
        a.innerHTML = "id user : " + reviewList[i].user_id + " id product: " + reviewList[i].id_product + " date: " + reviewList[i].date + " rating : " + reviewList[i].rating;
        const p = document.createElement("p");
        p.appendChild(a);
        reviewsDiv.appendChild(p);
    }
    return false;
}
*/

/*
function showPurchases(purchaseList) {
    console.log(purchaseList);
    let purchasesDiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(purchasesDiv);
    for(let i = 0; i < purchaseList.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        a.href = "/user/" + purchaseList[i].user_id;
        a.innerHTML = "id : " + purchaseList[i].id + " id user : " + purchaseList[i].user_id + " date : " + purchaseList[i].date + " status : " + purchaseList[i].pur_status;
        const p = document.createElement("p");
        p.appendChild(a);
        purchasesDiv.appendChild(p);
    }
    return false;
}
*/

/*
function showUserPurchases(purchaseList) {
    console.log(purchaseList);
    let purchasesDiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(purchasesDiv);
    for(let i = 0; i < purchaseList.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        //a.href = "/product/" + purchaseList[i].user_id;
        a.innerHTML = "id : " + purchaseList[i].id + " id user: " + purchaseList[i].user_id + " date : " + purchaseList[i].date + " status: " + purchaseList[i].pur_status;
        const p = document.createElement("p");
        p.appendChild(a);
        purchasesDiv.appendChild(p);
    }
    return false;
}
*/

/*
function showUserReviews(reviewList) {
    console.log(reviewList);
    let reviewsDiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(reviewsDiv);
    for(let i = 0; i < reviewList.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        a.href = "/product/" + reviewList[i].id_product;
        a.innerHTML = " id product: " + reviewList[i].id_product + " date: " + reviewList[i].date + " rating: " + reviewList[i].rating;
        const p = document.createElement("p");
        p.appendChild(a);
        reviewsDiv.appendChild(p);
    }
    return false;
}
*/

/*
function showWishlist(wishlist) {
    console.log(wishlist);
    let wishlistDiv = document.querySelector("#userinformation #showInformationProfile .info");
    clearDiv(wishlistDiv);
    for(let i = 0; i < wishlist.length; i++){
        const a = document.createElement("a");
        a.classList.add("fw-bolder","listingAdminProfile");
        a.href = "/product/" + wishlist[i].pivot.product_variation_id;
        a.innerHTML = "prod var id : " + wishlist[i].pivot.product_variation_id + " price : " + wishlist[i].price + "€";
        const p = document.createElement("p");
        p.appendChild(a);
        wishlistDiv.appendChild(p);
    }
    return false;
}
*/