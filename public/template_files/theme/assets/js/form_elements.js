/* Webarch Admin Dashboard 
/* This JS is only for DEMO Purposes - Extract the code that you need
-----------------------------------------------------------------*/	
//Cool ios7 switch - Beta version
//Done using pure Javascript
$("#department_id").select2();
$(".employee_deparments").select2();
$("#tcat_dropdown").select2();
$("#user_edit_timecategory").select2();
$("#designation_id").select2();
$("#branch_id").select2();
$("#role_id").select2();
$("#contract_type").select2();
$("#leave_deparment_id").select2();
$("#zone_id").select2();
$(".region").select2();
$("#tcat_id").select2();
$("#leave").select2();
$("#religion").select2();
$("#indivi_department_id").select2();
$("#source1").select2();
$("#roster_department_id").select2();
$("#edit_department_select").select2();
$("#tcat_edit_department").select2();
$("#edit_roster_type").select2();
$("#device_department_id").select2();
$("#tcat_id").select2();
$("#roster_department_id").select2();
$("#user_edit_department").select2();
$("#user_timecategory").select2();
$("#leave_fix_var").select2();
$(".sub_department").select2();
$(".select_by_main_dep").select2();
$(".department_id").select2();
$("#units").select2();
$("#department_id").select2();




if(!$('html').hasClass('lte9')) { 
var Switch = require('ios7-switch')
        , checkbox = document.querySelector('.ios')
        , mySwitch = new Switch(checkbox);
 mySwitch.toggle();
      mySwitch.el.addEventListener('click', function(e){
        e.preventDefault();
        mySwitch.toggle();
      }, false);
//creating multiple instances
var Switch2 = require('ios7-switch')
        , checkbox = document.querySelector('.iosblue')
        , mySwitch2 = new Switch2(checkbox);

      mySwitch2.el.addEventListener('click', function(e){
        e.preventDefault();
        mySwitch2.toggle();
      }, false);

}

$(document).ready(function(){

	  //Dropdown menu - select2 plug-in
	  $("#source").select2();

	  //Multiselect - Select2 plug-in
	  $("#multi").val(["Jim","Lucy"]).select2();
  
	  //Date Pickers
	  $('.input-append.date').removeClass('hasDatepicker');
	  $('.input-append.date').datepicker({
				autoclose: true,
				todayHighlight: true,
				format: "yyyy/mm/dd"
	   }).on("change", function() {
	   	       var dob= $('#date_of_birth').val();
				dob = new Date(dob);
				var today = new Date();
				var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
				$('#append_age').html(age+' years old');
				$('#age_hidden').val(age);
       });
	  $("#datepicker").datepicker({
		    format: "yyyy-mm",
		    viewMode: "months", 
		    minViewMode: "months"
       });
	   
	 //$('#dp5').datepicker(format: 'yyyy-mm-dd' );
	  $('#dp5').datepicker({
             format: 'yyyy-mm',
             viewMode:'months',
             miniViewMode:'months', 
              

        });
	 $('#sandbox-advance').datepicker({
			format: "yyyy-mm-dd",
			startView: 1,
			daysOfWeekDisabled: "3,4",
			autoclose: true,
			todayHighlight: true
    });
	
	//Time pickers
	$('.clockpicker ').clockpicker({
        autoclose: true
    });
	//Color pickers
	$('.my-colorpicker-control').colorpicker()
	
	//Input mask - Input helper
	$(function($){
	   $("#date").mask("99/99/9999");
	   $("#phone").mask("(999) 999-9999");
	   $("#tin").mask("99999-9999999-9");
	    $("#mob").mask("9999-9999999");
	    $("#mob2").mask("9999-9999999");
	   $("#ssn").mask("999-99-9999");
	});
	//Autonumeric plug-in - automatic addition of dollar signs,etc controlled by tag attributes
	$('.auto').autoNumeric('init');
	//HTML5 editor
	$('#text-editor').wysihtml5();
	//Drag n Drop up-loader
	$("div#myId").dropzone({ url: "/file/post" });
	
	//Single instance of tag inputs  -  can be initiated with simply using data-role="tagsinput" attribute in any input field
	$('#source-tags').tagsinput({
		typeahead: {
			source: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo']
		}	
	});
});