@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('quizzes')}}">Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4" href="{{route('quiz-game-page')}}">Quiz game</a>
@endsection
@section('tab-title') - Quiz game @endsection
@section('help-page')
    <div class="help-modal-header">Quiz game</div>
    <p>Here, you can set up a gamification activity with SAGA. The tool does not support groups yet, so if you want to have an activity in which different groups of people compete against each other, you will need to have duplicates of the same quiz and share the different quiz links to the different participant groups. The gamification page and the results are not accessible to participants, so you will need to project the resulting graphs on the board.</p>
    <p>First, you will need to select the number of groups that you need (1-4). Then, you need to name the groups (use short names only as long names will mess up the interface of the graphs). Finally, for each group you need to copy in the form the direct link of the quiz. With the <i>ACTION</i> links you are able to start the quizzes and their different phases. By clicking the <i>GENERATE VIEW</i> button, SAGA will display graphs of the real-time performance of the groups. This is the page that you will need to project on the board for the participants to see.
    </p>
@endsection
@section('content')
    <link href="{{ asset('css/quiz_game.css',config('app.secure', null)) }}" rel="stylesheet">
    <div class="container quiz-game-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                    <div class="section-header">Gamification Activity Setup</div>
                    <div class="divider"></div>
                    <div class="section set-up-section">
                        <div class="no-of-groups">
                            <div class="select-quiz-count-label">Number of groups:</div>
                            <div class="input-field groups-count">
                                <select>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>

                        <div class="links-subsection">
                            <div class="quiz-link-headers">
                                <span class="group-name-header">Group names</span>
                                <span class="link-header">Direct link to the quiz</span>
                                <span class="status-header">Quiz status</span>
                            </div>

                            <div class="quiz-link-container"
                                 data-quiz-link-example="{{route('quiz-direct-link', ['quiz_id' => '0'])}}">
                                <div class="quiz-row">
                                    <input placeholder="Group A" class="group-input" type="text" name="quiz-name[]"
                                           data-valid="false" data-reveal="">

                                    <input placeholder="Example: {{route('quiz-direct-link', ['quiz_id' => '0000'])}}"
                                           type="text"
                                           name="quiz-link[]" class="quiz-input" data-valid="false" data-status=""
                                           data-phase="" data-reveal=""> <span
                                        class="quiz-status"></span>
                                </div>
                                <div class="quiz-row"
                                     style="display: none">
                                    <input placeholder="Group B" class="group-input" type="text" name="quiz-name[]"
                                           data-valid="false">
                                    <input placeholder="Example: {{route('quiz-direct-link', ['quiz_id' => '0000'])}}"
                                           type="text"
                                           name="quiz-link[]" class="quiz-input" data-valid="false" data-status=""
                                           data-phase="" data-reveal=""> <span
                                        class="quiz-status"></span>
                                </div>
                                <div class="quiz-row"
                                     style="display: none">
                                    <input placeholder="Group C" class="group-input" type="text" name="quiz-name[]"
                                           data-valid="false">
                                    <input placeholder="Example: {{route('quiz-direct-link', ['quiz_id' => '0000'])}}"
                                           type="text"
                                           name="quiz-link[]" class="quiz-input" data-valid="false" data-status=""
                                           data-phase="" data-reveal=""> <span
                                        class="quiz-status"></span>
                                </div>
                                <div class="quiz-row"
                                     style="display: none">
                                    <input placeholder="Group D" class="group-input" type="text" name="quiz-name[]"
                                           data-valid="false">
                                    <input placeholder="Example: {{route('quiz-direct-link', ['quiz_id' => '0000'])}}"
                                           type="text"
                                           name="quiz-link[]" class="quiz-input" data-valid="false" data-status=""
                                           data-phase=""> <span
                                        class="quiz-status"></span>
                                </div>
                            </div>
                        </div>
                        <div class="actions-section">
                            <div class="actions-header">Actions</div>
                            <div class=" btn-flat disabled start-quiz">start quizzes</div>
                            <div class=" btn-flat disabled start-revision">start
                                revisions
                            </div>
                            <div class=" btn-flat disabled reveal-answers">show
                                answers
                            </div>
                        </div>
                        <form id="game-run-form" method="post" action="{{route('quiz-game-run-page')}}" target="_blank">
                            @csrf

                        </form>
                        <div class="section-footer">
                            <button class="generate-graph-view-btn btn right">generate view</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="{{ asset('js/quiz_game.js',config('app.secure', null)) }}"></script>

@endsection
