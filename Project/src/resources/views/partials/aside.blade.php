<div class="asideProdList bg-light">
    <div class="bg-background-off px-3 my-6 rounded">
        <h3 class=" font-bold text-md lg:text-xl pb-4">Subcategories of {{$category->name}}</h3>
    </div>
    @if(isset($subcategory))
    <p class="px-3 font-normal"><a href="{{ url('/products/category/'.$subcategory->category->id) }}">All</a></p>
    @else
    <p class="px-3 font-normal"><a href="{{ url('/products/category/'.$category->id) }}">All</a></p>
    @endif
    @each('partials.subcategory', $category->subcategories, 'subcategory')
</div>