@extends('adminViews::layouts.master')

@section('content')


    {{--data-sortlist="[[0,0],[1,0]]" --}}

    <div class="row mb-1">

        <div class="col-lg-3 col-md-6">
            <select class="form-select links">
                <option value="/{{config('admin.prefix')}}/system/codeManager/list">All Group</option>
                @foreach ($group as $key => $item)
                    <option value="/{{config('admin.prefix')}}/system/codeManager/list/{{$key}}">{{$item}}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <a href="/{{config('admin.prefix')}}/system/codeManager/Group" target="modal">
                <button type="button" class="btn btn-secondary float-end">Code Group Manager</button>
            </a>
        </div>

    </div>

    <div id="list">

        <table class="table tableControl" data-path="/{{config('admin.prefix')}}/system/codeManager/list">
            <style>
                table.table td:first-child {
                    color: yellow;
                    text-align: center;
                }
                table.table td:last-child {
                    text-align: center;
                }

                /*th:nth-child(2) {
                    color: yellow;
                }*/
            </style>

            <colgroup>
                <col width="20%">
                <col width="40%">
                <col width="10%">
                <col width="30%">
                <col width="1%">
            </colgroup>
            <thead>
            <tr>
                <th scope="col" data-fn="group_id" data-ft="selectBox"
                    data-fget-url="/{{config('admin.prefix')}}/system/codeManager/GroupList">Group
                </th>
                <th scope="col" data-fn="title">Title</th>
                <th scope="col" data-fn="sort_no" class="sort">Sort</th>
                <th scope="col">Updated At</th>
                <th scope="col" class="sorter-false">Delete</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($data as $list)

                <tr data-pk="{{ $list->id }}">
                    <td align="center">{{ $list->group_id }}</td>
                    <td>{{ $list->title }}</td>
                    <td align="center">{{ $list->sort_no }}</td>
                    <td align="center">{{ $list->updated_at }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-danger delete btn-sm">Delete</button>
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>

    </div>
@endsection
