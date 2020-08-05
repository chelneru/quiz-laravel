@extends('layouts.landing-page-layout')

@section('tab-title') - Introduction @endsection @section('content')
                                                     <style>
                                                         .highlight {
                                                             color: #26a69a;
                                                         }
                                                         .title-text {
                                                             display: inline-block;
                                                             font-size: 4vh;
                                                             width: 70%;
                                                             vertical-align: middle;
                                                         }
                                                         .collapsible-header {
                                                             color: #26a69a;
                                                         }
                                                     </style>
{{--    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">--}}
    <div class="container create-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                            <div class="header" style="text-align: center;"><img src="/images/Logo.png" style="display: inline-block;vertical-align: middle"><span class="title-text">Welcome to the Self-Assessment/Group Awareness (SAGA) tool!</span></div>
                        SAGA is an <span class="highlight">audience response system</span> in which you can create your own <span class="highlight">multiple-choice quizzes</span> or participate in quizzes that other users have created.
                        Each quiz in SAGA has <span class="highlight">two phases</span>. In the <span class="highlight">initial phase</span>, the students first answer the quiz questions, then the tool shows to them <span class="highlight">aggregated feedback</span> based on all the answers in the class, and in the <span class="highlight">revision phase</span>, the participants have the opportunity to revise their initial answers based on the feedback they received.
                        <br>SAGA uses several <span class="highlight">feedback metrics</span> to depict classroom performance and support self-assessment and group awareness. Apart from the percentage each question choice received (e.g., “A: 24%; B: 54%; C: 14%; D: 8%”), SAGA uses additional feedback metrics that allow for a better understanding of the people behind each question choice (e.g., students’ self-perceived <span class="highlight">preparedness</span> and <span class="highlight">confidence</span>, short open-text <span class="highlight">justifications</span>). The number and type of feedback metrics in a quiz can be easily modified by the quiz creator.
                        <img style="width: 100%;padding: 20px 0;" src="/images/home-graph.jpg">
                        SAGA is primarily a <span class="highlight">research tool</span> that has been used effectively in different instructional scenarios. It is also a tool that keeps evolving with new versions and functionalities replacing older ones. If you plan to try it yourself and use it for research or teaching, it is a good idea to <span class="highlight">let us know</span> of your intentions so that we can make sure that everything will be working fine for you.
                        For more information on SAGA and for inquiries for research collaborations, please contact <span class="highlight">Dr Papadopoulos (saga [dot] edu [dot] pi [at] gmail [dot] com)</span>.
                        <ul class="collapsible">
                            <li> <div class="collapsible-header"><i class="material-icons">add</i>Who can use it?</div>
                                <div class="collapsible-body"><span>SAGA is available for everyone, free of cost. The users are solely response for the material they upload. In general, uploaded material has to be in line with the legal and ethics guidelines of the University of Twente (be nice!) and inappropriate material will be deleted without notice.</span></div>
                            </li> <li><div class="collapsible-header"><i class="material-icons">add</i>What about GDPR?</div>
                                <div class="collapsible-body"><span>The platform is GDPR-compliant. All user information is stored on a server owned and managed by the University of Twente. User information is encrypted, password protected, and not shared in any way or form outside SAGA. In addition, since SAGA was designed primarily for self-assessment, users’ performance in quizzes is completely anonymized (also to the quiz-creator). SAGA allows you to create “assessment quizzes” in which the participants’ usernames will be visible in the final score report and, as always, the quiz-creator will be responsible for acquiring the appropriate consent from the quiz participants before the quiz starts. Finally, users can delete all their information from the system at any given moment.</span></div></li>
                            <li><div class="collapsible-header"><i class="material-icons">add</i>Who made it?</div>
                                <div class="collapsible-body"><span>The very first version of SAGA was developed at Aarhus University, Denmark and funded by the Aarhus University Research Foundation (AUFF) through the “Innovative &amp; Emerging Technologies in Education – I&amp;ETE” Starting Grant. Pantelis M. Papadopoulos, PhD was the recipient of the grant and he is to this
day the Principal Investigator behind SAGA. The current version of the tool was developed by Alin Panainte, MSc. Since April 2020, Dr Papadopoulos and SAGA have moved to the University of Twente, Netherlands.</span></div>
                            </li></ul>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

