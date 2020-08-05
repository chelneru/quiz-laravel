@extends('layouts.app')

@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4" href="#">Accompanying questions</a>
@endsection
@section('tab-title') - Accompanying Question @endsection

@section('help-page')
    <div class="help-modal-header">Accompanying questions</div>
    <p>Here, you can define what kind of accompanying questions will appear alongside your quiz. You can
        define which one and where to appear. You can also create your own. Each accompanying question
        can be used as a feedback metric after the initial phase in SAGA. There are already three readily
        available accompanying questions that you can use (and three respective feedback metrics):
        <i>Preparation</i>, <i>Confidence</i>, and <i>Justification</i>.</p>
    <div class="help-modal-header">Preparation question</div>
    <p>The preparation question appears only once, just before the start of a quiz. The purpose of the question is to record participants’ opinions about their prior knowledge. In other words, the preparation question is a measure of participants’ self-assessment. Participants’ accuracy in self-assessing themselves can vary. You can analyze this accuracy after the end of the quiz by comparing participants’ answers in the preparation question and their actual performance. This question makes more sense when the participants have already engaged with the material of the quiz, either by attending a lecture or by reading learning material before the lecture. The text and scale of the question are pre-entered in the system just to help you. You can change both of them to your needs. However, you cannot change the <i>Question type</i>. The preparation question must always have a scale, so that an average will be calculated as a feedback metric later on.</p>
    <div class="help-modal-header">Confidence question</div>
    <p>The confidence question is another measure of participants’ self-assessment that appears below a quiz question. Therefore, the confidence question is another measure of participants’ self-assessment, but contrary to the preparation question, participants can state their confidence levels after they have seen and answered the quiz question. The text and scale of the question are pre-entered, but you can change them as you see fit. You cannot change, however, the <i>Question type</i> as the confidence question must always have a scale, so that an average will be calculated as a feedback metric later on. By clicking on the <i>Appearance</i> link at the bottom of the confidence question tab, you can select where you want the confidence question to appear. For example, you may want this accompanying question to appear alongside only some of your quiz questions. Also, you can decide whether this question will be used as a feedback metric. If the box <i>Question (Initial Phase)</i> is checked, the confidence question will appear below that question in the initial phase of the quiz. If the box <i>Feedback (Revision Phase)</i> is checked, then the average confidence score of all participants that answered this question will appear as a feedback metric (i.e., one confidence score for each one of the question choices). Of course, you cannot use an accompanying question as a feedback metric if you have not previously used the question in the initial phase of the quiz. On the contrary, you can use an accompanying question in the initial phase, but not as a feedback metric. For example, you want to ask participants to reflect on their confidence, but you do not want to use the confidence scores as feedback. Note that the confidence question will appear in both phases of the quiz. In other words, the participants will have to answer the confidence question twice. This is to measure how much their confidence has changed after seeing the aggregated feedback. So, the <i>Feedback (Revision Phase)</i> only asks whether the confidence question should be used as a feedback metric and not whether or not it should appear in the revision phase – it will determine whether you use it as a feedback metric or not.
    </p>
    <div class="help-modal-header">Justification question</div>
    <p>The justification question allows the participant to provide a short (up to 140 characters, including spaces) justification on their answers. Explicitly elaborating on one’s understanding triggers cognitive processes that can aid learning gains. As such, you may want to allow some elaboration space to the participants. The justification length is purposefully short to make sure that the quiz will take a short time (and that it can be used within a lecture timeframe) and because it will be easier for the participants to read them, in case you want to use them as a feedback metric in the revision phase of the quiz. As in the confidence question, you can decide where the justification will appear inside your quiz by clicking on the <i>Appearance</i> link at the bottom of the justification question tab. You cannot change the <i>Question type</i> as the justification question must always be open text.
    </p>
    <div class="help-modal-header">Add other question</div>
    <p>By clicking the “+” sign at the bottom of the page, you can create your own accompanying question. You will first need to name your question, then add the question text and description, and then decide whether this additional accompanying question should appear <i>before/inside/after</i> the quiz. “Before” means that the question will appear before the first quiz question (i.e., this is the same place the preparation question appears). “Inside” means that the question will appear alongside quiz questions (i.e., this is the same place the confidence question appears). “After” means that the question will appear to the participants after the last quiz question has been answered. You can also decide whether your accompanying question is going to be using a scale or open text. Accompanying questions outside of the quiz can only have scale structure. Note that if you decide to use your additional accompanying question inside the quiz, you will have an additional option appearing: <i>Question available for answering in the initial phase</i>. This is not about the use of the question as a feedback metric but about whether you want the students to answer this accompanying question twice (i.e., in both phases of the quiz). </p>
  <p style="color: red">IMPORTANT: Adding your own accompanying questions gives you a lot of freedom but also creates the risk that the settings of the question you want to add may not work properly with other parts of the tool. Use additional accompanying questions very cautiously! </p>
    <p>In Figure 1, you can see a screenshot of SAGA during the revision phase. In the depicted question, there are four feedback metrics used: percentage, confidence, preparation, and justification. Note that the confidence question appears at the bottom of the quiz question tab and the participants can revise their initial answers there as well.</p>
    <img src="/images/Revision.png" style="width: 100%">
    Figure 1. SAGA Screenshot during the revision phase.

@endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/accompanying_questions.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    @php
        $question_counter = 0;
    @endphp
    <div id="{{$quiz->id}}" class="container quiz-accompanying-questions-page page" data-interaction-type="@if($create_quiz_mode){{'create'}}@else{{'edit'}}@endif">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="prep-row row">
                            <label>
                                <input type="checkbox" class="filled-in"
                                       @if($accompanying_questions->prep_question->id !== null){{'checked'}}@endif name="enable_prep_question"/>
                                <span>Preparation question</span>
                            </label>
                        </div>
                        <div class="question_options"
                             style="@if($accompanying_questions->prep_question->id !== null){{'display:block;'}}@endif">
                            @include('quizzes.preparation-question-panel')
                        </div>
                        <div class="conf-row row">
                            <label>
                                <input type="checkbox" class="filled-in"
                                       @if($accompanying_questions->conf_question->id !== null){{'checked'}}@endif name="enable_conf_question"/>
                                <span>Confidence question</span>
                            </label>
                        </div>
                        <div class="question_options"
                             style="@if($accompanying_questions->conf_question->id !== null){{'display:block;'}}@endif">
                            @include('quizzes.confidence-question-panel')
                        </div>
                        <div class="just-row row">
                            <label>
                                <input type="checkbox" class="filled-in"
                                       @if($accompanying_questions->just_question->id !== null){{'checked'}}@endif name="enable_just_question"/>
                                <span>Justification question</span>
                            </label>
                        </div>
                        <div class="question_options"
                             style="@if($accompanying_questions->just_question->id !== null){{'display:block;'}}@endif">
                            @include('quizzes.justification-question-panel')

                        </div>
                        <div class="other-questions-container">
                            @if(isset($accompanying_questions->other_questions))
                                @foreach($accompanying_questions->other_questions as $other_question)
                                    @include('quizzes.filled-other-question-panel')
                                @endforeach
                            @endif
                        </div>
                        <div class="add-new-other-question">
                            <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="top" data-tooltip="Add other question">add</i>
                        </div>

                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn save-accompanying-questions-btn right noselect">@if($create_quiz_mode === true){{'set accompanying questions (2/4)'}}@else{{'save changes'}}@endif</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="answer-row to-be-cloned row">
        <div class="input-field input-counter-div">
            <input type="text" class="answer-text" maxlength="3" value="1">
            <span class="input-counter">0/255</span>

        </div>
        <i class="material-icons delete-answer-icon">close</i>
    </div>
    <div class="question_options to-be-cloned other-question-panel">

        @include('quizzes.other-question-panel')
    </div>


    {{--workaround because materialize has a bug for initializing dynamically created select elements--}}
    <div class="hidden-selects">
        <div class="hidden-sel-position">
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
            <select class="question-position" name="question_location">
                <option value="1" selected>before</option>
                <option value="2">inside</option>
                <option value="3">after</option>
            </select>
        </div>
        <div class="hidden-sel-type">
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
            <div class="input-field">
                <select class="question-type-dropdown">

                    <option value="2">Rating question</option>
                    <option value="3">Text field question</option>
                </select>
                <label>Question type</label>
            </div>
        </div>
    </div>


    <script src="/js/Sortable.min.js" type="text/javascript"></script>

    <script src="{{ asset('js/accompanying_questions.js',config('app.secure', null)) }}" defer></script>

@endsection
