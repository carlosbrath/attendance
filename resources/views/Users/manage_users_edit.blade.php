
@extends('layout.layout')
@section('title','Create New User')
@section('content')
    @if(session('message'))
        <div class="alert alert-success" style="text-align: center;">{{session('message')}}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post"  action ="{{route('manage_users_update', $user->id)}}" id="form-condensed">
                        @method('PUT')
                        {{csrf_field()}}
                        <div class="row column-seperation">
                            <div class="row form-row">
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Name</strong></label>
                                    <input name="name" value="{{$user->name}}" type="text" class="form-control" placeholder="Enter Name" required>
                                    @if($errors->any())
                                        <p style="color:red">{{$errors->first('name')}}</p>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Email</strong></label>
                                    <input name="email" value="{{$user->email}}" type="email" class="form-control" placeholder="Enter Email" required>
                                    @if($errors->any())
                                        <p style="color:red">{{$errors->first('email')}}</p>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Password</strong></label>
                                    <input name="password" value="{{$user->password}}" type="password" class="form-control" placeholder="Enter Password" required>
                                    @if($errors->any())
                                        <p style="color:red">{{$errors->first('password')}}</p>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                    <select required name="role_id">
                                        <option value="">--Select Role--</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}" @if($user->role_id == $role->id) selected @endif>
                                                {{$role->title}}
                                            </option>
                                        @endforeach
                                    </select>

                                @if($errors->any())
                                        <p style="color:red">{{$errors->first('password')}}</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="pull-left">
                            <button class="btn btn-danger btn-cons" name="action"  type="submit"><i class="icon-ok"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
