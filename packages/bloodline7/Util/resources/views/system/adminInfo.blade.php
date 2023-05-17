@extends('adminViews::layouts.master')

@section('content')

    <div id="modal">
        <div class="modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Admin Info</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>

                    </div>


                    <div class="modal-body">

                        <form action="/{{config('admin.prefix')}}/system/adminList/{{$data->id}}" id="form" accept-charset="utf-8"
                              enctype="application/x-www-form-urlencoded"
                              method="post">

                            <div class="form-group">
                                <label class="form-label mt-4">Please Insert Administrator Information</label>
                                <div class="form-floating mb-3">
                                    <input type="email" name="email" class="form-control" id="floatingInput"
                                           placeholder="name@example.com" value="{{$data->email}}">
                                    <label for="floatingInput">Email address</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" name="name" class="form-control" id="floatingName"
                                           placeholder="Admin full Name" value="{{$data->name}}">
                                    <label for="floatingName">Admin full name</label>
                                </div>


                                <div class="form-floating">
                                    <input type="password" name="password" class="form-control" id="floatingPassword"
                                           placeholder="Password">
                                    <label for="floatingPassword">Password</label>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" form="form" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
