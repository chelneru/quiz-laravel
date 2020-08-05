@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('classes')}}">Classes</a>
    <a class="breadcrumb grey-text text-darken-4" href="{{route('copy-class-page',['class_id'=>$class_id])}}">Copy a class</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Copy a class</div>
    <p>When you duplicate a class you can select whether the teachers, participants, and existing quizzes within the class should be duplicated as well. If none of them is selected, then only the title and the description of the class is going to be copied in the new class. To differentiate between the old and the newly created class, the current date is used in the class title. You can change this as you wish.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container copy-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">

                    <div class="panel-body">
                        <form id="copy-class-form" method="POST" action="/class/copy-class">
                            @csrf

                            <input type="hidden" class="class-select" name="original_class_id" value="{{$class_id}}">

                            <div class="row">
                                <div class="input-counter-div">
                                    <input class="class_name" type="text" name="class_name" placeholder="Please enter the name of the new class" value="{{$new_class_name}}" maxlength="191" >
                                    <span class="input-counter">0/191</span>

                                </div>
                            </div>
                            <div class="section">
                                Choose copy options.
                            </div>
                            <div class="divider"></div>

                                    <label>
                                        <input type="checkbox" class="filled-in" checked="checked"  name="copy_teachers"/>
                                        <span>Copy the teachers.</span>
                                    </label>

                                    <label>
                                        <input type="checkbox" class="filled-in" checked="checked"  name="copy_participants"/>
                                        <span>Copy the participants.</span>
                                    </label>

                                    <label>
                                        <input type="checkbox" class="filled-in" checked="checked" name="copy_quizzes"/>
                                        <span>Copy the quizzes.</span>
                                    </label>

                        </form>
                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn cancel-create-btn  grey noselect" href={{route('classes')}}>return to classes page</a>
                            <a class="btn copy-class-btn right noselect">create class</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/classes.js',config('app.secure', null)) }}"></script>

@endsection
