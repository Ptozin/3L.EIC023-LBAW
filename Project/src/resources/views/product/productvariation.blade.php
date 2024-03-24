<form>
    {{ csrf_field() }}
    @foreach($product->product_variations as $variation)
        <form method="GET" action="{{route('product', $user->id)}}">
            <p>{{$variation->id}} </p>
            <p>{{$variation->stock}} </p> 
            <p>{{$variation->price}}€</p>
        </form>
    @endforeach
</form>