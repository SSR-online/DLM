<fieldset>
	<legend>@lang("Image")</legend>
	<label>@lang("Upload"):
		<input type="file" name="image" />
	</label>
	<label>@lang("Select"):</label>
	@foreach(Storage::disk('public')->files('images/' . $node->module->id) as $image)
		<input class="img" type="radio" name="image" value="{{$image}}" style="background-image: url(/storage/{{$image}});" @if($node->block->path == $image) checked="checked" @endif />
	@endforeach
<fieldset>
	<label>
		@lang("Alternative text (describe the image)"):
		<input type="text" name="alt" value="{{$node->block->alt}}" />
	</label>
	<label>
		@lang("Description (optional longer description, for instance for diagrams)")
		<textarea name="longdesc">{{$node->block->longdesc}}</textarea>
	</label>
	<label>
		@lang("Display"):
		<select name="display">
			<option value="hero" @if($node->setting('display')=="hero")selected="selected"@endif>@lang("Full width")</option>
			<option value="lightbox" @if($node->setting('display')=="lightbox")selected="selected"@endif>@lang("Popup")</option>
			{{-- <option value="aside">Naast tekstblok</option> --}}
			{{-- <option value="inset">In tekstblok</option> --}}
		</select>
	</label>
</fieldset>