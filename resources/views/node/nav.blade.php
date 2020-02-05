<?php 
$nodes = isset($children) ? $children : $nav_nodes;
$sort_url = '/module/' . $module->id . '/sortnodes';
$sort_url .= (isset($parent)) ? '/' . $parent->id : '';
?>
@if($isediting)<input type="hidden" name="menusort-{{$parent->id or ''}}" id="menusort-{{$parent->id or ''}}" value="" />@endif
<ol class="sticky @if($isediting) sortable @endif" data-field="menusort-{{$parent->id or ''}}" data-group="nav" data-url="{{ $sort_url }}">
@if($nodes)
@foreach( $nodes as $nodeList )
<?php $node = $nodeList['node']; $children = $nodeList['children']; ?>
    <li data-id="{{$node->id}}" class="@if($node->active) active @endif @if($node->completed) seen @endif">
      	<a class="{{ $node->classes }}" href="{{$node->path_url }}">{{$node->title or 'Pagina zonder titel'}}</a>
      	@if(!empty($children))
      		@include('node.nav', ['children'=>$children, 'parent'=>$node])
      	@endif
    </li>
@endforeach
@endif
@if($ismoving)
	<li class="edit move"><a class="edit move" href="/node/{{$ismoving}}/move/targetnode/{{$parent->id or ''}}">Hierheen verplaatsen</a></li>
@elseif($isediting) @can('update', $module) <li class="edit"><a class="edit" href="/module/{{$module->id}}/page/create/{{ $id or '' }}">@lang("Add page")</a></li> @endcan @endif
</ol>
