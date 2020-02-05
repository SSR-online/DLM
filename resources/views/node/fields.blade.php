{{ csrf_field() }}
<fieldset>
	<input type="hidden" name="module_id" value="{{ optional($node->module)->id }}" />
	<input type="hidden" name="layout_slot_id" value="{{ $node->layoutSlot->id or '' }}" />
	<label for="title">
		@lang("Title"):
		<input type="text" name="title" value="{{ old('title', $node->title) }}" />
	</label>
	{{-- <label for="description">
		Inhoud:
		<textarea class="edit" name="description">{{ old('description', $node->description) }}</textarea>
	</label> --}}
	@if(!$node->block || $node->block->can_change_parent)
	<label>
		@lang("Parent node")
		<select name="parent_id">
			<option name="-1">@lang('Select…')</option>
			<option name="0">@lang('Make top level page')</option>
			@include('node.option', ['type'=> 'parent', 'nodes' => $node->module->nodesByParent(), 'node' => $node, 'id' => '', 'prefix' => '', 'canselect' => true])
		</select>
	</label>
	@endif
	<label class="check">
		<input type="checkbox" name="is_page" @if($node->is_page) checked="checked" @endif>
		@lang("Show as page (top level nodes are always pages)")
	</label>
	<label class="check">
		<input type="checkbox" name="show_in_menu" @if($node->setting('show_in_menu')) checked="checked" @endif>
		@lang('Show in menu (top level always shows)')
	</label>
</fieldset>
<fieldset id="page-options" @if(!$node->is_page) class="admin-hidden" @endif>
	<label for="previous_id">
		@lang("Previous page")
	</label>
	<div>
		<select class="inline" name="previous_id">
			<option @if($node->previous_id==-2) selected="selected" @endif value="-2">@lang('Select automatically')</option>
			<option @if($node->previous_id==-1) selected="selected" @endif value="-1">@lang('None')</option>
			@include('node.option', ['type' => 'previous', 'nodes' => $node->module->nodesByParent(), 'node' => $node, 'id' => '', 'prefix' => '', 'canselect' => ($node->previous_id>0)])
		</select>
		<input class="inline" type="text" name="previous_title" placeholder="@lang('title')" value="{{$node->setting('previous_title') }}"/>
	</div>

	<label for="next_id">
		@lang('Next page')
	</label>
	<div>
		<select class="inline" name="next_id" id="next_id">
			<option @if($node->next_id==-2) selected="selected" @endif value="-2">@lang("Select automatically")</option>
			<option @if($node->next_id==-1) selected="selected" @endif value="-1">@lang("none")</option>
			@include('node.option', ['type' => 'next', 'nodes' => $node->module->nodesByParent(), 'node' => $node, 'id' => '', 'prefix' => '', 'canselect' => ($node->next_id>0)])
		</select>
		<input class="inline" type="text" name="next_title" placeholder="@lang('title')" value="{{$node->setting('next_title') }}"/>
	</div>
	
	@if($node->setting('jump_nodes', false))
	<fieldset>
		<legend>@lang('Jump pages')</legend>
			@foreach($node->setting('jump_nodes', false) as $jump_node)
			<label for="jump_id_{{$loop->iteration}}">Jump:</label>
			<div>
				<select class="inline" name="jump_id_{{$loop->iteration}}" id="jump_id_{{$loop->iteration}}">
					<option @if($jump_node['id']=='') selected="selected" @endif value="">@lang('Not selected yet')</option>

					@include('node.option', ['selected'=> App\Node::find($jump_node['id']), 'nodes' => $node->module->nodesByParent(), 'node' => $node, 'id' => '', 'prefix' => '', 'canselect' => true])
				</select>
				<input class="inline" type="text" name="jump_name_{{$loop->iteration}}" placeholder="@lang('title')" value="{{$jump_node['name']}}"/>
				<a class="delete" href=@if($jump_node['id']) "/node/{{$node->id}}/jump/{{$jump_node['id']}}/delete" @else "/node/{{$node->id}}/jump/delete" @endif />@lang('Delete')</a>
			</div>
			@endforeach
	</fieldset>
	@endif
	<input type="submit" name="add_jump_link" value="✚ @lang('Add link to next page')" />

	<label for="template">@lang('Page template')</label>
	<select name="template" id="template">
		<option @if($node->setting('template')=='normal') selected="selected" @endif value="normal">@lang('Normal')</option>
		<option @if($node->setting('template')=='fullscreen') selected="selected" @endif value="fullscreen">@lang('Fullscreen')</option>
	</select>
</fieldset>

@if($node->block)
	@include('block.edit' . class_basename($node->block))
@endif

{{-- Show timestamp field if the parent is a video --}}
@if($node->parent && class_basename($node->parent->block)=='VideoBlock')
<fieldset>
	<legend>@lang('Show in video')</legend>
	<label>
		@lang('Show after (seconds)'):
		<input type="number" step="0.1" min="0" name="timestamp" value="{{$node->block->setting('timestamp')}}" />
	</label>
	@if($node->block)
	<label class="check">
		<input type="checkbox" name="unskippable" @if($node->block->setting('unskippable')==1)checked="checked"@endif />
		@lang("Hide 'next' button")
	</label>
	@endif
</fieldset>
@endif

<fieldset>
	<legend>@lang("Order")</legend>
	<input type="hidden" name="nodes_sort_order" id="nodes_sort_order" value="" />
	<div class="sortable" data-field="nodes_sort_order" data-group="nodes_sort_order">
	@foreach($node->children as $child)
		<div data-id="{{$child->id}}">
			<label>{{$child->title}}
			<input type="number" name="sort_order_{{$child->id}}" value="{{$child->sort_order or $loop->iteration}}" />
			</label>
		</div>
	@endforeach
	</div>
</fieldset>


<input type="submit" value="@lang("Save")" />
<a href="{{$node->path()}}">@lang("Cancel")</a>