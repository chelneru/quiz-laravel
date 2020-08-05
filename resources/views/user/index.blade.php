@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="/participants">Participants</a></a>
@endsection
@section('action-buttons-space')
    <a class="btn" href="/class/invite-participants ">add participants</a>
@endsection
@section('tab-title') - Participants @endsection
@section('help-page')
    <div class="help-modal-header">Participants</div>
    <p>Here, you can find the information of all the participants of all your classes. You can use the filters on top to narrow down your view.</p>
    <p>You can dismiss a participant by clicking on the Options icon on the participant’s row. This will unenroll the participant from your class.Upon dismissal existing responses of the participant will have an anonymous author. It will not delete the Participant from the system. Since a participant may be enrolled in several of your classes, you can choose which enrollment to cancel. Dismissing a participant from one class will not affect this participant’s activity in the other classes.</p>
    <p>You can add a new participant in one of your classes by clicking on the <i>ADD PARTICIPANTS</i> button.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/users.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container participants-page" data-participants="{{json_encode($participants_autocomplete)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        @if ($participants->total() > $participants->perPage())
                            {{ $participants->links() }}
                        @endif
                        <div class="input-field col s3">
                            <select id="role-dropdown">
                                <option value="" @if($role_filter === null){{'selected'}}@endif>All roles</option>
                                <option @if($role_filter == 1){{'selected'}}@endif value="1">Participants</option>
                                <option @if($role_filter == 2){{'selected'}}@endif value="2">Teachers</option>
                            </select>
                            <label>Role filter</label>
                        </div>
                        <div class="input-field col s3 class-filter-field">
                            <select id="class-dropdown">
                                <option value="" @if($class_filter === null){{'selected'}}@endif>All classes</option>
                                @foreach($classes as $class)
                                    <option
                                        value="{{$class->id}}" @if($class_filter == $class->id){{'selected'}}@endif >{{shortenedString($class->name,30)}}</option>
                                @endforeach
                            </select>
                            <label>Class filter</label>
                        </div>
                            <div class="input-field col s3 autocomplete-participant">
                                <input type="text" id="autocomplete-participant-input" class="autocomplete" value="@if($participant_filter !== null){{$participant_filter}}@endif">
                                <label for="autocomplete-participant-input">Participant</label>
                            </div>

                        <table class="participants-table striped responsive-table">
                            <thead>
                            <tr>
                                <th class="name" data-function="@if($order_by_filter =='name'){{'sort-'.$order_dir_filter.'-name'}}@else{{'sort-asc-name'}}@endif">
                                    Name
                                <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='name' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='name' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span>
                                </th>
                                <th class="email" data-function="@if($order_by_filter =='email'){{'sort-'.$order_dir_filter.'-email'}}@else{{'sort-asc-email'}}@endif">
                                    Email
                                <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='email' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='email' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                                <th class="class" data-function="@if($order_by_filter =='class_names'){{'sort-'.$order_dir_filter.'-class'}}@else{{'sort-asc-class'}}@endif">
                                    Class
                                <span class="sort-group">
                                    <i class="material-icons asc @if($order_by_filter =='class_names' && $order_dir_filter =="asc"){{'selected'}}@endif">arrow_drop_up</i>
                                    <i class="material-icons desc @if($order_by_filter =='class_names' && $order_dir_filter =="desc"){{'selected'}}@endif">arrow_drop_down</i>
                                </span></th>
                              <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($participants as $participant)
                                <tr id="{{$participant->id}}" role="{{$participant->role}}">
                                    <td class="name-td">{{shortenedString($participant->name,25)}}</td>
                                    <td>{{shortenedString($participant->email,40)}}</td>
                                    <td class="class-td"
                                        data-class-ids="{{json_encode($participant->class_ids)}}" data-class-names="{{json_encode($participant->class_names)}}">{{shortenedString($participant->class_names,70)}}</td>
                                    <td class="right-align">
                                    @if($participant->id != Auth::user()->u_id)
                                        <!-- Dropdown Trigger -->
                                            <a class='dropdown-trigger black-text' href='#'
                                               data-target='dropdown{{$loop->index+2}}'>
                                                <i class="material-icons options-icon">more_vert</i>

                                            </a>
                                            <!-- Dropdown Structure -->
                                            <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                                <li class="inactive-button"><a class="modal-trigger"
                                                                               href="#dismiss-participant-modal">Dismiss
                                                        participant</a></li>
                                            </ul>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($participants->total() > $participants->perPage())

                            {{ $participants->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form  id="query-participants-form" method="get" action="/participants">
        @if($role_filter !== null)
        <input type="hidden" name="role_filter" value="{{$role_filter}}">
        @endif
            @if($class_filter !== null)
        <input type="hidden" name="class_filter" value="{{$class_filter}}">
            @endif
            @if($order_by_filter !== null)
        <input type="hidden" name="order_by_filter" value="{{$order_by_filter}}">
            @endif
            @if($order_dir_filter !== null)
        <input type="hidden" name="order_dir_filter" value="{{$order_dir_filter}}">
            @endif
            @if($participant_filter !== null)
                <input type="hidden" name="participant_filter" value="{{$participant_filter}}">
            @endif

    </form>
    @include('user.confirm-dismiss-participant')
    <script src="{{ asset('js/users.js',config('app.secure', null)) }}" defer></script>

@endsection
