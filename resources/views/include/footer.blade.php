 <script src="{{url('css/new_css/css/js/jquery-3.3.1.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<script src="{{url('css/new_css/css/js/jquery-printthis.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/dropzone/dropzone.min.js')}}" type="text/javascript"></script>


<script type="text/javascript">
    $('#indivi_rep_print').on('click',function(){
        $('#individual_report_print,#indivi_rep_profile,#indivi_summary').printThis();
    });
    $('#employees_print').on('click',function(){
        $('.all_emps').printThis();
    });

    $('#roster_report').on('click',function(){
        $('#printable_attendance').printThis();
    });

    $('#monthly_report_print').on('click',function(){

        $('.monthly_repor,.report_header').printThis({
            importCSS: true,
            importStyle: true,
            loadCSS: "{{url('template_files/print_style.css') }}"

        });
    });
</script>
<script src="{{url('css/new_css/css/js/jquery.dataTables.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/dataTables.buttons.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/buttons.flash.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/jszip.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/pdfmake.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/vfs_fonts.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/buttons.html5.min.js')}}" type="text/javascript"></script>
<script src="{{url('css/new_css/css/js/buttons.print.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var table=  $('#dataexample').DataTable( {
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 50, 100, 500, -1 ],
                [ '50 rows', '100 rows', '500 rows', 'Show all' ]
            ],

            buttons: [
                'pageLength',
                'copy', 'csv', 'excel', 'pdf',
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [ 0, ':visible' ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                        .css('text-align', 'center')
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<img src="" style="position:absolute; top:0; left:0;" />'
                            );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    }
                }
            ],

        });
        var table=  $('#dataexample_table1').DataTable( {
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 }
                ],
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 50, 100, 500, -1 ],
                [ '50 rows', '100 rows', '500 rows', 'Show all' ]
            ],

            buttons: [
                'pageLength',
                'copy', 'csv', 'excel', 'pdf',
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [ 0, ':visible' ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                        .css('text-align', 'center')
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<img src="" style="position:absolute; top:0; left:0;" />'
                            );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    }
                }
            ],

        });
        
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var table=  $('#monthlyreport').DataTable( {
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 }
            ],
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],

            buttons: [
                'pageLength',
                'copy', 'csv', 'excel', 'pdf',
            ],

        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#individual_report_datatable').DataTable({

            info: false,
            paging: false,
            sort: false,
            dom: 'fBrt',
            buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'print',
                footer: 'true',
                title: "Title of your print",
                autoPrint: 'false',
                customize: function (win) {
                    $(win.document.body)
                    .css('font-size', '6pt');

                    $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');
                }
            }
            ],
            scrollY: 525,
            scrollX: true
        })
    });
</script>

<script type="text/javascript">
    $('#id').blur(function(e){
        e.preventDefault();
        var user_id = $('#id').val();
        var url = "{{ route('users.checkid') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":user_id
            },
            success: function(result){
                if(result.id){
                    $('#user-text').text('ID: '+result.id+' exist in the Users please enter new one ! Thanks').show();
                    $('#user-save').hide();
                    $('#basic_action_save').hide();
                    $('#basic_action_save_next').hide();
                }else{
                    $('#user-save').show();
                    $('#user-text').hide();
                    $('#basic_action_save').show();
                    $('#basic_action_save_next').show()
                }
            }
        });
    });

    $('#btn_print').on('click', function(e){
        $("#print").printThis({
            debug: true
        });
    });


