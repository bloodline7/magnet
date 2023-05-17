@extends('adminViews::layouts.login')

@section('content')

    <div class="modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Login</h5>

                </div>

                <div class="modal-body">

                    <form action="login" id="form" accept-charset="utf-8" enctype="application/x-www-form-urlencoded"
                          method="post">


                        <div class="form-group">
                            <label class="form-label mt-4">Please Insert id and password</label>
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="form" class="btn btn-primary">Login</button>

                </div>
            </div>
        </div>
    </div>
@endsection
