<div class="monitor-section-header">Justifications
</div>
<div class="divider"></div>

<div class="just-question-section section">

    <div class="justifications-section">
        <div class="row">
            <div class="input-field col s1">

                <select class="just-question-select">
                    @foreach($quiz->questions as $question)
                        @if(in_array($question['id'],$quiz->just_question['feedback']))
                            <option value="{{$question['id']}}" @if($loop->index == 0) {{'selected'}}@endif>{{'Q '.($loop->index+1)}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="input-field col answers-tabs-field">
                <div class="answers-tabs-row">
                    @foreach($quiz->questions[0]['answers'] as $answer)
                        <div class="answer-tab @if($loop->index ==0){{'selected'}}@endif"
                             data-index="{{$loop->index+1}}">{{ strtoupper(chr(64 + $loop->index+1))}}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="justifications-container">
            @php
                $first_question = reset($quiz->just_question['responses']);
            @endphp
            @if(isset($first_question) && is_array($first_question))
                @foreach($first_question as  $key => $answer_index)
                    @if($key == 0 || $key == 1)
                        @foreach($answer_index as $response)
                            <div class="justification-content-row">{{$response}}</div>
                        @endforeach
                    @endif
                    @break
                @endforeach
            @endif
        </div>
    </div>
</div>
