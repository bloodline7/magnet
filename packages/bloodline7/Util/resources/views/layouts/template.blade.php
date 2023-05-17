<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
     {{--   <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">--}}
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('admin.title') }}</title>

        <link rel="stylesheet" href="{{ secure_asset('css/admin.css') }}">
        <script src="https://kit.fontawesome.com/d40d9160a5.js" crossorigin="anonymous"></script>
        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyArTzDd015BV61DFng-eXONFEhIezEoffc"></script>
        <script src="{{ secure_asset('js/admin.js') }}"></script>
        <script>

            $(function () { socketConnect("{{env("SOCKET_HOST")}}") });

            window.prefix = "{{config('admin.prefix')}}";
        </script>
    </head>
    <body>
    <div class="container-fluid" role="main">

        @includeIf("adminViews::layouts.header")


        @include("adminViews::layouts.empty")
    </div>
    <footer>
        @includeIf("adminViews::layouts.footer")
        @includeIf("adminViews::layouts.loading")
    </footer>
    <div id="console" data-user="{{ auth()->user()->id ?? "" }}">
        <div id="console-screen"></div>
        <div id="console-input">
            <input type="text" class="form-control" placeholder="" />
        </div>
    </div>

    <div id="modal"></div>

    </body>
</html>
