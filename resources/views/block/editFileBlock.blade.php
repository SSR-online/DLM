<fieldset>
	<legend>@lang("File")</legend>
	<label>@lang("Upload"):
		<input type="file" name="file" />
	</label>
	<label>@lang("Select"):</label>
	@foreach(Storage::disk('public')->files('files/' . $node->module->id) as $file)
		<label><input class="file" type="radio" name="file" value="{{$file}}" @if($node->block->path == $file) checked="checked" @endif /><a href="/storage/{{ $file }}">{{ $file }}</a></label>
	@endforeach
	<label>@lang("Display"):</label>
	<select name="display">
		<option @if($node->block->setting('display') == 'download') selected="selected" @endif value="download">@lang("Download")</option>
		<option @if($node->block->setting('display') == 'inline') selected="selected" @endif value="inline">@lang("Show in page (PDF-files only)")</option>
	</select>
	
</fieldset>