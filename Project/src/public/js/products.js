function ajaxSetup() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

function ajaxSuccess(response) {
    $('#product-listing').html(response.products);
    $('.pagination-links').html(response.links);

    if (response.total === 1)
        $('#product-total').html('1 Product');
    else
        $('#product-total').html(response.total + ' Products');

    window.history.pushState('', '', response.url);
}

function addToShoppingCart(obj, id) {

    ajaxSetup();

    let product = {};
    product.id = id;
    product.quantity = document.querySelector(".quantity_val").innerText;

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

            if (obj != null)
                removeDesignProduct(obj);

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

function addToWishlist(id) {

    ajaxSetup();

    let product = {};
    product.id = id;

    let insert_wishlist = "/wishlist";

    $.ajax({
        type: "POST",
        url: insert_wishlist,
        data: product,
        dataType: 'text',
        success: function (data) {
            console.log(data);
            $("#wishlist-error").css('display','none');            
            $("#wishlist-success").css('display','block');
            $("#wishlist-success").text(data);

            alert('The product was added to your wishlist successfully.');
        },
        error: function (data) {
            $("#wishlist-success").css('display','none');            
            $("#wishlist-error").css('display','block');
            $("#wishlist-error").text(data.responseText);
            console.log('Error: ', data);

            alert('An error ocurred while adding the product to your wishlist.\nIt is either already there or out of stock.');
        }
    });
    return false;
}