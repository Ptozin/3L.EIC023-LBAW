@extends('layouts.app')

@section('title', 'SportsVerse - Add Product')

@section('content')

<section class="nobackground">

    <a class="backlink" href="{!! route('profile') !!}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>
    </a>

    <div class="wrapper">
        <header>Add Product</header>

        <form form method="POST" action="/api/product" enctype="multipart/form-data">
            {{ csrf_field() }}

            <h4>Product Related Fields</h4>
            <br>

            <div class="field name">
                <p >Name:</p>
                <div class="input-area">
                <input id="name" type="text" name="name" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('name'))
                <span class="error">
                    {{ $errors->first('name') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field short_description">
                <p>Short Description:</p>
                <div class="input-area">
                <input id="short_description" type="text" name="short_description" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('short_description'))
                <span class="error">
                    {{ $errors->first('short_description') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field long_description">
                <p>Long Description:</p>
                <div class="input-area">
                <input id="long_description" type="text" name="long_description" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('long_description'))
                <span class="error">
                    {{ $errors->first('long_description') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field manufacturer">
                <p>Manufacturer:</p>
                <div class="input-area">
                <input id="manufacturer" type="text" name="manufacturer" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('manufacturer'))
                <span class="error">
                    {{ $errors->first('manufacturer') }}
                </span>
                @endif
                </div>
            </div>
            <br>

            <div class="field id_sub_category">
                <div class="input-area">
                <label for="id_sub_category">Subcategory ID:</label><br>
                <select id="id_sub_category" name="id_sub_category" required autofocus></select>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('id_sub_category'))
                <span class="error">
                    {{ $errors->first('id_sub_category') }}
                </span>
                @endif
                </div>
            </div>
            <br>

            <h4>Variation Related Fields</h4>
            <br>

            <div class="field stock">
                <p>Stock:</p>
                <div class="input-area">
                <input id="stock" type="numeric" min="0" name="stock" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('stock'))
                <span class="error">
                    {{ $errors->first('stock') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field price">
                <p>Price:</p>
                <div class="input-area">
                <input id="price" type="numeric" step="0.01" min="0" name="price" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('price'))
                <span class="error">
                    {{ $errors->first('price') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field id_color">
                <p>Color ID:</p>
                <div class="input-area">
                <input id="id_color" type="numeric" min="0" name="id_color" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('id_color'))
                <span class="error">
                    {{ $errors->first('id_color') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field id_size">
                <p>Size ID:</p>
                <div class="input-area">
                <input id="id_size" type="numeric" min="0" name="id_size" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('id_size'))
                <span class="error">
                    {{ $errors->first('id_size') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field images"> 
                <p> Images </p>
                <label for="image">Select an image: (jpg, jpeg, png)</label><br>
                <input id="images" type="file" name="images[]" accept="image/*" multiple>
                <br><br>
            </div>

            <p class="font-weight-bold">Note: to add a product atleast a variation of that product needs to be added</p>

            <input id="submitbut" type="submit" value="Submit">

        </form>
       
    </div>
</section>

<script>

    var values = <?php echo json_encode($subcategories); ?>;

    // get the select element
    var select = document.getElementById("id_sub_category");

    // loop through the array of values
    for (var i = 0; i < values.length; i++) {
      // create an option element
      var option = document.createElement("option");

      // set the value of the option to the current value in the array
      option.value = values[i].id;

      // set the text of the option to the current value in the array
      option.text = values[i].name;

      // add the option to the select element
      select.add(option);
    }
  </script>


@endsection