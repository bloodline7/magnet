@extends('adminViews::layouts.master')

@section('content')


    <div class="card border-primary mb-3">
        <div class="card-header">Product Import</div>
        <div class="card-body">
            {{-- <h4 class="card-title">System getConfig</h4>--}}
            <p class="card-text">

            <form action="/{{config('admin.prefix')}}/convention/importProduct" id="form" accept-charset="utf-8"
                  enctype="application/x-www-form-urlencoded"
                  method="post">

                <div class="row">

                    <div class="form-group col">
                        <label class="form-label ">Convention Exhibitor</label>

                        <div class="form-floating mb-3">
                            <select name="conventionExhibitor" class="form-select" id="conventionExhibitor">
                                @foreach( $data as $list )
                                    <option value="{{ $list->id }}">{{ $list->company }}</option>

                                @endforeach
                            </select>
                            <label for="conventionExhibitor">selected Exhibitors</label>
                        </div>


                        <div class="form-floating mb-3">
                            <input type="text" name="image_server_url" value="https://cdn.ausumsports.com/pub/media/catalog/product/" class="form-control" id="image_server_url">
                            <label for="image_server_url">Image Server Url</label>
                        </div>

                        {{--

												<div class="form-floating mb-3">
													<input type="text" name="sub_title" value="{{getConfig('sub_title')}}" class="form-control" id="sub_title">
													<label for="sub_title">Sub title</label>
												</div>
						--}}

                        <div class=" mb-3">
                            <label for="file">Upload CSV file</label><br>
                            <input type="file" name="file" id="file">

                        </div>


                    </div>


                    <div class="row">
                        <div class="col">

                        </div>


                        <div class="col">

                        </div>


                    </div>

                    <div class="row">
                        <div class="col">
                            <button type="submit" form="form" class="btn btn-primary">Import</button>
                        </div>
                    </div>


                </div>

            </form>


            </p>
        </div>
    </div>


@endsection
