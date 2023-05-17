@extends('adminViews::layouts.master')

@section('content')

    <div class="modal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{$data->title}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>

                <div class="modal-body">

                    <form action="/{{config('admin.prefix')}}/system/router-content/{{$data->id}}" id="form" accept-charset="utf-8" enctype="application/x-www-form-urlencoded"
                          method="post">

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="use" value="0">
                            <input class="form-check-input" name="use" value="1" {{ ($data->content->use) ? ' checked' : '' }}  type="checkbox" id="content_use">
                            <label for="content_use">Use Content</label>
                        </div>

                        <textarea id="ckeditor" name="content">{!! $data->content->content !!}</textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="form" class="btn btn-primary">Save</button>

                </div>
            </div>
        </div>
    </div>
@endsection
