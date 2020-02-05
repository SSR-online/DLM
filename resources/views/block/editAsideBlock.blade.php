<fieldset>
	<label>@lang("Content"):
		<textarea class="edit" name="content">{{$node->block->content}}</textarea>
	</label>
	<label>
		@lang("Belongs to (link ID)"):
		<input type="text" name="html_id" value="{{$node->block->html_id}}" />
	</label>
</fieldset>