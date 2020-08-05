@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('classes')}}">Classes</a>
    <a class="breadcrumb grey-text text-darken-4" href="{{route('create-class-page')}}">Create a class</a>
@endsection
@section('tab-title') - Create class @endsection
@section('help-page')
    <div class="help-modal-header">Create a class</div>
    <p>To create a class, you only need to submit the class name and the description. Participants are enrolled in classes, not quizzes. So, if you want your students to participate in your quizzes, you will need to first create a class, then invite participants and create quizzes in it. You will be able to do these after you press the <i>CREATE CLASS</i> button.</p>
@endsection
@section('content')
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">
    <div class="container create-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <form id="create-class-form" method="POST" action="{{route('create-class')}}">
                            @csrf
                            <div class="input-counter-div">
                                <input placeholder="Class name" id="class_name" maxlength="191" type="text"
                                       name="class_name">
                                <span class="input-counter">0/191</span>

                            </div>

                            <div class="input-counter-div">
                                <input placeholder="Class description" id="class_description" maxlength="191"
                                       type="text" name="class_description">
                                <span class="input-counter">0/191</span>

                            </div>

                        </form>
                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn cancel-create-btn  grey noselect" href="{{route('classes')}}">return to classes page</a>
                            <a class="btn create-class-btn right noselect">create class</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/classes.js',config('app.secure', null)) }}"></script>

@endsection
