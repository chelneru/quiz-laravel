@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="/remove-account">&nbsp;&nbsp;Delete account</a>
@endsection
<style>.help-button {
        display: none !important;
    }</style>
@section('content')
    <link href="{{ asset('css/profile.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container delete-profile-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">

                    <div class="panel-body">
                        <div class="panel-row delete-row">
                            <div class="panel-label">
                                You can delete your Account at any time. If you change your mind, you not be able to recover it. Upon deletion, all information related to this account will be erased.
                            </div>

                        </div>
                    </div>
                    <div class="panel-footer">

                        <div class="panel-row">

                            <a href="/home">
                                <button type="button" class="btn grey">
                                    @lang('profile.back-button')
                                </button>
                            </a>
                            <button type="button" class="btn delete-button modal-trigger" href="#delete-account-modal">
                                @lang('profile.delete-account')
                            </button>
                        </div>
                    </div>
                </div>
        </div>
        @include('user.confirm-delete-account')

    </div>
    <script src="{{ asset('js/profile.js',config('app.secure', null)) }}" defer></script>

@endsection
