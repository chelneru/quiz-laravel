<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('admin-classes')}}">@lang('admin-classes.admin-classes-page-title')</a>
@endsection

@section('tab-title') - ADMIN - @lang('admin-classes.admin-classes-page-title') @endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/admin_classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-classes-page" data-users="{{json_encode($users_autocomplete)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        @if ($classes->total() > $classes->perPage())
                            {{ $classes->links() }}
                        @endif

                        <div class="input-field col s3 autocomplete-user">
                            <input type="text" id="autocomplete-user-input" class="autocomplete"
                                   value="@if($user_filter !== null){{$user_filter}}@else{{''}}@endif"  data-lpignore="true">
                            <label for="autocomplete-user-input">User</label>
                            @if(strlen(trim($user_filter)) >0)
                                <i class="clear-field material-icons grey-text text-lighteb-2">clear</i>
                            @endif
                        </div>

                        <table class="classes-table striped responsive-table">
                            <thead>
                            <tr>
                                <th class="name"
                                    data-function="@if($order_by_filter =='name'){{'sort-'.$order_dir_filter.'-name'}}@else{{'sort-asc-name'}}@endif">
                                    @lang('admin-classes.admin-classes-page-name-header')
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='name' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='name' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="author"
                                    data-function="@if($order_by_filter =='author'){{'sort-'.$order_dir_filter.'-author'}}@else{{'sort-asc-author'}}@endif">
                                    @lang('admin-classes.admin-classes-page-author-header')
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='author' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='author' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="last-enrollment"
                                    data-function="@if($order_by_filter =='last_enrollment'){{'sort-'.$order_dir_filter.'-last_enrollment'}}@else{{'sort-asc-last_enrollment'}}@endif">
                                    @lang('admin-classes.admin-classes-page-enrollment-header')
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='last_enrollment' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='last_enrollment' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="right-align">@lang('admin-classes.admin-classes-page-options-header')</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($classes as $class)
                                <tr id="{{$class->id}}">
                                    <td class="name-td">{{shortenedString(ucfirst($class->name),25)}}</td>
                                    <td class="author-td"><a
                                            href="{{route('admin-user-view',['user_id'=>$class->author_id])}}">{{$class->author}}</a>
                                    </td>

                                    <td class="last-enrollment-td">{{$class->last_enrollment}}</td>
                                    <td class="right-align">
                                        <!-- Dropdown Trigger -->
                                        <a class='dropdown-trigger black-text' href='#'
                                           data-target='dropdown{{$loop->index+2}}'>
                                            <i class="material-icons options-icon">more_vert</i>

                                        </a>
                                        <!-- Dropdown Structure -->
                                        <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                            <li class="inactive-button"><a class="modal-trigger"
                                                                           href="{{route('admin-edit-class',['class_id'=>$class->id])}}">@lang('admin-classes.admin-classes-page-edit-option')</a>
                                            </li>
                                            <li class="inactive-button"><a class="modal-trigger delete-class-option"
                                                                           href="#delete-class-modal">@lang('admin-classes.admin-classes-page-delete-option')</a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($classes->total() > $classes->perPage())

                            {{ $classes->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="query-admin-classes-form" method="get" action="{{route('admin-classes')}}">


        @if($order_by_filter !== null)
            <input type="hidden" name="order_by_filter" value="{{$order_by_filter}}">
        @endif
        @if($order_dir_filter !== null)
            <input type="hidden" name="order_dir_filter" value="{{$order_dir_filter}}">
        @endif
        @if($user_filter !== null)
            <input type="hidden" name="user_filter" value="{{$user_filter}}">
        @endif

    </form>
    @include('admin.confirm-delete-class')

    <script src="{{ asset('js/admin_classes.js',config('app.secure', null)) }}" defer></script>

@endsection
