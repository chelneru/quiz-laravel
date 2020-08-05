<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('admin-quizzes')}}">@lang('admin-quizzes.admin-quizzes-page-title')</a></a>
@endsection

@section('tab-title') - ADMIN - @lang('admin-quizzes.admin-quizzes-page-title') @endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/admin_quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-quizzes-page" data-users="{{json_encode($users_autocomplete)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        @if ($quizzes->total() > $quizzes->perPage())
                            {{ $quizzes->links() }}
                        @endif
                        <div class="input-field col s3 autocomplete-user">
                            <input type="text" id="autocomplete-user-input" class="autocomplete"
                                   value="@if($user_filter !== null){{$user_filter}}@else{{''}}@endif"  data-lpignore="true">
                            <label for="autocomplete-user-input">User</label>
                            @if(strlen(trim($user_filter)) >0)
                                <i class="clear-field material-icons grey-text text-lighteb-2">clear</i>
                            @endif
                        </div>
                        <table class="quizzes-table striped responsive-table">
                            <thead>
                            <tr>
                                <th class="name"
                                    data-function="@if($order_by_filter =='name'){{'sort-'.$order_dir_filter.'-name'}}@else{{'sort-asc-name'}}@endif">
                                    Name
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='name' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='name' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="author"
                                    data-function="@if($order_by_filter =='author'){{'sort-'.$order_dir_filter.'-author'}}@else{{'sort-asc-author'}}@endif">
                                    Author
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='author' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='author' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="author"
                                    data-function="@if($order_by_filter =='status'){{'sort-'.$order_dir_filter.'-status'}}@else{{'sort-asc-status'}}@endif">
                                    Status
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='status' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='status' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($quizzes as $quiz)
                                <tr id="{{$quiz->id}}">
                                    <td class="name-td"><a href="{{route('quiz-info',['id'=>$quiz->id])}}">{{shortenedString(ucfirst($quiz->name),50)}}</a></td>

                                    <td class="author-td"><a href="{{route('admin-user-view',['user_id'=>$quiz->author_id])}}">{{$quiz->author}}</a></td>
                                    <td class="status-td">@if($quiz->session_id >0)
                                            <div class="running-status-icon pulse"></div>
                                        @else
                                            <div class="stopped-status-icon"></div>@endif</td>
                                    <td class="right-align">
                                        <!-- Dropdown Trigger -->
                                        <a class='dropdown-trigger black-text' href='#'
                                           data-target='dropdown{{$loop->index+2}}'>
                                            <i class="material-icons options-icon">more_vert</i>

                                        </a>
                                        <!-- Dropdown Structure -->
                                        <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                            <li class="inactive-button"><a class="modal-trigger"
                                                                           href="{{route('admin-edit-quiz',['quiz_id'=>$quiz->id])}}">Edit</a>
                                            </li>
                                            <li class="inactive-button"><a class="modal-trigger delete-quiz-option"
                                                                           href="#delete-quiz-modal">Delete</a>
                                            </li>
                                            {{--                                                <li class="inactive-button"><a class="modal-trigger reset-password-option"--}}
                                            {{--                                                                               href="#reset-user-password-modal">Reset password</a></li>--}}
                                        </ul>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($quizzes->total() > $quizzes->perPage())

                            {{ $quizzes->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="query-admin-quizzes-form" method="get" action="{{route('admin-quizzes')}}">
        @if($role_filter !== null)
            <input type="hidden" name="role_filter" value="{{$role_filter}}">
        @endif

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
        @include('admin.confirm-delete-quiz')
    <script src="{{ asset('js/admin_quizzes.js',config('app.secure', null)) }}" defer></script>

@endsection
