@extends('layout.layout')
@section('title','Add Employee')
@section('content')
    @if(session('message'))
        <div class="alert alert-success" style="text-align: center;">{{session('message')}}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="text-align: center;">{{session('error')}}
        </div>
    @endif
    <div>
        <style>
            .user_card {
                height: 205px;
                background-size: contain;
                margin: 0px;
                padding: 0px;
                display: flex;
                background-color: white;
                justify-content: center;
                padding-left: 20px;
                flex-direction: column;
                background-repeat: no-repeat;
                border-radius: 10px;
                background-position: right;
            }
        </style>
        @if($user)
            <div class="user_card mb-3">
                <div class="card-body bg-gradient-info ">
                    <p>User Id: {{ $user->id }}</p>
                    <p>User Name: {{ $user->name }}</p>
                    <p>Designation: {{ $user->title }}</p>
                    <p>Tags: {{ $user->tags }}</p>
                </div>
            </div>
        @endif
    </div>
    <div class="row m-t-3">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-title no-border">
                    <h4>Employee Registration Form</h4>
                </div>
                <div class="grid-body no-border">
                    <form method="POST" action="{{ route('allemployees_edit', $user->id) }}">
                        @method('PUT')
                        @csrf
                        <fieldset>
                            <legend><i class="fa fa-tags"></i> AI Search Tags</legend>
                            <div class="col-md-12">
                                <input type="text" name="tags" value="{{$user->tags}}" id="tagsInput" class="form-control" placeholder="Deputy Commissioners,DCs,Deputy Commissioner Mirpur,DC Mirpur">
                                <div id="tagsContainer" class="mt-2"></div>
                            </div>
                        </fieldset>
                        <div class="form-actions">
                            <div class="pull-right simple">
                                <button class="btn btn-danger btn-cons" id="basic_action_save" name="action" value="save" type="submit"><i class="icon-ok"></i> Save</button>
                                <button class="btn btn-success btn-cons" id="" name="action" value="save_next" ><i class="icon-ok"></i>Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add these links to your HTML file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


    <script>
        $(document).ready(function () {
            $('#tagsInput').on('input', function () {
                updateTagsDisplay();
            });

            function updateTagsDisplay() {
                var tagsInputValue = $('#tagsInput').val();
                var tagsArray = tagsInputValue.split(',');
                var tagsContainer = $('#tagsContainer');

                // Clear the container
                tagsContainer.empty();

                // Add tags to the container
                tagsArray.forEach(function (tag) {
                    tag = tag.trim();
                    if (tag !== '') {
                        tagsContainer.append('<span class="badge badge-primary mr-1">' + tag + '</span>');
                    }
                });
            }
        });
    </script>

@endsection

