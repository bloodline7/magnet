@extends('adminViews::layouts.master')

@section('content')


    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div id="list">

        <table class="table tableControl tableSorter" data-sortlist="[[1,0][2,0],]">
            <style>
                table.table td {
                    text-align: center;
                }
            </style>

            <colgroup>
                <col width="5%">
                <col width="8%">
                <col width="20%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="5%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" class="sorter-false" data-fn="id[]" data-ft="checkBox">ID</th>
                <th scope="col" data-fn="sports_id" data-ft="selectBox" data-fget-url="/admin/collection/sports_code">Sports</th>
                <th scope="col" data-fn="title">League Title</th>
                <th scope="col" data-fn="code">Code</th>
                <th scope="col" data-fn="region">Region</th>
                <th scope="col" class="sorter-false" data-fn="logo">Logo</th>
                <th scope="col" data-fn="popularity">Popularity</th>
                <th scope="col" class="sorter-false" data-fn="default" data-ft="toggle">Default</th>
                <th scope="col" class="" >Seasons</th>
                <th scope="col" class="" >Teams</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->sports_id }}</td>
                    <td>{{ $list->title }}</td>
                    <td>{{ $list->code }}</td>
                    <td>{{ $list->region }}</td>
                    <td>{{ $list->logo }}</td>
                    <td>{{ $list->popularity }}</td>
                    <td>{{ $list->default }}</td>
                    <td>{{ $list->seasons->count() }}</td>
                    <td>{{ $list->teams->count() }}</td>
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
