<html>
<body>
<ul>
@foreach(App\Module::all() as $module)
	<li><a href="/lti/contentitem/{{$module->id}}/">{{$module->title}}</a></li>
@endforeach
</ul>
</body>