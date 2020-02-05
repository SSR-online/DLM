<fieldset>
	<legend>@lang("Quiz settings")</legend>
	<label>@lang("Feedback")
		<select name="feedback_type">
			<option value="direct" @if($node->block->feedback_type=='direct') selected="selected" @endif)>@lang("Immediately after question")</option>
			<option value="end" @if($node->block->feedback_type=='end') selected="selected" @endif>@lang("After quiz completion")</option>
			<option value="no" @if($node->block->feedback_type=='no') selected="selected" @endif>@lang("No feedback")</option>
		</select>
	</label>
	<label class="check">
		<input type="checkbox" name="allow_navigation" @if($node->block->allow_navigation) checked="checked" @endif>@lang("Allow answering in any order")
	</label>
	<label>
		@lang("Allowed attempts")
		<input type="number" name="attempts_allowed" value="{{$node->block->attempts_allowed or '1' }}" />
	</label>
	<label class="check">
		<input type="checkbox" name="attempts_allowed_unlimited" @if($node->block->attempts_allowed == -1) checked="checked" @endif>@lang("Allow unlimited attempts")
	</label>
</fieldset>
<fieldset>
	<legend>@lang("Display")</legend>
	<label>
		<select name="display">
			<option value="horizontal" @if($node->block->setting('display') == 'horizontal') selected="selected" @endif>@lang("Horizontally")</option>
			<option value="vertical" @if($node->block->setting('display') == 'vertical') selected="selected" @endif>@lang("Vertically")</option>
		</select>
	</label>
	<p>@lang("Display is always vertical when more than 5 questions are added")</p>
</fieldset>
<fieldset>
	<legend>DEV SETTINGS</legend>
	<label class="check"><input type="checkbox" name="reset_attempts">@lang("Reset attempts")</label>
</fieldset>