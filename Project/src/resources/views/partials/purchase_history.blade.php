<div class="purchase_history_container"> 
    <hr>
    <h3 class="font-bold text-md lg:text-xl pb-4">Purchase History</h1>
    <div class="font-normal">
        @foreach($user->purchases as $purchase)
            <p>Purchase id - {{$$purchase->id}}</p>
            <p>Purchase price - {{$purchase->price}}</p>
            <p>Purchase Date - {{$purchase->date}}</p>
            <p>Purchase Status - {{$purchase->pur_status}}</p>
            @foreach($purchase->product_purchase as $variation)
                <p>Purchase id - {{$variation->product->id}}</p>
                <p>Purchase Name - {{$variation->product->name}}</p>
            @endforeach
        @endforeach
    </div>
</div>