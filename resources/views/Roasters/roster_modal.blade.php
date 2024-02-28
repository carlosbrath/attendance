
<style type="text/css">
 .btn-cons {
        margin-right: 5px;
       min-width: 120px;
       margin-bottom: 0px;
   }
</style>
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">  
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	 <span class="modal-title" id="exampleModalLabel">Roster Details</span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="row form-row">
                <div class="col-md-6">
                  <div class="form-group">
                      <label class="form-label"><strong>Time Category/Leave</strong></label>
                      <div class=" right">
                          <i class=""></i>
                          <select name="tcat_id" id="tcat_id"   class= "select2 form-control tcat_id">
                              <option value="">
                              Please Select...
                              </option>
                          <?php  foreach($timecategory as $tcat) { ?>
                          <option value="<?php echo $tcat->id?>"><?php echo $tcat->title?></option>
                          <?php    } ?>                            
                          </select>
                          @if($errors->any())
                          <p style="color:red">{{$errors->first('tcat_id')}}</p>
                          @endif       
                       </div>
                  </div> 
                </div>
              </div>
              <div class="row form-row">
              <div class="col-md-6">
              <label class="form-label"></label>
                  <div class="input-append success date col-md-10 col-lg-6 no-padding" style="width:243px">
                    <input type="text" autocomplete="off" name="from_date" placeholder="From Date" class="form-control from_date">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                       @if($errors->any())
                       <p style="color:red">{{$errors->first('from_date')}}</p>
                     @endif
                  </div>
                </div>
              </div>
              <div class="row form-row">
              <div class="col-md-6">
                   <label class="form-label"></label>
                  <div class="input-append success date col-md-10 col-lg-6 no-padding" style="width:243px">
                    <input type="text" autocomplete="off" name="to_date" id="dp3" placeholder="To Date" class="form-control from_date">
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                     @if($errors->any())
                       <p style="color:red">{{$errors->first('to_date')}}</p>
                     @endif
                  </div>
                </div>
              </div>
        <input type="hidden" id="roster_id"   value="{{Request::segment(2)}}">
       <input type="hidden"  id="date" value="">
       <input type="hidden"  id="user_id" value="">
      </div>
      <div class="modal-footer">
        <button id="submit" type="button" class="btn btn-success btn-cons" data-dismiss="modal">Submit</button>
        <button type="button" class="btn btn-danger btn-cons">Close</button>
      </div>
    </div>
  </div>
</div>