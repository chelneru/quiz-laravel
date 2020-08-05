@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href={{route('classes')}}>Classes</a></a>
@endsection
@section('action-buttons-space')
    <a class="btn" href="/class/create-class">create new class</a>
@endsection

@section('tab-title') - Classes @endsection
@section('help-page')
    <div class="help-modal-header">Classes</div>
    <p>Here, you can have a comprehensive picture of all your classes, the number of quizzes and participants in each of them and the date they were created.</p>
    <p>You can click on the <i>CREATE NEW CLASS</i> button to create a new class, or click on the option icon on a class row to edit the class, duplicate it, invite participants in it, or delete it completely.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container classes-page">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        @if ($classes->total() > $classes->perPage())
                            {{ $classes->links() }}
                        @endif
                        <table class="quizzes-table striped responsive-table">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Quizzes</th>
                                <th>Participants</th>
                                <th class="right-align">Created on</th>
                                <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($classes as $class)
                                <tr id="{{$class->id}}">
                                    <td>
                                        @if($class->description != "")
                                            <a href="/class/class-info/{{$class->id}}" class="tooltipped"
                                               data-position="top"
                                               data-tooltip="{{shortenedString($class->description,50)}}">{{shortenedString($class->name,50)}}</a>

                                        @else
                                            <a href="/class/class-info/{{$class->id}}">{{shortenedString($class->name,50)}}</a>

                                        @endif
                                    </td>

                                    <td>{{$class->quizzes}}</td>
                                    <td>{{$class->participants}}</td>
                                    <td class="right-align">{{$class->date}}</td>
                                    <td class="right-align">

                                        <!-- Dropdown Trigger -->
                                        <a class='dropdown-trigger black-text' href='#'
                                           data-target='dropdown{{$loop->index+2}}'>
                                            <i class="material-icons options-icon">more_vert</i>

                                        </a>
                                        <!-- Dropdown Structure -->
                                        <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                            <li><a href="/class/edit-class/{{$class->id}}">Edit</a></li>
                                            <li><a href="/class/copy-class/{{$class->id}}">Duplicate</a></li>
                                            <li><a href="/class/invite-participants/{{$class->id}}">Invite
                                                    participants</a></li>
                                            <li><a class="delete-class-menu-btn modal-trigger"
                                                   href="#class-delete-confirm-modal" data-class-id="{{$class->id}}">Delete
                                                    class</a></li>
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
        @include('classes.confirmation-panel')
    </div>
    <script src="{{ asset('js/classes.js',config('app.secure', null)) }}" defer></script>

@endsection