</script>
<script type="text/javascript">
    $(document).on("change","#department_id",function() {
        $('#preloader').show();

        var department_id=$(this).val();
        var url="{{route('fetch.emp-by-dep')}}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":department_id
            },
            success:function(response){  
                $('#preloader').hide();
                console.log(response);
                $('.hol_cal_users').children().remove(); 
                $.each(response, function (i,value) {
                    $('.hol_cal_users').append("<option value='"+value.id+"'>"+value.name+"</option>");
                });
                $('#preloader').hide();
            }
        });
    });

    $('.Depart').click(function(){

        if($(this).prop("checked") == true){
            $('#dp').show();
            $('#usr').hide();

        }
    });
    $('.user').click(function(){

        if($(this).prop("checked") == true){

            $('#dp').show();
            $('#usr').show();
        }
    });
    $('#overtime_check').click(function(){
        if($(this).prop("checked") == true){
            $('#overtime_show_field').show();

        }
        else{
            $('#overtime_show_field').hide();
        }

    });

    $('#unit').click(function(){

        if($(this).prop("checked") == true){
            $("#department_email").hide();
        }
    });
    $('#sub_account').click(function(){
        if($(this).prop("checked") == true){
            $("#department_email").show();
        }
    });


    $('#dep').click(function(){

        if($(this).prop("checked") == true){

            $('#dpr').show();
            $('#usrs').hide();
            $('#emp_select').hide();
            $('#dep_select').hide();
            $('#show_only_dep').show();

        }
    });
    $('#emp').click(function(){

        if($(this).prop("checked") == true){

            $('#emp_select').show();
            $('#dep_select').show();
            $('#show_only_dep').hide();
        }
    });
    $('#subdepartment_id').on('change', function() {

        if($(this).val()==''){
            $('.search').attr("disabled",false);
        }
        else{
            $('.search').attr("disabled","");
        }


    });
    $('.search').on('change', function() {
        if($(this).val()==''){
            $('#subdepartment_id').attr("disabled",false);
        }
        else{
            $('#subdepartment_id').attr("disabled","");
        }

    });

    $('#department_id').on('change', function() {
        if($(this).val()==''){
            $('#sub_department').attr("disabled",false);
        }
        else{
            $('#sub_department').attr("disabled","");
        }
    });
    $('#sub_department').on('change', function() {
        if($(this).val()==''){
            $('#department_id').attr("disabled",false);
        }
        else{
            $('#department_id').attr("disabled","");
        }
    });
    $('#leave_department_id').on('change', function() {

        $('#preloader').show();
        var id = $('#leave_department_id').val();

        var url = "{{ route('leave.report_employee') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){
                console.log(response);

                if(response.users==""){

                    $('#tcat_id_msg').append("Please fill Timcategory first");
                    $('#roster_form_action').hide();
                }
                $("#source1").empty();
                var value="";
                $("#tcat_dropdown").empty();
                $.each(response.users, function (i,value) {
                    $('#source1').append("<option value='"+value.id+"'>"+value.name+"</option>");
                    $('#roster_form_action').show();
                    $('#tcat_id_msg').text("");
                    $('#lev_msg').text("");

                });



                $('#preloader').hide();
            }
        });
    });



    $('input[name=attachment_status]').change(function(){
        var attachment_status_id = $( 'input[name=attachment_status]:checked').val();

        if(attachment_status_id == 1){
            $('#attach_to_date_block').hide();
            $('#attach_time_to').show();  
            $('#attach_time_from').show();
        }else{
            $('#attach_to_date_block').show(); 
            $('#attach_time_to').hide();  
            $('#attach_time_from').hide();     
        }
    });

    $('.leave').click(function(){

        if($(this).prop("checked") == true){

            $('#time_from').hide();
            $('#time_to').hide();
        }
    });
    $('.short_leve').click(function(){
        if($(this).prop("checked") == true){
            $('#time_from').show();
            $('#time_to').show();
        }
    });


    $('.checkbox_type_non_roster').click(function(){

        if($(this).prop("checked") == true){

            $('#time_category').hide();
            $('#emp_leave_config_panel').hide();
        }
    });

    $('.checkbox_type_roster').click(function(){

        if($(this).prop("checked") == true){
            $('#time_category').show();
            $('#emp_leave_config_panel').show();


        }
    });
    $('#select_by_main_dep').on('change', function() {
        var id = $('#select_by_main_dep').val();


        var url = "{{ route('users.fetch_dep') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){
                $("#sub_dep_unit").empty();
                $.each(response, function (i,value) {
                    $('#sub_dep_unit').append("<option value='"+value.id+"'>"+value.name+"</option>");
                });
            }

        });
    });

    $('#department_select').on('change', function() {

        var id = $('#department_select').val();
        var url = "{{ route('users.cat_name') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){
                console.log(response);

                var value="";
                $("#tcat_dropdown").empty();
                $.each(response.timecategory, function (i,value) {
                    $('#tcat_dropdown').append("<option value='"+value.id+"'>"+value.title+"</option>");
                    $('#roster_form_action').show();
                    $('#tcat_id_msg').text("");
                    $('#lev_msg').text("");

                });

                $('#preloader').hide();
            }
        });
    });

    $('.load_sub_departments').on('change', function() {

        $('#preloader').show();
        var id =$(this).val();
        var id =$(this).val()

        var url = "{{ route('sub_deparments') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "department_id":id
            },
            success:function(response){

                $("#sub_department_dropdown").empty();
                $.each(response, function (i,value) {
                    $('#sub_department_dropdown').append("<option  value="+value.id+">"+value.id+'-'+value.name+"</option>");
                });
                $('#preloader').hide();
            }
        });
    });
    $('#leave_deparment_id').on('change', function() {

        $('#preloader').show();
        var id = $('#leave_deparment_id').val();
        var url = "{{ route('leave_request.load_employees') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){


                if(response.employee==""){
                    $('#emp_msg').append("Please fill employee first");


                }
                var value="";
                $("#source").empty();
                $.each(response.employee, function (i,value) {

                    $('#source').append("<option  value="+value.id+">"+value.id+'-'+value.name+"</option>");
                });


                $('#preloader').hide();
            }
        });
    });
    $('#leave_nature').on('change', function() {
        var id = $('#leave_nature').val();
        var url="{{route('leave.leave_type')}}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id,

            },
            success:function(response){
                console.log(response);
                $("#leave").empty();
                var opt = '<option>Please Select...</option>'
                $.each(response.leave, function (i,value) {
                    opt+="<option  value="+value.id+">"+value.title+"</option>";
                });
                $("#leave").empty().append(opt);
            }
        });
    });


    $('.leave_apply_form_user_id').on('change', function() {
        $('#preloader').show();
        var id = $(this).val();
        var leave_id= $('#leave').val();
        $('#total_leave').val("");
        url="{{route('employee.fetch_total_leave')}}"
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id,
                "leave_id":leave_id
            },
            success:function(response){
                console.log(response);
                $('#total_leaves').val(response.total_leave);
                $('#availed_leaves').val(response.check_emp_leave);
                $('#preloader').hide();
            }
        });
    });

    $("#personal_file_no").change(function(){
        $('#department_form_action').show();
        var personal_file_no = $(this).val();
        $('#append_file_no_msg').empty();
        var url = "{{ route('emp.check_file_no') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "file_number":personal_file_no
            },
            success:function(response){
                $('#append_file_no_msg').append(response);
                $('#department_form_action').hide();
            }
        });
    });

    $('#department_id').on('change', function() {
        var id = $('#department_id').val();
        var url = "{{ route('leave.leave_name') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success: function(result){
                console.log(result);
                $.each(result, function(index) {
                    $('#leave_type').html('<option  value="'+result[index].id+'">'+result[index].title+'</option>').show();
                });

            }});
    });
    $('#indivi_department_id').on('change', function() {
        $('#preloader').show();
        var id = $('#indivi_department_id').val();
        var url = "{{ route('individual.fetch_users') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){

                console.log(response);

                $("#source1").empty();
                if(response.employee==""){
                    $('#emp_msg').append("Please fill employee first");
                }
                $.each(response.employee, function (i,value) {

                    $('#source1').append("<option  value="+value.id+">"+value.id+'-'+value.name+"</option>");
                });
                $('#preloader').hide();
            }



        });
    });
    $('#indivi_payrol_department_id').on('change', function() {
        $('#preloader').show();
        var id = $('#indivi_payrol_department_id').val();
        var url = "{{ route('individual_payroll.fetch_users') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){

                console.log(response);

                $("#source1").empty();
                if(response.employee==""){
                    $('#emp_msg').append("Please fill employee first");
                }
                $.each(response.employee, function (i,value) {

                    $('#source1').append("<option  value="+value.id+">"+value.id+'-'+value.name+"</option>");
                });
                $('#preloader').hide();
            }



        });
    });
    $('#bonus_department_id').on('change', function() {

        $('#preloader').show();
        var id = $('#bonus_department_id').val();
        var url = "{{ route('bonus.fetch_users') }}";
        jQuery.ajax({
            url:url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "id":id
            },
            success:function(response){



                $("#source1").empty();
                if(response.employee==""){
                    $('#emp_msg').append("Please fill employee first");
                }
                $.each(response.employee, function (i,value) {

                    $('#source1').append("<option  value="+value.id+">"+value.id+'-'+value.name+"</option>");
                });
                $('#preloader').hide();
            }



        });
    });

    $(document).ready(function() {
        $('#print_report').click(function(){
            $('#print_att_table_body').html($('#att_status_report_attendance_body').html());
            $('#at_status_report_summary, #print_att_table, #page_title,.report_header').printThis();
        });
    });

