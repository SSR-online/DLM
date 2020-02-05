@if($block->setting('display') == 'inline' && $block->getExtensionClass() == 'pdf')
	<div style="padding-bottom: 95vh; position: relative; height: 0; overflow: hidden;">
		<iframe style=" position: absolute; top:0; left: 0; width: 100%; height: 100%;" frameBorder="0" src="/js/pdfjs/web/viewer.html?file={{ $block->downloadPath }}"></iframe>
	</div>
@else
	@if($block->path) <a class="{{$block->linkClass}}" href="{{$block->downloadPath}}" data-turbolinks="false" />{{$node->title}}</a>
	@else <h3>Leeg bestand-blok</h3>
	@endif
@endif