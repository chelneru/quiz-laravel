@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    @if(isset($quiz->id))
        <a class="breadcrumb grey-text text-darken-4" href="/quiz/edit-quiz/{{$quiz->id}}">{{'Edit quiz'}}</a>
        <a class="breadcrumb grey-text text-darken-4"
           href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,40)}}</a>
    @else
        <a class="breadcrumb grey-text text-darken-4" href="/quiz/create-quiz/">{{'Create a quiz'}}</a>

    @endif
@endsection

@section('tab-title') - Create quiz @endsection

@section('help-page')
    <div class="help-modal-header">Create a quiz</div>
    <p>Here, you can create a new quiz. Each quiz must have a title and a description, but these are not necessarily unique. You can have quizzes with the same title.</p>
    <p>If you check the option <i>Allow anonymous participation</i>, then participants will be able to see and participate in the quiz even without a SAGA account. They will only need the direct link to the quiz (this appears in the quiz information page, after you create the quiz).</p>
    <p>If you check the option <i>This quiz is for assessment</i>, then you, as the quiz-creator, will be able to see the names of the participants along with their scores. By default, quizzes in SAGA are used for self-assessment. This means that if a quiz is not marked for assessment, then you will not be able to use the participants’ names as their performance is anonymous. You will be able to see, instead their user id. The user id is a unique number assigned automatically to an account by the system. If a quiz is used for self-assessment, you, as the quiz-creator, will be able to see, for example the performance of User 42, but you will not know the identity of the user.</p>
    <p style="color: red">IMPORTANT NOTE: In case you want to use a quiz for assessment, you are responsible for informing the participants that their information will be visible to you, and getting their consent.</p>
    <p>Each quiz can only belong to one class. You can assign a quiz to one of your own classes by selecting the class in the dropdown list. If you do not select a class at this stage, then the quiz will be created as an orphan. This can be useful in case you want to check the quiz yourself before having it appear in a class. You can place an existing quiz in a class at any point by editing the quiz and selecting a class.</p>
    <p>You can create as many questions as you want inside a quiz and reorder them by dragging and dropping them around. The same applied for the order of the question choices. Each question must have the question text (title), at least one choice, and one choice marked as <i>CORRECT</i>. Optionally, a question can also have an image. You can use direct links, Dropbox links, or Google links (make sure you use the appropriate sharing link and not the URL). You can check whether your image is properly imported by clicking the <i>PREVIEW IMAGE</i> button. Finally, you can copy a question (and edit it later) by clicking the copy icon. In case you have created other quizzes, you can import an existing question to the new quiz by clicking on the <i>IMPORT A QUESTION</i> link. A copy of the existing question will be created in the new quiz. This means that if you change the existing question in one quiz it will NOT change automatically in other quizzes.</p>
    <p>When you have finished with the questions, click the <i>CREATE QUIZ (1/4)</i> button to continue to the next step of creating a quiz.</p>
@endsection
@section('content')
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container create-quiz-page"
         data-interaction-type="@if(isset($quiz->id)){{'edit'}}@else{{'create'}}@endif">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="error-message-div"></div>

                <div class="default-panel z-depth-2" id="@if(isset($quiz->id)){{$quiz->id}}@endif">
                    @php
                        $question_counter = 0;
                    @endphp


                    <div class="panel-body">
                        <div class="input-counter-div">
                            <input placeholder="Enter quiz title here" id="quiz_title" maxlength="255" type="text"
                                   value="@if(isset($quiz->title)) {{$quiz->title}}@endif">
                            <span class="input-counter">0/255</span>
                        </div>
                        <div class="input-counter-div">

                            <input placeholder="Enter quiz description here" id="quiz_description" maxlength="255"
                                   type="text"
                                   class=""
                                   value="@if(isset($quiz->description)){{$quiz->description}}@endif">
                            <span class="input-counter">0/255</span>
                        </div>
                        @if(isset($quiz->id))
                            <div class="quiz-direct-link">
                                <div class="direct-link-label">Quiz direct link:</div>
                                <div class="direct-link-value"><a id="quiz-link"
                                                                  href="{{$quiz->link}}">{{$quiz->link}}</a></div>
                                <button class="btn-flat teal-text copy-direct-link">copy</button>
                            </div>
                        @endif
                        <div>
                        <label>
                            <input type="checkbox" class="filled-in"
                                   @if(isset($quiz->allow_anon) && $quiz->allow_anon === true){{'checked="checked"'}}@endif name="anon_participation"/>
                            <span>Allow anonymous participation</span>
                        </label>
                        </div>
                        <div>
                        <label>
                            <input type="checkbox" class="filled-in"
                                   @if(isset($quiz->is_assessed) && $quiz->is_assessed === true){{'checked="checked"'}}@endif name="quiz_is_assessed"/>
                            <span>This quiz is for assessment</span>
                        </label>
                        </div>
                        <div class="class-select-div">
                            <select class="class-select" name="class-select">
                                @if((isset($quiz->class_id) && isset($quiz->class_name))|| (isset($selected_class_id) && $selected_class_id !== null))

                                    <option value="" disabled>Choose a class</option>
                                    @foreach($classes as $class)
                                        <option class="black-text" value="{{$class->id}}"
                                        @if((isset($quiz->class_id) && $class->id == $quiz->class_id) || (isset($selected_class_id) &&$selected_class_id == $class->id)){{'selected'}}
                                            @endif>{{shortenedString($class->name,35)}}</option>

                                    @endforeach
                                @else
                                    <option value="" disabled selected>Choose a class</option>
                                    @foreach($classes as $class)
                                        <option class="black-text"
                                                value="{{$class->id}}">{{shortenedString($class->name,35)}}</option>

                                    @endforeach
                                @endif

                            </select>
                        </div>
                        <div id="questions-container">
                            @include('quizzes.question-panel')
                            <div class="add-new-question">
                                <a class="btn-flat teal-text lighten-1 new-question-button">add new question</a> OR
                                <a class="btn-flat teal-text lighten-1 import-question-button modal-trigger"
                                   href="#quiz-import-question-modal">import a question</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <div
                                class="btn create-quiz-btn noselect">@if(isset($quiz->id)){{'save changes'}}@else{{'create quiz (1/4)'}}@endif</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('quizzes.question-panel-to-be-cloned')
        @include('quizzes.answer-row')
        @include('quizzes.image-preview-modal')
        @include('quizzes.import-question-modal')
    </div>

    <script src="/js/Sortable.min.js" type="text/javascript"></script>
    <script src="{{ asset('js/app.js',config('app.secure', null)) }}"></script>
    <script src="{{ asset('js/create_quiz.js',config('app.secure', null)) }}"></script>

@endsection
