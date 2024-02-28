<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<form method="put" action="{{route('post.update',$post->id)}}">
	{{csrf_field()}}
	<label>Title</label>
	<input type="text" name="title" value="{{ $post->title }}">
     <label>Body</label>
	<input type="text" name="body" value="{{ $post->body }}">
	<input type="submit" value="Save">

</form>

</body>
</html>