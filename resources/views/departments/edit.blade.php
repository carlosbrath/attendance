    @extends('layout.layout')
    @section('title','Update Department')
    @section('content')
    <style type="text/css">
    .form-actions {
    background-color: #f6f7f8;
    border: 0px;
    margin-bottom: 20px;
    margin-top: 0px;
    padding: 19px 20px 9px;
    }
    .grid {
    clear: both;
    margin-top: 0px;
    margin-bottom: 0px;
    padding: 0px;
    }
    </style>        
    <div class="row">
        <div class="col-md-12">
            <div class="grid simple form-grid">
                <div class="grid-body no-border">
                    <form class="form-no-horizontal-spacing" method="post" 
                          action ="{{route('departments.update',$department->id)}}" id="form-condensed" novalidate="novalidate">     
                           {{method_field('PUT')}}
                           @csrf

                           
                        <div class="row column-seperation">
                            <div class="col-md-12">
                                <label class="form-label"><strong></strong></label>
                                <div class="radio">
                                        <input id="unit" type="radio" name="check_account" value="1" <?php echo ($department->sub_account==1)?'checked':''?>>
                                        <label for="unit">Unit</label>
                                        @if($check_subaccount->sub_account!=2)
                                        <input id="sub_account" type="radio" <?php echo ($department->sub_account==2)?'checked':''?>  name="check_account" value="2">
                                        <label for="sub_account">Sub Account</label>
                                        @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row form-row">
                                    <div class="col-md-8">
                                        <label><strong>Name</strong></label>
                                        <input name="name"  value="{{$department->name}}" Autocomplete="off" id="form3FirstName" type="text" class="form-control" placeholder="Enter Department Name">
                                    @if($errors->any())
                                     <p style="color:red">{{$errors->first('name')}}</p>
                                    @endif
                                    </div>

                                </div>
                                <div class="row form-row">
                                  
                                        <div class="col-md-8">
                                            <label><strong>Description</strong></label>
                                         <textarea id="text-editor" name="description" class="form-control" rows="10" placeholder="Enter text ..." style="width: 100%; height: 100px;">{{ $department->description}}
                                        </textarea>
                                            <!--<input name="form3LastName" id="form3LastName" type="text" class="form-control" placeholder="Last Name">-->
                                        </div>

                                   

</div>

</div>
</div>

</div>

</div>
<div class="form-actions">

    <div class="pull-left">
        <button class="btn btn-success btn-cons" name="action" value="save_and_close" ="save_and_close" type="submit"><i class="icon-ok"></i> Update</button>
       
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection