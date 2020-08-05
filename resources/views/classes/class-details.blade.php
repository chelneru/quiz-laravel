@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('classes')}}">Classes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="{{route('class-info',['id'=>$class->id])}}">Class details</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Class details</div>
    <p>Here, you can have an overview of the class information, along with the quizzes in the class. By clicking the <i>EDIT CLASS</i> button, you can edit a class and remove a quiz from it. Removing a quiz will not delete the quiz. It will just make it an orphan.</p>
@endsection
@section('content')
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container class-details-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Title:</div>
                                <div class="panel-info-field">{{shortenedString($class->name,50)}}</div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Description:</div>
                                <div class="panel-info-field">{{shortenedString($class->description,50)}}</div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label"> Quizzes:</div>
                                <table class="quizzes-table">
                                    @foreach($class_quizzes as $quiz)
                                        <tr>
                                            @if(strlen(trim($quiz->description)) > 0 )
                                            <td class="tooltipped" data-position="right" data-tooltip="{{shortenedString($quiz->description,50)}}"><a
                                                        href="{{route('quiz-info',['id'=>$quiz->id])}}">{{shortenedString($quiz->title,50)}}</a></td>
                                                @else
                                                <td><a
                                                        href="{{route('quiz-info',['id'=>$quiz->id])}}">{{shortenedString($quiz->title,50)}}</a></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label"> Participants:</div>
                                <table class="participants-table">
                                    @foreach($class_participants as $user)
                                        <tr>
                                            <td class="name-td"> {{$user->name}}</td>
                                            <td class="role-td">@if($user->id == $class->created_by){{'- Creator'}}
                                                @elseif ($user->role == 2 ) {{'- Teacher'}}
                                                @endif</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <a class="btn cancel-create-btn grey noselect" href="{{route('classes')}}">return to classes
                                page</a>
                            <a class="btn noselect edit-btn" href="{{route('class-edit',['id'=>$class->id])}}">edit class</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            $('.tooltipped').tooltip();
        });
    </script>
@endsection
