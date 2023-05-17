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


    <div id="list">

        <div>
            Total : {{ number_format($data->total()) }}
        </div>

        <table class="table tableControl">
            <style>
                table.table td {
                    text-align: center;
                }

                table.table td:nth-child(7), table.table td:nth-child(8) {
                    text-align: right;
                }


            </style>

            <colgroup>
                <col width="5%">
                <col width="8%">
                <col width="10%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="10%">
                <col width="10%">
                <col width="5%">

            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="id">ID</th>
                <th scope="col">Sports</th>
                <th scope="col">League</th>
                <th scope="col" data-fn="team_name">Team Name</th>
                <th scope="col" data-fn="team_region">Team Region</th>
                <th scope="col" data-fn="team_logo">Team Logo</th>
                <th scope="col">Players</th>
                <th scope="col">CheckList</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->league->sport->sports_name??'' }}</td>
                    <td>{{ $list->league->title }}</td>
                    <td>{{ $list->team_name }}</td>
                    <td>{{ $list->team_region }}</td>
                    <td>{{ $list->team_logo }}</td>
                    <td>{{ number_format($list->rosters->count()) }}</td>
                    <td>{{ number_format($list->checklists->count()) }}</td>
                    <td >
                        <button type="button" class="btn btn-danger delete btn-sm">Delete</button>
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>


        {{ $data->withQueryString()->links() }}

    </div>

@endsection
