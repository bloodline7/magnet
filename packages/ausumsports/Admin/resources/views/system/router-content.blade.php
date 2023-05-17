@extends('adminViews::layouts.master')

@section('content')


    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div id="list">

        <table class="table">
            <style>

                table.table td {
                    text-align: center;
                }


                table.table td:first-child {
                    text-align: left;
                }

                table.table td:last-child {
                    text-align: center;
                }

                td:nth-child(2) {
                    text-align: center;
                }

                td:nth-child(5) {
                    text-align: center;
                }

                td {
                    text-shadow: rgba(9, 9, 9, 0.78) 1px 0 3px;
                }

            </style>

            <colgroup>
                <col width="30%">
                <col width="25%">
                <col width="20%">
                <col width="5%">
                <col width="10%">
                <col width="5%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="path">Path</th>

                <th scope="col" data-fn="controller">Controller</th>
                <th scope="col" data-fn="title">Title</th>
                <th scope="col" class="sorter-false" data-fn="use" data-ft="toggle">Use</th>
                <th scope="col" class="sorter-false">Edit</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td>{{ $list->path }}</td>
                    <td>{{ $list->controller }}</td>
                    <td>{{ $list->title }}</td>
                    <td align="center">
                        <div class="form-switch">
                            <input type="checkbox" value="1" class="form-check-input"
                                   @if ($list->content->use)
                                   checked="checked"
                                    @endif
                            >
                        </div>

                    </td>
                    <td align="center">
                      <a href="/{{ config('admin.prefix') }}/system/router-content/{{$list->id}}" target="modal">
                          <button type="button" class="btn btn-outline-info update btn-sm">Edit Content</button>
                      </a>
                    </td>
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
