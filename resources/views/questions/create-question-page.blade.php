@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('questions')}}">Questions</a>
    <a class="breadcrumb grey-text text-darken-4" href="/create-question">Create a question</a>
@endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/questions.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container questions-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="section">New Question</div>
                    <div class="divider"></div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Lorem ipsum dolor sit amet ?" id="question_text" type="text"
                                       class="">
                            </div>

                        </div>
                        <div class="section">
                            Choose Quiz
                        </div>
                        <div class="divider"></div>

                        <div class="row">
                            <div class="input-field col s3">
                                <select>
                                    <option value="" selected>No Quiz selected</option>
                                    <option value="1">Quiz 1</option>
                                    <option value="2">Quiz 2</option>
                                    <option value="3">Quiz 3</option>
                                </select>
                            </div>
                        </div>

                            <div class="section">
                                Question answers
                            </div>
                            <div class="divider"></div>
                            <div id="answers" class="section answers-container">
                                <div class="row answer-row">
                                    <div class="input-field col s5 inline">
                                        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>
                                        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">
                                    </div>
                                    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect">clear</i>
                                </div>

                                <div class="row answer-row">
                                    <div class="input-field col s5 inline">
                                        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>
                                        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">
                                    </div>
                                    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect">clear</i>
                                </div>

                                <div class="row answer-row">
                                    <div class="input-field col s5 inline">
                                        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>
                                        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">
                                    </div>
                                    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect">clear</i>
                                </div>

                                <div class="row answer-row">
                                    <div class="input-field col s5 inline">
                                        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>
                                        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">
                                    </div>
                                    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect">clear</i>
                                </div>

                            </div>
                            <div class="answer-container-action">
                                <a class="btn-small add-new-answer-btn noselect"><i class="material-icons left">add</i>new
                                    answer</a>
                            </div>

                        </div>
                        <div class="panel-footer">

                            <div class="row">
                                <a class="btn cancel-create-btn  grey noselect" href="{{route('questions')}}">return to questions page</a>
                                <a class="btn create-question-btn right noselect">create question</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/Sortable.min.js" type="text/javascript"></script>
        <script src="{{ asset('js/create_question.js',config('app.secure', null)) }}"></script>

@endsection
