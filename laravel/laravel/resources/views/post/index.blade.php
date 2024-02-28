@extends('layouts.app')
@section('content')
<h1>Crud Resources</h1>
<table>
	<tr><th>Name</th><th>Name</th></tr>
 @foreach($post as $key => $value)
	

	<tr><td>{{ $value->title }}</td><td>{{ $value->body }}</td><td><a href="{{ route('post.show',$value->id)}}">View</a></td> <td>
{!! Form::open(['method'=>'DELETE','route'=>['post.destroy',$value->id],'style'=>'display-inline'])!!}
		<a href="{{route('post.edit',$value->id)}}">Edit</a></td>
		{!! Form::close() !!}}

	</tr>
@endforeach

</table>
@endsection


