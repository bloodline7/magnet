@extends('adminViews::layouts.master')

@section('content')


    @if(env('SERVICE') == "show")
        <div class="jumbotron rounded-1 p-3">

            <h1 class="display-3">Global Collectibles Expo!</h1>
            <p class="lead">Please use Convention Menu for Register List and Search</p>
            <hr class="my-4">
            <p>Global Collectibles Expo
                MARCH 10TH - 13TH, 2022
                Mandalay Bay Resort, Las Vegas</p>
            <p class="lead">
                <a class="btn btn-primary btn-lg" href="/{{config('admin.prefix')}}/convention" role="button">Register List and Search</a>
            </p>
        </div>


    @else
        <div class="jumbotron rounded-1 p-1">

            <h1 class="display-3">Welcome!</h1>
            <p class="lead">Please use Product Menu for Crawling Sports Card Search</p>
            <hr class="my-4">
            <p>Some of function not working yet. Please tell to us whatever you want to do.</p>
            <p class="lead">
                <a class="btn btn-primary btn-lg" href="/{{config('admin.prefix')}}/product/crawling" role="button">Crawling Now</a>
            </p>
        </div>

    @endif

@endsection
