@extends($layout)
@section('title')<a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a></a>
@endsection
@section('tab-title') - Waiting for next phase @endsection

@section('content')

    <style>
        .wait-next-phase {
            text-align: center;
        }
        .loading-image {
            width: 50px;
            height: 50px;
        }
    </style>
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container wait-next-phase-page" data-quiz-id="{{$quiz_id}}" >
        <div class="row justify-content-center">
            <div class="col-md-8">


                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="wait-next-phase">
                            <div>Wait for the next phase (when the revision phase is started this page will redirect to the revision phase)</div>
                            <img class="loading-image" src="../images/loading.gif">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script >
        var got_previous_response = false;
        var first_request = true;
        $(document).ready(function () {
            var quiz_id = $('.wait-next-phase-page').attr('data-quiz-id');
            let intervalId = setInterval(checkQuizPhase, 1000); //3000 MS == 3 seconds

            function checkQuizPhase() {
                console.log('checking phase...');
               if((got_previous_response !== false && first_request == false) ||
                   (got_previous_response === false && first_request == true )) {
                   first_request = false;
                   $.post({
                       url: '/quiz/get-quiz-phase',
                       headers: {
                           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       },
                       data: {quiz_id :quiz_id }
                   }).done(function (data, textStatus, jqXHR) {

                       var result = JSON.parse(data);

                       if (result.status == true) {
                           got_previous_response = true;
                           if(result.phase == 2) {
                               clearInterval(intervalId);

                               //revision phase has been activate , redirect to quiz progress page
                           window.location = '/quiz/'+quiz_id;
                           }
                       } else {
                           ShowGlobalMessage('An error occurred while retrieving the next phase status.', 2);
                           console.log(result.message);
                       }
                   }).fail(function (jqXHR, textStatus, errorThrown) {
                       console.log("Error");
                   }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
                       // alert("complete");
                   });
               }
            }
            //optimize the transition to other page
            $('a').on('click',function () {
                clearInterval(intervalId);
            });
        });
    </script>

@endsection
