@extends('layouts.app')
@section('tab-title') - Monitor quiz {{shortenedString($quiz->title,20)}}@endsection
@section('title')
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="#">Monitor page</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Monitor page</div>
    <p>This is a complex page in which you can control the different phases of the quiz and see in real-time the progress of the whole class.</p>
    <p>The menu at the top of the tab allows you to open each phase sequentially:</p>
      <ul>
          <li><i>START QUIZ</i>: Once you have started the quiz by clicking the <i>START QUIZ</i> button, the page shows the class progress and the buttons of the next phases.
</li>
          <li><i>ENABLE REVISION PHASE</i>: The next step while running a quiz is to click the <i>ENABLE REVISION PHASE</i> button to allow the participants that have finished the initial phase to move to the revision phase in which the aggregated feedback will be available alongside the quiz questions. If a participant has finished answering all the questions in the initial phase and the revision phase is not enabled, then the participant will see a waiting message. Once you enable the revision phase, this message automatically disappears and the participant is redirected to the first question of the quiz in the revision phase. On the contrary, if you enable the revision phase while participants are still midway in the initial phase, they will not notice a difference. Once they finish the initial phase, they will be redirected to the revision phase without waiting. The choice of when to enable each phase is up to you. Ideally, all participants should be at the same phase at all times. However, it is expected that some participants are faster than others. The more participants have finished the initial phase waiting for the revision phase to start, the louder the classroom will be. A suggestion, in case you control the sequence of the phases yourself and you have not scheduled it automatically, is to announce a time limit to the classroom (e.g., 10 minutes) and check during this timeframe the progress of the classroom. You can then decide whether you will need to give the students more time or if you can start the revision phase earlier.
</li>
          <li><i>REVEAL ANSWERS</i>: When participants reach the end of the revision phase, they see a second waiting message telling them that the answers will be revealed by the teacher soon. Once you click the <i>REVEAL ANSWERS</i> button, the correct answers and participants’ scores in the two phases will be available to them (individually). Participants that are midway the revision phase can still answer the remaining questions and they will see the answers when they finish.
</li>
          <li><i>QUIZ PRESENTATION</i>: It is likely that after you reveal the answers of the quiz to the participants, you will need to discuss with the audience. By clicking the <i>QUIZ PRESENTATION</i>, you will see a new window in which all quiz questions, along with their choices, correct answers, and audience answers (i.e., percentage that each choice received) in the two phases will be presented sequentially. You can go back and forth and you can close this new window at any point, without affecting the quiz.
</li>
          <li><i>STOP QUIZ</i>: Clicking the <i>REVEAL ANSWERS</i> button means that the quiz is essential over. However, you will need to click the STOP QUIZ button to actually close the quiz. Once you do that, the monitor page gets empty and the session is closed. Therefore, make sure that click the STOP QUIZ button only when you are certain that you will not need the quiz open anymore!
</li>
      </ul>
    <p>In the monitoring part of the page, you can see the percentage of the participants that have answered each question (so that you can track completion of the quiz) and the percentage each question choice received. The correct choice (marked with a green border) and the most selected choice by the participants (marked with green highlight) are noted, so that you can have an estimate of the classroom performance during the quiz runtime. If a question choice has both a green border and green highlight, it means that most of the students answered the question correctly. In any other case, it means that another choice was wrongly selected as the correct one by most participants.
    </p>
    <p>In case you have included justifications in your quiz, you will be able to see what participants wrote by selecting a question and a choice (only questions accompanied by a justification question will appear in the dropdown list).
    </p>
    <p>On the right of the buttons you can see the number of active, anonymous, and enrolled participants. The number of active participants shows how many people have started the quiz (i.e., answered at least the first question), while in brackets you can see how many of these people have started the quiz anonymously. On the next line, you can see the number of all the participants that have enrolled in the class that contains the quiz. So, for example, if you see something like that:</p>
    <p>Active Participants: 43 (11)<br>Enrolled Participants: 52</p>
    <p>It means that 43 participants submitted at least one answer to the quiz. From these 43, 11 answered the quiz anonymously (i.e., without logging into their SAGA account – this is possible only if you allow anonymous participation in the quiz settings). Finally, 53 participants are enrolled in the class that contains the quiz.</p>
    <p>In the monitoring part of the page, you can also see the percentage of the participants that have answered each question (so that you can track completion of the quiz) and the percentage each question choice received. The correct choice (marked with a green border) and the most selected choice by the participants (marked with green highlight) are noted, so that you can have an estimate of the classroom performance during the quiz runtime. If a question choice has both a green border and green highlight, it means that most of the students answered the question correctly. In any other case, it means that another choice was wrongly selected as the correct one by most participants.</p>
    <p>In case you have included justifications in your quiz, you will be able to see what participants wrote by selecting a question and a choice (only questions accompanied by a justification question will appear in the dropdown list).
    </p>
