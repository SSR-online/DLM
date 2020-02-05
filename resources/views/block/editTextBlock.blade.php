<fieldset>
	<label>@lang('Content'):
		<textarea class="edit" name="content">{{$node->block->content}}</textarea>
	</label>

	<label>@lang('Display as'):
		<select name="display_as">
			<option value="-1">@lang("Select…")</option>
			<option @if($node->block->setting('display_as') == 'intro') selected="selected" @endif value="intro">@lang("Introduction")</option>
			<option @if($node->block->setting('display_as') == 'goals') selected="selected" @endif 
				value="goals">@lang("Learning objectives")</option>
			<option @if($node->block->setting('display_as') == 'tip') selected="selected" @endif value="tip">@lang("Tip")</option>
			<option @if($node->block->setting('display_as') == 'highlight') selected="selected" @endif value="highlight">@lang("Highlight")</option>
			<option @if($node->block->setting('display_as') == 'sources') selected="selected" @endif value="sources">@lang("Sources")</option>
			<option @if($node->block->setting('display_as') == 'duration') selected="selected" @endif value="duration">@lang("Duration")</option>
		</select>
	</label>

	<label>
		class-list:
		<input type="text" name="classlist" value="{{ $node->block->setting('class_list') }}" />
	</label>
</fieldset>
@if($node->children)
<fieldset>
	<legend>@lang("Asides")</legend>
	<ul>
	@foreach($node->children as $child_node) 
		<aside><h4>#{{$child_node->block->html_id}}</h4>
		{!!str_limit($child_node->block->content, 30)!!}
		<a href="/node/edit/{{$child_node->id}}">@lang("Edit")</a>
		</aside>
	@endforeach
	</ul>
</fieldset>
@endif