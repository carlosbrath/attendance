<h3>{{ $client_name }}</h3>
<b>Daily Summary Report</b>
<table cellpadding="0" cellspacing="0" width="640" border="1">
	<tr>
		<th>All Employees</th><td>{{ $total }}</td>
	</tr>
	<tr>
		<th>Present</th><td>{{ $present }}</td>
	</tr>
	<tr>
		<th>Late</th><td>{{ $late }}</td>
	</tr>
	<tr>
		<th>Absent</th><td>{{ $absent }}</td>
	</tr>
	<tr>
		<th>Leave</th><td>{{ $leave }}</td>
	</tr>
	<tr>
		<th>Attach</th><td>{{ $attach }}</td>
	</tr>
</table>