@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('classes')}}">Classes</a>
    <a class="breadcrumb grey-text text-darken-4" href="{{route('class-edit',['id'=>$class->id])}}">Edit class</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Edit class</div>
    <p>Here, you can edit the information of a class, add quizzes in it, and invite participants. By default, you are also a participant in the quizzes you have created.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container page edit-class-page" data-class-id="{{$class->id}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <form id="edit-class-form" method="POST" action="{{route('class-edit-action')}}">
                            @csrf
                                <input name="class_id" type="hidden" value="{{$class->id}}">
                            <div class="input-counter-div">
                                    <input placeholder="Class name" id="class_name" maxlength="255" type="text" name="class_name"
                                           value="{{$class->name}}">
                                <span class="input-counter">{{strlen($class->name)}}/255</span>

                            </div>
                            <div class="input-counter-div">

                            <input placeholder="Class description" id="class_description" maxlength="255" type="text"
                                           name="class_description" value="{{$class->description}}">
                                <span class="input-counter">{{strlen($class->description)}}/255</span>

                            </div>


                            <div class="section">Quizzes</div>
                            <div class="divider"></div>
                            <div class="quiz-container">
                                @foreach($class_quizzes as $quiz)
                                    <div id="{{$quiz->id}}" class="quiz-row">
                                        <a href="{{route('quiz-info',['id'=>$quiz->id])}}">{{shortenedString($quiz->title,50)}}</a>
                                        <i class="material-icons right modal-trigger right remove-icon tooltipped" href="#modal1"  data-position="top" data-tooltip="Remove quiz">close</i>

                                    </div>
                                @endforeach
                            </div>
                           <i id="add_quizzes" class="small material-icons teal-text text-lighten-1 tooltipped modal-trigger"
                                                        href="#add_existing_quiz_modal" data-position="top" data-tooltip="Add new existing quiz">add</i>

                            <div class="section">Participants</div>
                            <div class="divider"></div>
                            <div class="participants-container">
                                @foreach($class_participants as $participant)
                                    <div  id="{{$participant->id}}" class="participant-row">
                                        <span>{{$participant->name}}</span>
                                        @if($participant->id != Auth::user()->u_id)
                                        <i class="material-icons right modal-trigger remove-icon tooltipped" href="#modal2"  data-position="top" data-tooltip="Dismiss participant">close</i>
                                            @endif
                                        {{--<i class="material-icons right modal-trigger remove-icon" href="#modal2">close</i>--}}

                                    </div>
                                @endforeach
                            </div>
                            <a id="invite_participants" class="small material-icons teal-text text-lighten-1 tooltipped"
                                                        href="{{route('invite-participants',['class_id'=>$class->id])}}" data-position="top" data-tooltip="Invite participants">add</a>
                        </form>


                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn cancel-create-btn  grey noselect" href={{route('classes')}}>return to classes page</a>
                            <a class="btn edit-class-btn right noselect">save changes</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div id="modal1" class="modal">
        <i class="material-icons right modal-close">close</i>

        <div class="modal-content">
            <div style="padding:0 16px;margin-bottom: 30px;">How do you want to remove this quiz?</div>
            <div class="row">
                <a class="btn-flat teal-text lighten-1 unlink-quiz">Remove the quiz from this class</a>

            </div>
            <div class="row">

                <a class="btn-flat teal-text lighten-1 delete-quiz">Delete the quiz permanently</a>
            </div>

        </div>

    </div>

    <div id="modal2" class="modal">
        <i class="material-icons right modal-close">close</i>

        <div class="modal-content">
            Are you sure you want to dismiss this participant from the class?
            <div class="modal-footer">
                <a class="btn grey modal-close">no</a>

                <a class="btn right dismiss-participant">yes</a>

            </div>


        </div>

    </div>
    </div>

    @php
        $class_id = $class->id;
    @endphp
    @include('quizzes.add-existing-quiz')

    <script src="{{ asset('js/classes.js',config('app.secure', null)) }}"></script>

@endsection