</script>

<script>
    $(document).ready(function (){
        $("#online_devices").dataTable({
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 }
                ],
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 20, 50, 100, 500, -1 ],
                [ '20 rows', '50 rows', '100 rows', '500 rows', 'Show all' ]
                ],

            buttons: [
                'pageLength',
                {
                    extend: 'excel',
                    title:'Online Devices',
                },
                {
                    extend: 'pdf',
                    title:'Online Devices',
                },
                {
                    extend: 'print',
                    title:'Online Devices',

                    exportOptions: {
                        columns: [ 0, ':visible' ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                        .css('text-align', 'center')
                        .css( 'font-size', '10pt' );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    }
                }
                ],
        });
        $("#ofline_devices").dataTable({
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 }
                ],
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 20, 50, 100, 500, -1 ],
                [ '20 rows', '50 rows', '100 rows', '500 rows', 'Show all' ]
                ],

            buttons: [
                'pageLength',
                {
                    extend: 'excel',
                    title:'Offline Devices',
                },
                {
                    extend: 'pdf',
                    title:'Offline Devices',
                },
                {
                    extend: 'print',
                    title:'Offline Devices',
                    
                    exportOptions: {
                        columns: [ 0, ':visible' ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                        .css('text-align', 'center')
                        .css( 'font-size', '10pt' );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    }
                }
                ],
        });

        $("#inactive_devices").dataTable({
            "columnDefs": [
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 },
                { "width": "1%", "targets": 0 }
                ],
            fixedColumns: true,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 20, 50, 100, 500, -1 ],
                [ '20 rows', '50 rows', '100 rows', '500 rows', 'Show all' ]
                ],

            buttons: [
                'pageLength',
                {
                    extend: 'excel',
                    title:'Inactive Devices',
                },
                {
                    extend: 'pdf',
                    title:'Inactive Devices',
                },
                {
                    extend: 'print',
                    title:'Inactive Devices',
                    
                    exportOptions: {
                        columns: [ 0, ':visible' ]
                    },
                    customize: function ( win ) {
                        $(win.document.body)
                        .css('text-align', 'center')
                        .css( 'font-size', '10pt' );

                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    }
                }
                ],
        });



        $('#preloader').hide();

        function updateLeaveData(){
            var leave_id = $(".leave_type_id").val();
            var emp_id = "{{Request::segment(3)}}";
            var CSRF_TOKEN = "{{ csrf_token() }}";

            $.ajax({
                url: '{{ route('leave.get_leaves') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    'leave_id': leave_id,
                    'emp_id': emp_id,
                },
                success: function (data) {
                    $(".number_days").val(data.total_leaves);
                }
            });
        }

        updateLeaveData();
        $('.leave_type_id').on("change", updateLeaveData);

        $("#selectAll").click(function () {
            $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
        });
        $('#main_department_id').on("change", function () {
            var CSRF_TOKEN = "{{ csrf_token() }}";
            var department_id = $(this).val();

            $.ajax({
                url: '{{ route('sub_deparments') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    'department_id': department_id
                },
                success: function (data) {
                    console.log(data);
                    var div_data = "<option value=''>Choose Department</option>";
                    $.each(data, function (i, obj) {
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('#sub_department_dropdown').html(div_data);
                }
            });
        });

        $('body').on('click', '.add_leave_row', function () {
            $('#leaves_holder').append($(this).parent().parent().parent().clone());
        });

        $('body').on('click', '.add_deduction_row', function () {
            $('#deduction_holder').append($(this).parent().parent().parent().clone());
        });

        $('body').on('click', '.add_allowances_row', function () {
            $('#allowance_holder').append($(this).parent().parent().parent().clone());
        });

        $("#contact_holder").on('click', '.remove_row', function (){
            $(this).closest('.d_contact_row').remove();
        });

        $("#suggest_unused_ids_btn").click(function (){
            var CSRF_TOKEN = "{{ csrf_token() }}";

            $.ajax({
                url: '{{ route('suggest_unused_ids') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    var div_data = "";
                    $.each(data, function (i, obj) {
                        div_data += "<tr><td>" + obj.MissingID + "</td><td><a style='float:right' class='btn btn-xs btn-success suggested_unused_id_btn' id='" + obj.MissingID + "'>Use</a></td></tr>";
                    });
                    $('#suggested_missing_ids_table_body').html(div_data);
                }
            });
        });

        $("#region_id").change(function (){
            var CSRF_TOKEN = "{{ csrf_token() }}";

            $.ajax({
                url: '{{ route('load_zone') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    'region_id': $(this).val()
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    var div_data = "<option>Please Select</option>";
                    $.each(data, function (i, obj) {
                        div_data += "<option value='" + obj.zone_id + "'>" + obj.zone_name + "</option>";
                    });
                    $('#r_zone_id').html(div_data);
                }
            });
        });

        $("#r_zone_id").change(function () {
            var CSRF_TOKEN = "{{ csrf_token() }}";

            $.ajax({
                url: '{{ route('load_branch') }}',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    'zone_id': $(this).val()
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    var div_data = "<option>Please Select</option>";
                    $.each(data, function (i, obj) {
                        div_data += "<option value='" + obj.branch_id + "'>" + obj.branch_name + "</option>";
                    });
                    $('#z_branch_id').html(div_data);
                }
            });
        });

        $('body').on('click', '.suggested_unused_id_btn', function () {
            $('#id').val($(this).attr('id'));
            $('#suggested_ids_modal').modal('toggle');
        });
    });
