@extends('adminViews::layouts.master')

@section('content')


{{--data-sortlist="[[0,0],[1,0]]" --}}

        <div class="row mb-3">
            <div class="col-12">
                    <a href="/{{config('admin.prefix')}}/register" target="modal"><button type="button" class="btn btn-secondary float-end">Register Admin Account</button></a>
            </div>
        </div>


        <table class="table" >
            <colgroup>
                <col width="30%">
                <col width="15%">
                <col width="25%">
                <col width="20%">
                <col width="5%" >
                <col width="5%" >
            </colgroup>
            <thead>
            <tr>
                <th scope="col" >Email</th>
                <th scope="col" >Name</th>
                <th scope="col" >Created At</th>
                <th scope="col" >Updated At</th>
                <th scope="col" class="sorter-false" >Update</th>
                <th scope="col" class="sorter-false" >Delete</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($data as $list)

            <tr data-pk="{{ $list->id }}">
                <td>{{ $list->email }}</td>
                <td align="center">{{ $list->name }}</td>
                <td align="center">{{ $list->created_at }}</td>
                <td align="center">{{ $list->updated_at }}</td>
                <td align="center"><a href="/{{config('admin.prefix')}}/system/adminList/{{ $list->id }}" target="modal"><button type="button" class="btn btn-primary update btn-sm">Update</button></a></td>
                <td align="center"><button type="button" class="btn btn-danger delete btn-sm">Delete</button></td>
            </tr>

            @endforeach

            </tbody>
        </table>


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
