@extends('adminViews::layouts.master')

@section('content')


	<div class="card border-primary mb-3">
		<div class="card-header">General Configure</div>
		<div class="card-body">
			{{-- <h4 class="card-title">System getConfig</h4>--}}
			<p class="card-text">

			<form action="/{{config('admin.prefix')}}/system/setting" id="form" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" method="post">

				<div class="row">

					<div class="form-group col">
						<label class="form-label ">Front Site configure</label>

						<div class="form-floating mb-3">
							<input type="text" name="domain" class="form-control" id="domain" value="{{getConfig('domain')}}">
							<label for="domain">Site URL</label>
						</div>

						<div class="form-floating mb-3">
							<input type="text" name="title" value="{{getConfig('title')}}" class="form-control" id="title">
							<label for="title">Title</label>
						</div>
						{{--

												<div class="form-floating mb-3">
													<input type="text" name="sub_title" value="{{getConfig('sub_title')}}" class="form-control" id="sub_title">
													<label for="sub_title">Sub title</label>
												</div>
						--}}

						<div class="form-floating mb-3">
							<select name="timezone" class="form-select" id="timezone">
								{!! code('timezone', getConfig('timezone')) !!}
							</select>

							<label for="timezone">timezone</label>
						</div>

						{{--<div class="form-floating mb-3">
							<input type="text" name="middleware" value="{{getConfig('middleware')}}" class="form-control" id="middleware">
							<label for="middleware">middleware List</label>
						</div>
						--}}

						<hr>

						<label class="form-label ">Reset Password configure</label>

						<div class="form-floating mb-3">
							<input type="text" name="resetmail_subject" value="{{getConfig('resetmail_subject')}}" class="form-control" id="resetmail_subject"> <label for="resetmail_subject">password Reset mail Subject</label>
						</div>


						<div class="form-floating mb-3">
							<input type="email" name="resetmail_from" value="{{getConfig('resetmail_from')}}" class="form-control" id="resetmail_from">
							<label for="resetmail_from">password Reset mail Sender Email</label>
						</div>

						<div class="form-floating mb-3">
							<select name="token_expired" class="form-select" id="token_expired">
								{!! code('hour' , getConfig('token_expired')) !!}
							</select> <label for="token_expired">token expired hours</label>
						</div>

					</div>


					<div class="form-group col">
						<label class="form-label">Administrator Information</label>

						<div class="form-floating mb-3">
							<input type="text" name="admin_prefix" class="form-control" id="admin_prefix"> <label for="admin_prefix">Admin Path prefix</label>
						</div>


						<div class="form-floating mb-3">
							<input type="text" name="admin_title" class="form-control" id="admin_title"> <label for="admin_title">Admin title</label>
						</div>

						<div class="form-floating mb-3">
							<input type="text" name="admin_sub_title" class="form-control" id="admin_sub_title"> <label for="admin_sub_title">Admin sub title</label>
						</div>


						<div class="form-floating mb-3">
							<select name="admin_timezone" class="form-select" id="admin_timezone">
								{!! code('timezone' , getConfig('admin_timezone')) !!}
							</select>

							<label for="admin_timezone">Admin timezone</label>
						</div>

						<div class="form-check form-switch mb-3">
							<input type="hidden" name="admin_console" value="0"> <input class="form-check-input" name="admin_console" value="1" {{ (getConfig('admin_console')) ? ' checked' : '' }}  type="checkbox" id="admin_console"> <label for="admin_console">Admin Console</label>
						</div>

						<hr>

						<div class="form-group col">
							<label class="form-label ">Convention configure</label>

							<div class="form-floating mb-3">
								<select name="default_convention" class="form-select" id="d_convention">
									{!! code('convention' , getConfig('default_convention')) !!}
								</select> <label for="d_convention">Default Convention</label>
							</div>


							<div class="form-check form-switch mb-3">
								<input type="hidden" name="all_stadium" value="0"> <input class="form-check-input" name="all_stadium" value="1" {{ (getConfig('all_stadium')) ? ' checked' : '' }}  type="checkbox" id="all_stadium"> <label for="all_stadium">Show Allegiant Stadium</label>
							</div>


						</div>

					</div>
				</div>


				<div class="row">
					<div class="col">
						<div class="">
							<input type="text" class="form-control input-lg colorPicker" value="#000000"/>
						</div>
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
