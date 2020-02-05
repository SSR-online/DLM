@foreach($current->children()->where(['is_page' => '0'])->get() as $node)
	@include('node.display', ['current'=>$node])
@endforeach
