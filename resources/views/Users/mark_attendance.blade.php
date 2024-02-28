    @extends('layout.layout')
    @section('title','Mark Attendance')
    @section('content')
         
    <div class="row">
       <div class="col-md-12">
              <div class="grid simple form-grid">
                     <div class="grid-body no-border">
                      <br><br>
                           <form action="{{route('markattendance.store')}}" method="post">
                          @csrf
     
          <div class="form-group  col-md-6">
            <input type="hidden"  value="{{Request::segment(3) }}" name="id">
            <label for="recipient-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" name="" value="{{$user->name}}" readonly="" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Timing:</label>
             <div class="controls">
              <div class="input-group col-md-6">
                <input type="datetime-local" name="time_in"  autocomplete="Off" class="form-control" placeholder="Time In">
                
                </div>
                @if($errors->any())
                <p style="color:red">{{$errors->first('time_in')}}</p>
                @endif
              </div>
             
            </div>
            <div class="form-group">

            </div>
            <div style="float: right;">
       
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
        </form>
</div>
</div>
</div>
</div>
@endsection