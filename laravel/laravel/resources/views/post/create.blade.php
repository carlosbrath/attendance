<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<form method="post" action="{{route('post.store')}}">
{{csrf_field()}}
	<label>Title</label>
	<input type="text" name="title" value="">
     <label>Body</label>
	<input type="text" name="body" value="">
	<input type="submit" value="Save">

</form>

</body>
</html>