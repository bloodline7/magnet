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


            </style>

            <colgroup>
                <col width="3%">
                <col width="5%">
                <col width="15%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="5%">
                <col width="5%">
                <col width="10%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="id">ID</th>
                <th scope="col">Sports</th>
                <th scope="col" data-fn="full_name">Full Name</th>
                <th scope="col" data-fn="first_name">First Name</th>
                <th scope="col" data-fn="last_name">Last Name</th>
                <th scope="col" data-fn="birth_date">Birth Date</th>
                <th scope="col" data-fn="birth_place">Birth Place</th>
                <th scope="col" data-fn="familiarity">Familiarity</th>
                <th scope="col" data-fn="retirements" data-ft="toggle">Retirements</th>
                <th scope="col" data-fn="affiliation_college">Affiliation College</th>
                <th scope="col" >Team</th>
                <th scope="col" >Checklist</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->sport->sports_name }}</td>
                    <td>{{ $list->full_name }}</td>
                    <td>{{ $list->first_name }}</td>
                    <td>{{ $list->last_name }}</td>

                    <td>{{ $list->birth_date}}</td>
                    <td>{{ $list->birth_place}}</td>

                    <td>{{ $list->familiarity}}</td>
                    <td>{{ $list->retirements }}</td>
                    <td>{{ $list->affiliation_college}}</td>
                    <td>{{ $list->rosters->count() ?? ''}}</td>
                    <td>{{ number_format($list->checklists->count()??'')}}</td>
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
