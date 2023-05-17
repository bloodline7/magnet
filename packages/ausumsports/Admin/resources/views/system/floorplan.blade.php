@extends('adminViews::layouts.master')

@section('content')

    <style>
        img {
            max-width: 100%;
        }

    </style>

    <div class="card border-primary mb-3">
        <div class="card-header">Floor Plan Configure</div>
        <div class="card-body">
            {{-- <h4 class="card-title">System getConfig</h4>--}}
            <p class="card-text">

            <form action="/{{config('admin.prefix')}}/system/floorplan" id="form" accept-charset="utf-8"
                  enctype="application/x-www-form-urlencoded"
                  method="post">

		        <div class="row">

			        <div class="form-group col">
				        <label class="form-label ">Floor Plan Updated On</label>
				        <div class="form-floating mb-3">
					        <input type="text" name="plan_updated_on" class="form-control" value="{{getConfig('plan_updated_on')}}" id="plan_updated_on">
					        <label for="plan_updated_on">ex: mm/dd/yy ( null for today )  </label>
				        </div>
			        </div>
		        </div>

                <div class="row">

                    <div class="form-group col">
                        <label class="form-label ">Floor Plan Image update</label>

                        <div class="form-floating mb-3 dropzone" data-upload-url="/{{config('admin.prefix')}}/system/imageUpload" data-message="Floor Plan Image Drop">
                            <input type="hidden" name="image" value="{{ env("AWS_S3_URL").getConfig('floorPlan_image') }}">
                        </div>
                        <hr>
                    </div>


                </div>


    @if(getConfig('floorPlan_image_back'))

	<div class="row">
		<div class="form-group col">
			<label class="form-label ">Restore floorplan Image</label>

            	<img src="{{ env("AWS_S3_URL").getConfig('floorPlan_image_back') }}">

			<hr>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" name="restore" value="1"  type="checkbox" id="restore">
                <label for="restore">Restore Image by backup file</label>
            </div>

        </div>
	</div>

    @endif







	<div class="row">
		<div class="col">

		</div>


		<div class="col">

		</div>


		</div>

	<div class="row">
		<div class="col">
			<button type="submit" form="form" class="btn btn-primary">Save changes</button>
		</div>
	</div>
</form>


</p>
</div>
</div>


@endsection
