<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('admin-users')}}">Users</a></a>
@endsection
@section('action-buttons-space')
    <a class="btn" href="{{ route('admin-manage-user') }}">create user</a>
@endsection
@section('tab-title') - ADMIN - Users @endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/admin_users.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-users-page" data-users="{{json_encode($users_autocomplete)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        @if ($users->total() > $users->perPage())
                            {{ $users->links() }}
                        @endif

                        <div class="input-field col s3 autocomplete-user">
                            <input type="text" id="autocomplete-user-input" class="autocomplete" data-lpignore="true"

                                   value="@if($user_filter !== null){{$user_filter}}@endif">
                            @if(strlen(trim($user_filter)) >0)
                            <i class="clear-field material-icons grey-text text-lighteb-2">clear</i>
                            @endif
                            <label for="autocomplete-user-input">User</label>
                        </div>
                        <div class="input-field col s3">
                            <select id="role-dropdown">
                                <option value="" @if($role_filter === null){{'selected'}}@endif>All roles</option>
                                <option @if($role_filter == 1){{'selected'}}@endif value="1">Participants</option>
                                <option @if($role_filter == 2){{'selected'}}@endif value="2">Teachers</option>
                            </select>
                            <label>Role filter</label>
                        </div>
                        <table class="users-table striped responsive-table">
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
                                <th class="email"
                                    data-function="@if($order_by_filter =='email'){{'sort-'.$order_dir_filter.'-email'}}@else{{'sort-asc-email'}}@endif">
                                    Email
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='email' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='email' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="classes"
                                    data-function="@if($order_by_filter =='class_count'){{'sort-'.$order_dir_filter.'-class'}}@else{{'sort-asc-class'}}@endif">
                                    Classes
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='class_count' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='class_count' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="quizzes"
                                    data-function="@if($order_by_filter =='quiz_count'){{'sort-'.$order_dir_filter.'-quiz'}}@else{{'sort-asc-quiz'}}@endif">
                                    Quizzes
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='quiz_count' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='quiz_count' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="register-date"
                                    data-function="@if($order_by_filter =='created_at'){{'sort-'.$order_dir_filter.'-register_date'}}@else{{'sort-asc-register_date'}}@endif">
                                    Register date
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='created_at' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='created_at' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="register-date"
                                    data-function="@if($order_by_filter =='last_login'){{'sort-'.$order_dir_filter.'-last_login'}}@else{{'sort-asc-last_login'}}@endif">
                                    Last login
                                    <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='last_login' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='last_login' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($users as $user)
                                <tr id="{{$user->id}}" role="{{$user->role}}">
                                    <td class="name-td"><a href="{{route('admin-user-view',['user_id'=>$user->id])}}">
                                            @if(strlen(trim($user->name)) >0){{shortenedString(ucfirst($user->name),25)}}@else<i>{{'<no name>'}}</i>@endif</a></td>
                                    <td class="email-td">{{shortenedString($user->email,40)}}</td>
                                    <td class="classes-td">{{$user->class_count}}</td>
                                    <td class="quizzes-td">{{$user->quiz_count}}</td>
                                    <td class="register-td">{{$user->created_at}}</td>
                                    <td class="last-login-td">{{$user->last_login}}</td>
                                    <td class="right-align">
                                    @if($user->id != Auth::user()->u_id)
                                        <!-- Dropdown Trigger -->
                                            <a class='dropdown-trigger black-text' href='#'
                                               data-target='dropdown{{$loop->index+2}}'>
                                                <i class="material-icons options-icon">more_vert</i>

                                            </a>
                                            <!-- Dropdown Structure -->
                                            <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                                <li class="inactive-button"><a class="modal-trigger"
                                                                               href="{{ route('admin-manage-user',['user_id'=>$user->id]) }}">Edit</a>
                                                </li>
                                                <li class="inactive-button"><a class="modal-trigger delete-user-option"
                                                                               href="#delete-user-modal">Delete</a>
                                                </li>
                                                <li class="inactive-button"><a
                                                        class="modal-trigger reset-password-option"
                                                        href="#reset-user-password-modal">Reset password</a></li>
                                            </ul>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($users->total() > $users->perPage())

                            {{ $users->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="query-admin-users-form" method="get" action="/admin-users-panel">
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
    @include('admin.confirm-delete-user')
    @include('admin.confirm-reset-password')

    <script src="{{ asset('js/admin_users.js',config('app.secure', null)) }}" defer></script>

@endsection
