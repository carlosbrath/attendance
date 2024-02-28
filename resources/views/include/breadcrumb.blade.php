<div class="page-title listing-page" style="justify-content: space-between;margin-bottom: 4px;">
    <h3 style="margin-bottom: 0px;width:90% !important;">
        <a style="text-transform: uppercase;" href="@if(\Session::get('role_id')==1)
    {{url('super-admin')}}
@elseif(\Session::get('role_id')==2)
    {{url('dashboard-manager')}}
@else
    {{url('dashboard')}}
@endif" class="active"><i style="font-size:20px;" class="icon-custom-left"></i> Dashboard</a> -
        <span class="semi-bold">{{ $page_title ?? 'No Page Title Choosen' }}</span>
    </h3>
    @if(\Session::get('role_id') == 1)
        <a class="btn btn-primary btn-mini" style="float:right;" href="{{route('sync-attendance')}}"> Sync Attendance</a>
    @endif
</div>    

