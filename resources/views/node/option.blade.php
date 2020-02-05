<?php
	$type = (isset($type)) ? $type : false;
	$selected = (isset($selected)) ? $selected : false;
?>
@if($nodes)
@foreach($nodes[$id] as $aNode)
	<option value="{{$aNode->id}}" 
		@if(($type && $node->$type && ($aNode->is($node->$type))) 
		|| ($selected && ($aNode->is($selected)))) 
			@if($canselect) selected="selected" 
			@endif 
		@endif 
		@if($aNode->is($node)) disabled="disabled" @endif>
		{{$prefix}} {{$aNode->title}}
	</option>
	@if(!empty($nodes[$aNode->id]))
	@include('node.option', ['type' => $type, 'selected' => $selected, 'nodes'=> $nodes, 'node'=>$node, 'id'=>$aNode->id, 'prefix' => $prefix . '-'])
	@endif
@endforeach
@endif