@extends('adminViews::layouts.master')

@section('content')


    <div class="card border-secondary mb-3">
        <div class="card-header">Search</div>
        <div class="card-body">
            <form name="searchSite" id="searchSite" method="get" target="#list">
                <fieldset class="searchSite">
                    <legend class="mt-1">Search Options</legend>

                    <div class="col-4">
                    <div class="form-group">
                        <label for="sports">Sports</label>
                        <select class="form-control" id="sports" name="sports">
                            <option value="">All</option>
                            @foreach ($sports as $key => $val)
                            <option value="{{$key}}" {{ (request()->sports == $key) ? 'selected' : '' }}>{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    </div>

                        <div class="input-group mt-2" role="group">
                            <input type="text" class="form-control" name="keyword" placeholder="Search Keyword"
                                    aria-label="search keyword" value="{{request()->keyword}}">
                            <div class="input-group-append">
                                <input type="submit" class="btn bg-secondary" value="Search">
                            </div>
                        </div>
                </fieldset>
            </form>
        </div>
    </div>



    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div id="list">

        <div>
            Total : {{ number_format($data->total()) }}
        </div>

        <table class="table tableControl">
            <style>
                table.table td {
                    text-align: center;
                }


            </style>

            <colgroup>
                <col width="5%">
                <col width="8%">
                <col width="8%">
                <col width="8%">
                <col width="8%">
                <col width="20%">
                <col width="8%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col">Image</th>
                <th scope="col">Sports</th>
                <th scope="col">Brand</th>
                <th scope="col">League</th>
                <th scope="col">Season</th>
                <th scope="col">Product TItle</th>
                <th scope="col" data-fn="year">Year</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td><img src="{{ $list->product_image }}" height="25" /> </td>
                    <td>{{ $list->sport->sports_name }}</td>
                    <td>{{ $list->brand->name }}</td>
                    <td>{{ $list->season->league->title??'' }}</td>
                    <td>{{ $list->season->season_title??'' }}</td>
                    <td>{{ $list->product_title }}</td>
                    <td>{{ $list->year }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-danger delete btn-sm">Delete</button>
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>


        {{ $data->withQueryString()->links() }}

    </div>

@endsection
