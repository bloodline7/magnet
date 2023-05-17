@extends('adminViews::layouts.master')

@section('content')

    <div id="modal">
        <div class="modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Code Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>


                    <div class="modal-body">

                        <table class="table tableControl" data-path="/{{config('admin.prefix')}}/system/codeManager/Group">
                            <colgroup>
                                <col width="35%">
                                <col width="15%">
                                <col width="40%">
                                <col width="10%" align="center">
                            </colgroup>
                            <thead>
                            <tr>

                                <th scope="col" data-fn="title" >Title</th>
                                <th scope="col" >Codes</th>
                                <th scope="col" >Updated At</th>
                                <th scope="col" class="sorter-false" >Delete</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($data as $list)

                                <tr data-pk="{{ $list->id }}">
                                    <td align="center">{{ $list->title }}</td>
                                    <td align="center">{{ $list->code->count() }}</td>
                                    <td align="center">{{ $list->updated_at }}</td>
                                    <td align="center"><button type="button" class="btn btn-danger delete btn-sm">Delete</button></td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>

                    </div>

                   {{-- <div class="modal-footer">
                        <button type="submit" form="form" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
