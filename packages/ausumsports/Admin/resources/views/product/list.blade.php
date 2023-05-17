@extends('adminViews::layouts.master')

@section('content')
<style>
    div.product {
	    margin-bottom: 10px;
    }

    img {
        mix-blend-mode: multiply;
	    width: 100%;
	    height: auto;
    }

    div.imgbox {
        background-color: rgba(241, 241, 239, 0.98);
	    margin: 5px;
	    border-radius: 5px;
        box-shadow: 10px 10px 15px rgba(0, 0, 0, 0.65);
        display: flex;
        align-items: center;
    }

    div.product-name a {
	    color: #a4d0f3;
	    font-size: 12px;
	    text-decoration: solid;
    }

    div.product-price:before {
	    content:'$';
	    color: #FFBD00;
    }
    div.product-price {
        color: rgb(248, 78, 4);
        font-size: 15px;
	    text-align: center;
    }

    .square:before{
        content: "";
        display: block;
        padding-top: 100%; 	/* initial ratio of 1:1*/
    }

</style>

<div class="row mb-3">
	{{--<div class="col-12">
			<a href="/admin/register" target="modal"><button type="button" class="btn btn-secondary float-end">Register Admin Account</button></a>
	</div>
	--}}

	<form name="searchSite" id="searchSite" method="get"
			action="/{{config('admin.prefix')}}/products/list/search"
			target="#list">

		<fieldset class="searchOption">

			<legend class="mt-2">Search Options</legend>

			<div class="row mt-2">

				<div class="col col-sm-12 col-md-6">
					<div class="row mx-2 pt-2">

						<div class="form-check col">
							<input class="form-check-input" checked name="search[]" value="name" type="checkbox"
									id="searchName">
							<label class="form-check-label" for="searchName">Name</label>
						</div>

						<div class="form-check col">
							<input class="form-check-input" checked name="search[]" value="brand" type="checkbox"
									id="searchBrand">
							<label class="form-check-label" for="searchBrand">Brand</label>
						</div>

						<div class="form-check col">
							<input class="form-check-input" checked name="search[]" value="category" type="checkbox"
									id="searchCategory">
							<label class="form-check-label" for="searchCategory">Category</label>
						</div>

						<div class="form-check col">
							<input class="form-check-input" name="search[]" value="description" type="checkbox"
									id="searchDescription">
							<label class="form-check-label" for="searchDescription">Description (Slow)</label>
						</div>
					</div>
				</div>

				<div class="col col-sm-12 col-md-6 align-content-right">
					<div class="form-group">
						<div class="row ">
							<div class="form-floating col">
								<input type="text" name="startDate" class="form-control date-picker"
										id="startDate" placeholder="YYYY-MM-DD">
								<label for="startDate">Search From</label>
							</div>

							<div class="form-floating col">
								<input type="text" name="endDate" class="form-control date-picker"
										id="endDate" placeholder="YYYY-MM-DD">
								<label for="endDate">Search To</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="input-group mt-2" role="group">
				<input type="text" class="form-control" name="keyword" placeholder="Search Keyword"
						aria-label="search keyword">
				<div class="input-group-append">
					<input type="submit" class="btn bg-secondary" value="Search">
				</div>
			</div>

		</fieldset>
	</form>

</div>

<div id="list">
	<div class="row">

		@foreach($data as $item)

			<div class="col-xs-12 col-sm-6  col-md-4 col-lg-3  product">
				<div class="row imgbox square">
					<div class="col">
						<a href="/{{config('admin.prefix')}}/products/view/{{$item->id}}" target="modal">
					<img src="/storage/{{ $item->image->url ?? '' }}">
						</a>
					</div>
				</div>

				<div class="row">
					<div class="product-name">
						<a href="/{{config('admin.prefix')}}/products/view/{{$item->id}}" target="modal">
							{{ $item->name }}
						</a>
					</div>

					<div class="product-price">
						{{ number_format($item->attributes->price, 2) }}
					</div>

					<div class="row">
						<div class="col-md-6">
							<button class="btn btn-sm add-to-cart bg-info"><i class="fas fa-cart-plus"></i></button>
						</div>
						<div class="col-md-6">
							<button class="btn btn-sm add-to-wishlist bg-primary"><i class="fas fa-grin-tongue-wink"></i></button>
						</div>
					</div>

				</div>
			</div>
		@endforeach
	</div>

	<div class="row">
		{{ $data->withQueryString()->links() }}
	</div>
</div>

@endsection
