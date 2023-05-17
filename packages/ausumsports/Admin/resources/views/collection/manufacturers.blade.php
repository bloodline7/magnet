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
                <col width="15%">
                <col width="10%">
                <col width="30%">
                <col width="10%">
                <col width="8%">
                <col width="16%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="id">ID</th>
                <th scope="col" data-fn="name">Manufacturer</th>
                <th scope="col" >Logo Image</th>
                <th scope="col" data-fn="logo">Logo Url</th>
                <th scope="col" data-fn="countries">Countries</th>
                <th scope="col">Brands</th>
                <th scope="col">Updated At</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td>{{ $list->name }}</td>
                    <td><img src="{{ $list->logo }}" width="50" height="50"> </td>
                    <td>{{ $list->logo }}</td>
                    <td>{{ $list->countries }}</td>
                    <td>{{ number_format($list->brands->count()) }}</td>
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
