@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href={{route('classes')}}>Classes</a></a>
@endsection
@section('action-buttons-space')
    <a class="btn modal-trigger" href="#join_class_modal">join a class</a>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">
    <style>.help-button {
            display: none;
        }</style>
    <div class="container classes-page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        @if($classes->total() > $classes->perPage())
                            {{ $classes->links() }}
                        @endif
                        <table class="classes-table striped">
                            <thead>
                            <tr>
                                <th class="class-th">Title</th>
                                <th class="quiz-th">Quizzes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($classes as $class)
                                <tr>
                                    <td class="class-td">
                                        @if($class->description != "")
                                            <span class="tooltipped"
                                               data-position="top"
                                               data-tooltip="{{$class->description}}">{{$class->name}}</span>
                                        @else
                                            <span>{{$class->name}}</span>
                                        @endif
                                    </td>

                                    <td class="quiz-td">{{$class->quizzes}}</td>
                                </tr>
                            @empty
                                <tr id="">
                                    <td style="text-align: center" colspan="2">You are not currently enrolled in any
                                        class
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                            @if($classes->total() > $classes->perPage())
                                {{ $classes->links() }}
                            @endif

                    </div>

                </div>
            </div>
        </div>
        <div id="join_class_modal" class="modal">
            <i class="material-icons right modal-close">close</i>

            <div class="modal-content">
                <div class="class-code-field-label">Insert the class code</div>
                <div class="input-field class-code-field">
                    <input class="class_code" name="class_code" value="">
                </div>
            </div>
            <div  class="modal-footer"> <a class="btn join-class-btn">join class</a></div>
        </div>
    </div>


    <script src="{{ asset('js/classes_participants.js',config('app.secure', null)) }}" defer></script>

@endsection
