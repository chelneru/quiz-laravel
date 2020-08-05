<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app-no-menu')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="">&nbsp;&nbsp;Validate Teachers</a>
@endsection

@section('content')

    <link href="{{ asset('css/register.css',config('app.secure', null)) }}" rel="stylesheet">
    <div class="container validate-teachers-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">

                        <table class="teachers-table striped responsive-table">
                            <thead>
                            <tr>
                                <th class="name">Name</th>
                                <th class="email">Email</th>
                                <th class="creation_date">Creation date</th>
                                <th class="last_login">Last login</th>
                                <th class=" ">Status</th>
                                <th class="ops">Operations</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($teachers as $teacher)
                                <tr id="{{$teacher->id}}">
                                    <td class="name-td"><a href="{{route('admin-user-view',['user_id'=>$teacher->id])}}">
                                            {{shortenedString(ucfirst($teacher->first_name),30).' '.shortenedString(ucfirst($teacher->last_name),30)}}</a>
                                    <td class="email-td">{{shortenedString($teacher->email,60)}}</td>
                                    <td class="classes-td">{{$teacher->created_at}}</td>
                                    <td class="quizzes-td">{{$teacher->last_login}}</td>
                                    <td class="status-td">@if($teacher->status == 1){{'Validated'}}@else{{'Not validated'}} @endif</td>
                                    <td class="">
                                        @if($teacher->status == 0)
                                    <button class="btn btn-link validate-btn">validate</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin_validate_teachers.js',config('app.secure', null)) }}" defer></script>


@endsection
