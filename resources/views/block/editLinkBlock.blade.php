<fieldset>
	<label>@lang("Link to")
		<select name="target_id">
			<option name="-1">@lang("Select…")</option>
			@include('node.option', ['selected'=> $node->block->targetNode, 'nodes' => $node->module->nodesByParent(), 'node' => $node, 'id' => '', 'prefix' => '', 'canselect' => true])
		</select>
	</label>
	<label @if($errors->has('url')) class="error" @endif>@lang("Or url"):
		<input type="url" name="url" value="{{ $node->block->url }}">
		@if($errors->has('url')) {{ $errors->first('url') }} @endif
	</label>
	<label>
		@lang("Display"):
		<select name="display">
			<option @if($node->block->setting('display') == 'menu') selected="selected" @endif value="menu">@lang("as menu")</option>
			<option @if($node->block->setting('display') == 'list') selected="selected" @endif value="list">@lang("as list")</option>
			<option @if($node->block->setting('display') == 'jump') selected="selected" @endif value="jump">@lang("as navigation option")</option>
			<option @if($node->block->setting('display') == 'inline') selected="selected" @endif value="inline">@lang("in page (not all links can be embedded in the page")</option>
		</select>
	</label>
</fieldset>