<fieldset>
	<legend>@lang("Conversation")</legend>
	<label for="archive_all" class="check">
		<input type="checkbox" name="archive_all" id="archive_all">@lang("Archive all :count messages", ['count' => $node->block->messages->count()])
	</label>

</fieldset>