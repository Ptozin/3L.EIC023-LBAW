<p class=" font-normal">
    <a href="{{ url('/products/category/'.$category->id) }}">{{$category->name}}</a>
    <a href="{{ url('/category/'.$category->id) }}"> |  (click to see what hover on this div would look like)</a>
</p>