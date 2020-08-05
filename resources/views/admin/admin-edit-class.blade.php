<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app-no-menu')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('admin-classes')}}">Users</a>

    <a class="breadcrumb grey-text text-darken-4"
                    href="{{route('admin-edit-class',['class_id'=>$class_info->id])}}">@lang('admin-classes.edit-class-page-title')</a>
@endsection
@section('tab-title') - ADMIN - @lang('admin-classes.edit-class-page-title') @endsection

@section('content')

    <link href="{{ asset('css/admin_edit_class.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-edit-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="card-body">
                        <form id="edit_class_form" method="POST"
                              action="{{route('admin-edit-class-action')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$class_info->id}}">
                            <div class="panel-row">
                                <div class="panel-input-field input-counter-div">
                                    <input id="name" type="text"
                                           class="panel-text-input"
                                           name="name" maxlength="191"
                                           value="{{$class_info->name}}" placeholder="@lang('admin-classes.class-name-field-placeholder')" required>
                                    <span class="input-counter">0/191</span>
                                </div>
                            </div>

                            <div class="panel-footer">
                                <div class="panel-row">
                                    <a href="{{route('admin-classes')}}" class="btn grey">
                                        @lang('admin-classes.go-back')
                                    </a>
                                    <button type="submit" class="btn edit-class-btn">
                                        @lang('admin-classes.edit-class')

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
