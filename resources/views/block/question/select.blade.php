<h3>@lang("Question type")</h3>
<div class="add_options">
	@foreach($block->allowed_types as $key=>$value)
		<input class="add_section" type="submit" name="set_type" value="@lang($value)" />
	@endforeach
</div>