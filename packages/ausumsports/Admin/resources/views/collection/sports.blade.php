@extends('adminViews::layouts.master')

@section('content')


    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div id="list">

        <table class="table tableControl">
            <style>
                table.table td {
                    text-align: center;
                }


            </style>

            <colgroup>
                <col width="5%">
                <col width="25%">
                <col width="25%">
                <col width="20%">
                <col width="20%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="id">ID</th>
                <th scope="col" data-fn="sports_name">Sports Name</th>
                <th scope="col" data-fn="sports_keyword">Sports Keyword</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->sports_name }}</td>
                    <td>{{ $list->sports_keyword }}</td>
                    <td>{{ $list->created_at }}</td>
                    <td>{{ $list->updated_at }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-danger delete btn-sm">Delete</button>
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>


    </div>

@endsection
