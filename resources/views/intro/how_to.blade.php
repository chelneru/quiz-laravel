@extends('layouts.landing-page-layout')

@section('tab-title') - How to use SAGA @endsection @section('content')
{{--    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">--}}
    <div class="container create-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <div class="header">Tasks/ functionalities</div>
                        <ul class="collapsible">
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Create account</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Delete account</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Create a class</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Invite student</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Create a quiz</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Create questions</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Create accompanying questions</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Monitor a quiz</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Schedule a quiz</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Export data</div>
                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

