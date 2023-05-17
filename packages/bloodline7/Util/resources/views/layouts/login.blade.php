<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('admin.title') }}</title>

    <link rel="stylesheet" href="{{ secure_asset('css/admin.css') }}">
    <script src="https://kit.fontawesome.com/d40d9160a5.js" crossorigin="anonymous"></script>
    <script src="{{ secure_asset('js/admin.js') }}"></script>

</head>
<body>
<div class="container-fluid" role="main">
    <div class="row mt-3">
        <div class="content col-md col-lg pl-lg-0">
            <div id="content">
                @yield('content')
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {

        console.log('Modal Show');

        $('.modal').modal({backdrop: 'static', keyboard: false}).show();


        $('.modal').find("form").unbind().submit(function () {

            var target = $(this).attr('target');

            $.post(target, $(this).serializeArray(), function (Result) {

                //alert(Result.message);

                window.location.href = '/' + "{{config('admin.prefix')}}";

            }).fail(function (Result) {

                $.toast({
                    icon: Result.statusText,
                    text: Result.responseJSON.message,
                    allowToastClose: false,
                    hideAfter: 3000,
                    position: {
                        left: '48%',
                        top: '48%'
                    }
                });
            });

            return false;

        });

    });


</script>
</body>
</html>
