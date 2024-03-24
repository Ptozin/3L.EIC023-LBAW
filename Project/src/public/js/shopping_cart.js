$(document).ready(function(){
    
    isEmpty();

});

function update_quantity(obj) {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let product = {};
    product.id = parseInt($(obj).attr("data-id"));
    product.quantity = parseInt($(obj).val());

    let updateProduct = "shopping_cart";
    
    $.ajax({
        type: "PUT",
        url: updateProduct,
        data: product,
        dataType: 'text',
        success: function (data) {
            
            let final = JSON.parse(data);

            let value = parseFloat(final.Price).toFixed(2);
            $(".total_price").html(value + "€");
        },
        error: function (data) {
            let final = JSON.parse(data.responseText);
            alert("Error: " + final.Message);
            console.log('Error: ', data);
        }
    });
    return false;
}

function increment(obj, quantity_aval) {
    let quantity = $(obj).parent().prev();
    if(quantity.val() < 1 || quantity.val() >= quantity_aval) return;
    quantity.val(parseInt(quantity.val())+1);
    update_quantity(quantity);
    return false;
}

function decrement(obj) {
    let quantity = $(obj).parent().next();
    if(quantity.val() <= 1) return;
    quantity.val(parseInt(quantity.val())-1);
    update_quantity(quantity);
    return false;
}

function isEmpty() {

    let products = document.querySelectorAll(".shopping-cart-item");
    console.log(products);
    if (products[0] == null) {
        document.getElementsByClassName("section-container")[0].innerHTML = "<p style='text-align:center'>You don't have any products in your cart.";
        return true;
    }
    return false;
}

function removeDesignProduct(obj) {


    $(obj).closest(".shopping-cart-item").next().remove();
    $(obj).closest(".shopping-cart-item").remove();
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

    let deleteProduct = "shopping_cart";
    
    $.ajax({
        type: "DELETE",
        url: deleteProduct,
        data: product,
        dataType: 'text',
        success: function (data) {
            let final = JSON.parse(data);
            removeDesignProduct(obj);
            $(".total_price").html(final.Price+"€");
        },
        error: function (data) {
            let final = JSON.parse(data.responseText);
            alert("Error: " + final.Message);
            console.log('Error: ', data);
        }
    });
    return false;
}