@endsection
@section('content')


    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/monitor_quiz.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container monitor-quiz-page">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="error-message-div"></div>

                <div class="default-panel main-panel z-depth-2" data-quiz-id="{{$quiz->id}}"
                     data-quiz-status="{{$quiz->status}}"
                     data-quiz-reveal-answers-status="@if(isset($quiz->reveal_answers)){{$quiz->reveal_answers}}@else{{'0'}}@endif"
                     data-quiz-phase="{{$quiz->phase}}" data-quiz="{{json_encode($quiz)}}"
                     @if(isset($quiz->active_participants_count))
                     data-quiz-participants-count="{{$quiz->active_participants_count}}"
                        @endif
                >
                    <div class="buttons-bar">
                        @if($quiz->status ==1)
                            <button class="btn modify-quiz-status">{{'stop quiz'}}</button>
                            <button class="btn modify-quiz-phase @if($quiz->phase == Config::get('defines.QUIZ_REVISION_PHASE')) {{'disabled'}}@endif">
                                enable revision phase
                            </button>
                        @else

                            <button class="btn modify-quiz-status" @if($quiz->overdue_start){{'disabled'}}@endif>{{"start quiz"}}</button>
                            @if($quiz->overdue_start)
                                <div class="overdue-start-text-div">
                                    Disabled START QUIZ button means that for this quiz there is an active scheduling
                                    which is past the current time. In order to be able to start the quiz you can either
                                    modify the quiz scheduling or deactivate the quiz scheduling <a
                                            href="/quiz/scheduling/{{$quiz->id}}">here</a>.
                                </div>
                            @endif
                        @endif
                        @if(isset($quiz->reveal_answers))
                            <button class="btn modify-quiz-reveal-answers" @if($quiz->reveal_answers == 1 || $quiz->phase == Config::get('defines.QUIZ_INITIAL_PHASE')) {{'disabled'}}@endif>{{'reveal answers'}}</button>
                        @endif
                            @if($quiz->phase !== null)

                            <a  target="_blank" href="{{ route('quiz-session-presentation',['session_id'=>$quiz->session_id]) }}" class="btn quiz-presentation-btn  @if(isset($quiz->reveal_answers) && $quiz->reveal_answers == 0) {{'disabled'}}@endif">{{'quiz presentation'}}</a>
@endif
                        @if($quiz->phase !== null)
                            <div class="session-info">
                                <div class="session-phase">Current phase
                                    : {{$quiz->phase == Config::get('defines.QUIZ_INITIAL_PHASE') ?'Initial phase':'Revision phase'}}</div>
                                <div class="session-active-participants">Active Participants
                                    : {{$quiz->active_participants_count}} ({{$quiz->active_anon_participants_count}})
                                </div>
                                <div class="session-enrolled-participants">Enrolled Participants
                                    : {{$quiz->enrolled_participants_count}}</div>
                            </div>
                        @endif

                    </div>
                    @if($quiz->status ==1)

                        @if($quiz->scheduling->id !== null && $quiz->scheduling->active == 1)
                            <div class="monitor-section-header">Timeline
                            </div>
                            <div class="divider"></div>
                            <div class="quiz-scheduling-section">

                                <div class="timeline-tab init-tab">
                                    <div class="timeline-tab-text">Initial</div>
                                    <div class="timeline-tab-value">
                                        <div class="timeline-tab-value-start">{{date('d M H:i',strtotime($quiz->scheduling->init_start))}}</div>
                                        -
                                        <div class="timeline-tab-value-end">{{date('d M H:i',strtotime($quiz->scheduling->init_end))}}</div>
                                    </div>
                                </div>
                                <div class="timeline-tab rev-tab">
                                    <div class="timeline-tab-text">Revision</div>
                                    <div class="timeline-tab-value">
                                        <div class="timeline-tab-value-start">{{date('d M H:i',strtotime($quiz->scheduling->rev_start))}}</div>
                                        -
                                        <div class="timeline-tab-value-end">{{date('d M H:i',strtotime($quiz->scheduling->rev_end))}}</div>
                                    </div>
                                </div>
                                <div class="timeline-tab ans-tab">
                                    <div class="timeline-tab-text">Answers reveal</div>
                                    <div class="timeline-tab-value">
                                        <div class="timeline-tab-value-start">{{date('d M H:i',strtotime($quiz->scheduling->ans_start))}}</div>
                                        -
                                        <div class="timeline-tab-value-end">{{date('d M H:i',strtotime($quiz->scheduling->ans_end))}}</div>
                                    </div>
                                </div>
                                <div class="timeline-tab">
                                    <div class="timeline-tab-text"><a href="#edit-quiz-scheduling-modal"
                                                                      class="open-schedule-edit-modal teal-text modal-trigger text-lighten-1 btn-flat">quick
                                            edit</a></div>
                                </div>
                            </div>
                        @endif
                        <div class="monitor-section-header">Quiz progress
                        </div>
                        <div class="divider"></div>

                        <div class="section">

                            <div id="progress_initial_chart"></div>
                            <div id="progress_revision_chart"></div>
                        </div>

                        <div class="monitor-section-header">Quiz answers percentages
                        </div>

                        <div class="divider"></div>
                        @include('monitor_quiz.percentage-section')

                        @if(isset($quiz->just_question))
                            @include('monitor_quiz.justification-section')
                        @endif

                        @if(isset($quiz->conf_question))
                            @include('monitor_quiz.confidence-section')
                        @endif
                    @endif

                    @if(isset($quiz->prep_question))
                        <div class="preparation-section inline">
                            <div class="monitor-section-header">Preparation
                            </div>
                            <div class="divider"></div>
                            <div id="preparation-pie-chart"></div>
                        </div>
                    @endif
                    <div class="divider"></div>

                    @if(isset($quiz->other_questions))
                        <div class="other-questions-section">
                        @foreach($quiz->other_questions as $other_question)
                            @include('monitor_quiz.other-questions-section')
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($quiz->scheduling->id !== null)
            @include('monitor_quiz.quiz-scheduling-edit-modal')

        @endif
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="/js/libs/moment.min.js"></script>

    <script src="{{ asset('js/monitor_quiz.js',config('app.secure', null)) }}" defer></script>

@endsection
