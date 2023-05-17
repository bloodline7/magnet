@extends('adminViews::layouts.master')

@section('content')

	<div id="modal">
		<div class="modal fade">
			<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
				<div class="modal-content">
					<style>
						.imageBack {
							background-color: rgba(234, 227, 207, 1);
							border-radius: 10px;
							opacity: 0.95;
						}

						.description {
							margin-top: 10px;
							background-color: rgba(250, 235, 215, 0.59);
							border-radius: 10px;
							color: #0a0a0a;
							padding: 15px;
						}
                       /* .images img {
                            max-width: 100px;
                           !* mix-blend-mode: color-burn;*!
                            mix-blend-mode: multiply;
	                        !*mix-blend-mode: multiply;*!
                        }
*/
                        .desc table {
	                        background-color: transparent !important;
                        }
                        .desc table td {
                            vertical-align: top;
                        }

                        .desc h3 {
	                        font-family: inherit !important;
	                        font-size: 15px;
                        }


                        div.product-price:before {
                            content:'$';
                            color: #FFBD00;
                        }

                        .modal-body div.product-price {
                            color: rgb(239, 202, 188);
                            font-size: 15px;
                            text-align: left;
	                        text-shadow: 1px 1px 2px rgba(10, 10, 10, 0.62);
                        }

                        .category ul {

	                        list-style-type: none;

                        }

                        .swiper_viewer {
                            width: 100%;
                            height: 40vw;


	                        border-radius: 10px;


                        }


                        .swiper_viewer .swiper-slide {
                            background-color: #f1ecec;
	                        border-radius: 10px;
                            overflow: hidden;
                        }

                        .swiper_viewer .swiper-slide img {
                            border-radius: 10px;
                        }


                        .swiper-wrapper {
                            width: 100%;
                            height: 100%;
                        }

                        .swiper-slide {
                            text-align: center;
                            font-size: 18px;
                            background: #fff;

                            /* Center slide text vertically */
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: -webkit-flex;
                            display: flex;
                            -webkit-box-pack: center;
                            -ms-flex-pack: center;
                            -webkit-justify-content: center;
                            justify-content: center;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                            -webkit-align-items: center;
                            align-items: center;
                        }

                        .swiper-slide img {
                            display: block;
                            width: 100%;
                            object-fit: cover;
                        }

                        .swiper {

                            margin-left: auto;
                            margin-right: auto;
                        }

                        .swiper-slide {
                            background-size: cover;
                            background-position: center;

                        }

                        .swiper_controller {
                            width: 100%;
                            box-sizing: border-box;
                            padding: 10px 0;
                        }


                        .swiper_controller .swiper-slide {
                            /*  width: 25% !important;*/
                            background-color: transparent;
                            background-color: #ffffff;
                            opacity: 0.4;
                            height: 100%;
                            cursor: pointer;
	                        border-radius: 5px;
                        }

                        .swiper_controller .swiper-slide-thumb-active {
                            opacity: 1;
                        }

                        .swiper-slide img {
                            display: block;
                            width: 100%;
                            object-fit: cover;
                        }


					</style>

					<div class="modal-header">
						<h5 class="modal-title">{{ $data->name }}</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true"></span>
						</button>
					</div>

					<div class="modal-body">
						<div class="row">
							<div class="col-sm-12 col-md-6" style="overflow: hidden; max-height: 800px">

								<div
										style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff; width: 100%; height: 500px"
										class="swiper swiper_viewer imageViewer"
								>
									<div class="swiper-wrapper">
										@foreach($data->images as $image)
											<div class="swiper-slide" >
												<img src="/storage/{{$image->url}}">
											</div>
										@endforeach
									</div>
								</div>

								<div thumbsSlider="" class="swiper swiper_controller mt-2">
									<div class="swiper-wrapper">

										@foreach($data->images as $image)
											<div class="swiper-slide" >
												<img src="/storage/{{$image->url}}">
											</div>
										@endforeach

									</div>
								</div>

							</div>






							{{--	<div class="imageBack">
								<img src="/storage/{{$data->image->url}}" style="max-width: 100%">
								</div>

								<div class="row images">
									@foreach($data->images as $image)
										<div class="col-auto" >
											<img src="/storage/{{$image->url}}">
										</div>
									@endforeach

								</div>--}}



							<div class="col-sm-12 col-md-6">
								<div class="row category">
									<ul>
									@foreach($data->cateArray as $cate)

										<li>
										@foreach($cate as $catedata)
											<a href="/{{ config('admin.prefix') }}/products/list?category={{ $catedata->id }}"> {{ $catedata->name }} </a>
										@endforeach

										</li>

									@endforeach
									</ul>

								</div>
								<div class="row">
									<div class="col-4">Brand</div>
									<div class="col">{{$data->brand->name}}</div>
								</div>
								<div class="row">
									<div class="col-4">Weight</div>
									<div class="col">{{$data->attributes->weight}}</div>
								</div>
								<div class="row">
									<div class="col-4">Price</div>
									<div class="col product-price">{{$data->attributes->price}}</div>
								</div>
								{{--<div class="row">
									<div class="col-4">Spacial</div>
									<div class="col">{{$data->attributes->spacial_price}}</div>
								</div>
--}}
								<div class="row description">
									<div class="col desc">{!! $data->attributes->short_description !!}</div>
								</div>

							</div>
						</div>

						<div class="row description">
							<div class="col desc">{!! $data->attributes->description !!}</div>
						</div>


						<div class="modal-footer">
							<button type="submit" form="form" class="btn btn-primary">Save changes</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>

					</div>
				</div>
			</div>
@endsection
