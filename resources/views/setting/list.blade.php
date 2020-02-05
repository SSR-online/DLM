@extends('layouts.app')

@section('title')
@lang("Settings")
@endsection

@section('sidebar')
	@foreach($nav as $item)
		<a href="/settings/{{$item['name']}}">{{$item['text']}}</a>
	@endforeach
	</nav>
@endsection

@section('content')

@if(isset($configuration))
<form action="/setting/update/{{$blockname}}" method="post">
	{{csrf_field() }}
<input type="hidden" name="block" value="{{$blockname}}" />
@foreach($configuration as $key=>$conf)
	<label for="{{$key}}">{{$conf['friendly_name']}}
		@if(is_array($conf['default']))
		<textarea name="{{$key}}" id="{{$key}}">@if(isset($conf['value']))
@foreach($conf['value'] as $value){{$value}}
@endforeach
			@else
@foreach($conf['default'] as $value){{$value}}
@endforeach
			@endif
		</textarea>
		@elseif(is_bool($conf['default']))
			<input type="checkbox" name="{{$key}}" id="{{$key}}" @if(isset($conf['value']) && $conf['value'] == true || !isset($conf['value']) && $conf['default']== true)checked="checked"@endif>
		@else
			<input type="text" name="{{$key}}" id="{{$key}}" value="{{$conf['value'] or $conf['default']}}">
		@endif
	</label>
@endforeach
	<input type="submit" value="@lang('Save')" />
</form>
@else
@foreach($settings as $setting) 
	<label for="setting-{{$setting->name}}">{{$setting->name}}
		<input type="text" id="setting-{{$setting->name}}" name="{{$setting->name}}" value="{{$setting->value}}">
	</label>
@endforeach
<a href="/setting/create/">@lang("Add setting")</a>

@endif
@endsection