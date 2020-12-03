@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Messages</h3>
                    </div>

                    <div class="card-body" ref="scrollParent">
                        <chat-messages :messages="messages"></chat-messages>
                    </div>
                    <div class="card-footer">
                        <chat-form
                            v-on:sent="addMessage"
                            v-on:clear="clearMessages"
                            :user="{{ Auth::user() }}"
                        ></chat-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection