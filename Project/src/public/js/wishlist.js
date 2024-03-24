$(document).ready(function(){
    
    isEmpty();

});

function isEmpty() {
    let products = document.querySelectorAll(".cart-item");
    if (products[0] == null) {
        document.getElementsByClassName("section-container")[0].innerHTML = "<p style='text-align:center'>You don't have any products in your wishlist.";
        return true;
    } else if(products.length == 1) {
        $(products[0]).next().remove();
    }
    return false;
}


function removeDesignProduct(obj) {

    $(obj).closest(".cart-item").next().remove();
    $(obj).closest(".cart-item").remove();
    isEmpty();
}

function deleteProduct(obj, id) {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let product = {};
    product.id = id;

    let delete_product = "wishlist";
    
    $.ajax({
        type: "DELETE",
        url: delete_product,
        data: product,
        dataType: 'text',
        success: function (data) {
            console.log(data);
            removeDesignProduct(obj);
        },
        error: function (data) {
            alert("Error: " + data.responseText);
            console.log('Error: ', data);
        }
    });
    return false;
}

function addToShoppingCart(obj, id) {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let product = {};
    product.id = id;
    product.quantity = 1;

    let insertShoppingCart = "/shopping_cart";

    $.ajax({
        type: "POST",
        url: insertShoppingCart,
        data: product,
        dataType: 'text',
        success: function (data) {
            console.log(data);

            $("#shopping-cart-error").css('display','none');            
            $("#shopping_cart-success").css('display','block');
            $("#shopping_cart-success").text(data);

            alert('The product was added to your shopping cart successfully.');

        },
        error: function (data) {
            console.log('Error: ', data);

            $("#shopping_cart-success").css('display','none');            
            $("#shopping-cart-error").css('display','block');
            $("#shopping-cart-error").text(data.responseText);

            alert('An error ocurred while adding the product to your shopping cart.\nIt is either already there or out of stock.');
        }
    });
    return false;
}