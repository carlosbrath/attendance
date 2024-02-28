<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<form method="put" action="<?php echo e(route('post.update',$post->id)); ?>">
	<?php echo e(csrf_field()); ?>

	<label>Title</label>
	<input type="text" name="title" value="<?php echo e($post->title); ?>">
     <label>Body</label>
	<input type="text" name="body" value="<?php echo e($post->body); ?>">
	<input type="submit" value="Save">

</form>

</body>
</html>