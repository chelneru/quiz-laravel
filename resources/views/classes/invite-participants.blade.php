@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('questions')}}">Classes</a>
    <a class="breadcrumb grey-text text-darken-4" href="/invite-participants">Invite participants</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Invite participants</div>
    <p>Here, you can invite participants in your classes. First, you will need to select the class in which you want to invite participants. Then you will have three methods to do so: <i>MANUAL</i>, <i>FILE IMPORT</i>, <i>CLASS CODE</i>.</p>
    <ol>
        <li><i>MANUAL</i>: You will need to add the participant(s) first name, last name, and email address. The participants will receive an email notifying them that they have been invited to participate in your class. The email to them will also include a link that will allow them to do so. If the participants that you want to  invite have a SAGA account already, then by clicking the link they will be automatically enrolled in your class. If they do not have a SAGA account, then by clicking the link, SAGA will ask them to register. After they register, they will be automatically enrolled in your class. </li>
        <li><i>FILE IMPORT</i>: You can use a .csv file (comma-separated values) to batch invite participants into your course. You can create a .csv file in Excel by simply writing the participants’ information (first name, last name, email) in the first three columns of an Excel sheet, without using any headers. In other words the A1 cell must include the first name of the participant you want to invite. After you click the <i>INVITE PARTICIPANTS</i> button, the participants will receive an email with the invitation and a link. After that, the same information mentioned in the <i>MANUAL</i> process is applied.
        </li>
        <li><i>CLASS CODE</i>: This is the easiest way to invite participants in your class. For each class created in SAGA, the system creates a unique identifier (i.e., a class code). Any participant with a SAGA account can enroll in your class by submitting the class code in the <i>JOIN A CLASS</i> menu. You can share this code with the participants in any platform (e.g., email, social media post, learning management system) by simply copying it there. </li>
    </ol>
@endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="/css/dropzone.min.css" rel="stylesheet">
    <script src="/js/libs/dropzone.min.js"></script>
    <div class="container invite-participants-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    @if($class_id == null)
                            <div class="input-field col s5">
                                <select id="class-select" name="class_id">
                                    <option value="" disabled selected>Choose a class</option>
                                    @foreach($classes as $class)
                                        <option class="black-text" value="{{$class->id}}">{{shortenedString($class->name,40)}}</option>

                                    @endforeach

                                </select>
                            </div>
                    @endif

                    <div class="panel-body">


                            <div class="row">
                                <div class="invite-operations-div" @if($class_id == null)style="display: none" @endif>
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s4 manual_invite_tab"><a class="active"
                                                                                    href="#manual_invite">Manual</a>
                                        </li>
                                        <li class="tab col s4 file_import_tab"><a href="#file_import_invite">File
                                                Import</a></li>
                                        <li class="tab col s4 code_tab"><a href="#code_invite">Class Code</a></li>
                                    </ul>
                                </div>
                                <div id="manual_invite" class="col s12">
                                    <div class="participants_rows_container">
                                        <div class="participant_row row">
                                            <div class="input-field col s3 input-counter-div">
                                                <input placeholder="Firstname" id="participant_first_name"
                                                       type="text" maxlength="255">
                                                <span class="input-counter">0/255</span>

                                            </div>
                                            <div class="input-field col s3 input-counter-div">
                                                <input placeholder="Lastname" id="participant_last_name"
                                                       type="text" maxlength="255">
                                                <span class="input-counter">0/255</span>

                                            </div>
                                            <div class="input-field col s3 input-counter-div">
                                                <input placeholder="Email@example.com" id="participant_email"
                                                       type="text" maxlength="190">
                                                <span class="input-counter">0/190</span>

                                            </div>
                                            <i class="material-icons delete-participant-row">close</i>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="btn-flat teal-text lighten-1 new-participant-button">
                                            <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="right"
                                               data-tooltip="Add new row">add</i>
                                        </div>

                                    </div>
                                </div>
                                <div id="file_import_invite" class="col s12">
                                    <form id="invite-participants-form" method="POST" class="dropzone"
                                          action="/upload-invites-participants-csv"
                                          style="@if($class_id == null){{'display:none'}}@endif">
                                        @csrf
                                        <input type="hidden" class="class-select" name="class_id" value="{{$class_id}}">
                                    </form>
                                </div>
                                <div id="code_invite" class="col s12">
                                    <div class="code-info-div">Share this code with your students and they can use it to enroll to this class.</div>

                                    <div class="code-div">{{$class_code}}</div>
                                </div>
                                </div>
                            </div>
                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn cancel-invite-participants-btn  grey noselect" href={{route('classes')}}>return to
                                classes page</a>
                            <button type="button" class="btn btn-success" id="btnUpload">invite participants</button>

                            <button class="btn invite-participants-btn right noselect">invite participants</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/classes.js',config('app.secure', null)) }}"></script>

@endsection
