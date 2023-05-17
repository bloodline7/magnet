@extends('adminViews::layouts.master')

@section('content')


    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div id="list">

        <table class="table tableControl">
            <style>
                table.table td:first-child {
                }
                table.table td:last-child {
                    text-align: center;
                }

                td:nth-child(2) {
                    text-align: center;
                }

                th:nth-child(5):after {
                    content: "/Lock";
                }

                td:nth-child(5) {
                    text-align: center;
                }

                td:nth-child(2) {
                    text-shadow: rgba(9, 9, 9, 0.78) 1px 0 3px;
                }


                td:nth-child(2)[data-value=GET] {
                    color: #8280f3;
                }


                td:nth-child(2)[data-value=POST] {
                    color: #32ff3b;
                }

                td:nth-child(2)[data-value=PUT] {
                    color: #366aff;
                }

                td:nth-child(2)[data-value=DELETE] {
                    color: #ff3c7b;
                }


            </style>

            <colgroup>
                <col width="30%">
                <col width="15%">
                <col width="25%">
                <col width="20%">
                <col width="5%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>

                <th scope="col" data-fn="path">Path</th>
                <th scope="col" data-fn="method" data-ft="selectBox"
                    data-fget='{ "GET" : "GET" ,"POST":"POST" ,"PUT" : "PUT", "DELETE" : "DELETE"}'>Method
                </th>
                <th scope="col" data-fn="controller">Controller</th>
                <th scope="col" data-fn="title">Title</th>
                <th scope="col" class="sorter-false" data-fn="use" data-ft="toggle">Use</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->path }}</td>
                    <td align="center">{{ $list->method }}</td>
                    <td>{{ $list->controller }}</td>
                    <td>{{ $list->title }}</td>
                    <td align="center">{{ $list->use }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-danger delete btn-sm">Delete</button>
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>


        {{ $data->withQueryString()->links() }}

    </div>

    {{--

            <div>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">&laquo;</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">5</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
    --}}

@endsection
