{{ csrf_field() }}
<fieldset>
	<label for="title">
		@lang('Title'):
		<input type="text" name="title" value="{{ old('title', $module->title) }}" />
	</label>
	<label for="contents">
		@lang('Description'):
		<textarea class="edit" name="description">{{ old('description', $module->description) }}</textarea>
	</label>
</fieldset>
<fieldset>
	<legend>@lang('Remember visitor location')</legend>
	<label>@lang('Visitors are sent to last visited page within this many hours')
		<input type="number" step="1" name="remember_position" value="{{ $module->setting('remember_position') }}" />
	</label>
</fieldset>
<fieldset>
	<legend>Score</legend>
	<p>@lang('The module can return a numerical score to the') <abbr title="Learning Management system">LMS</abbr>. </p>
		<label>@lang('Minimumscore (percentage) before score will be sent')
			<input type="text" name="score_threshold" value="{{ $module->setting('score_threshold' )}}" />
		</label>
		<label><input class="inline" type="radio" name="score_by" value="none" @if($module->setting('score_by')=='none') checked="checked" @endif>@lang("Don't return score")</label>
		<label><input class="inline" type="radio" name="score_by" value="pages" @if($module->setting('score_by')=='pages') checked="checked" @endif>@lang('Pages seen (7/10: 70%)')</label>
		<label><input class="inline" type="radio" name="score_by" value="quiz" @if($module->setting('score_by')=='quiz') checked="checked" @endif>@lang('Quiz score (average for all quizzes, sent after all quizzes are completed)')</label>
		<label><input class="inline" type="radio" name="score_by" value="page_quiz" @if($module->setting('score_by')=='page_quiz') checked="checked" @endif>@lang('A combination of pages and quizzes (Weights to be determined)')</label>
		<h4>@lang('Score weights')</h4>
		<label>@lang('Pages'):<input type="number" name="score_by_page_percentage" class="inline" value="{{ $module->setting('score_by_page_percentage') }}"> @lang('percent')</label>
		<label>@lang("Quizzes"): <input type="number" name="score_by_quiz_percentage" class="inline" value="{{ $module->setting('score_by_quiz_percentage') }}"> @lang('percent')</label>
</fieldset>


<fieldset>
	<label>@lang("Category")
		<input type="text" name="category" value="{{ $module->category }}" />
	</label>
</fieldset>

<fieldset>
	<label class="inline">
		<input type="checkbox" name="archive" />@lang("Archive")</label>
	</label>
</fieldset>

<fieldset>
	<legend>@lang('Order')</legend>
	@foreach( $module->nodes->sortBy('sort_order') as $node )
	<label>{{$node->title}}
		<input type="number" name="node_sort_order_{{$node->id}}"value="{{ $node->sort_order }}" />
	</label>
	@endforeach
</fieldset>

<input type="submit" value="@lang("Save")" />