</script>
@stack('scripts')

<script src="{{url('template_files/theme/assets/plugins/bootstrapv3/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-block-ui/jqueryblockui.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-unveil/jquery.unveil.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js')}}" type="text/javascript"></script>

<script src="{{url('template_files/theme/assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{url('template_files/theme/webarch/js/webarch.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/js/chat.js')}}" type="text/javascript"></script>

<script src="{{url('template_files/theme/assets/plugins/jquery-datatable/js/jquery.dataTables.min.js')}}" type="text/javascript"></script>  <!-- datatable -->
<script src="{{url('template_files/theme/assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js')}}" type="text/javascript"></script>  <!-- datatable -->
<script type="text/javascript" src="{{url('template_files/theme/assets/plugins/datatables-responsive/js/datatables.responsive.js')}}"></script> <!-- datatable -->
<script type="text/javascript" src="{{url('template_files/theme/assets/plugins/datatables-responsive/js/lodash.min.js')}}"></script><!-- datatable -->
<script src="{{url('template_files/theme/assets/js/datatables.js')}}" type="text/javascript"></script><!-- datatable -->

<script src="{{url('template_files/theme/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/jquery-autonumeric/autoNumeric.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/ios-switch/ios7-switch.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-select2/select2.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js')}}" type="text/javascript"></script>
<script src="{{url('template_files/theme/assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js')}}" type="text/javascript"></script>

<script src="{{url('template_files/theme/assets/js/form_elements.js')}}" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script>
    $(".mydateformat").on("change", function() {
        this.setAttribute(
            "data-date",
            moment(this.value, "YYYY-MM-DD")
                .format( this.getAttribute("data-date-format") )
        )
    }).trigger("change")
</script>