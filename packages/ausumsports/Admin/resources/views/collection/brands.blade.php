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
                <col width="8%">
                <col width="10%">
                <col width="20%">
                <col width="12%">
                <col width="12%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="id">ID</th>
                <th scope="col" >Logo Image</th>

                <th scope="col" data-fn="manufacturer_id" data-ft="selectBox" data-fget-url="/admin/collection/manufacturers_code">Manufacturers</th>
                <th scope="col" data-fn="name">Brand</th>
                <th scope="col">Products</th>
                <th scope="col">Updated At</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>

            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->id }}</td>
                    <td><img src="{{ $list->manufacturer->logo }}" width="50" height="50"> </td>
                    <td>{{ $list->manufacturer->id }}</td>
                    <td>{{ $list->name }}</td>
                    <td style="text-align: right">{{ number_format($list->products->count()) }}</td>
                    <td>{{ $list->updated_at }}</td>
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
