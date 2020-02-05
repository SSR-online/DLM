@section('header')
@if($block->mainLibrary)
<link href="/css/h5p.css" rel="stylesheet" type="text/css">
<script src="/js/h5p/jquery.js"></script>

<script>
window.H5PIntegration = {
  "baseUrl": "{{ env('APP_URL') }}",
  "url": "/storage/h5p/",
  "postUserStatistics": false,
  "ajaxPath": "/path/to/h5p-ajax",
  "ajax": {
    "setFinished": "/h5p/{{$block->id}}/results/new", 
    "contentUserData": "/h5p/:contentId/user-data?data_type=:dataType&subContentId=:subContentId"
  },
  "saveFreq": 30,
  "siteUrl": "{{ env('APP_URL') }}",
  "l10n": {
    "H5P": { 
      "fullscreen": "Fullscreen",
      "disableFullscreen": "Disable fullscreen",
      "download": "Download",
      "copyrights": "Rights of use",
      "embed": "Embed",
      "size": "Size",
      "showAdvanced": "Show advanced",
      "hideAdvanced": "Hide advanced",
      "advancedHelp": "Include this script on your website if you want dynamic sizing of the embedded content:",
      "copyrightInformation": "Rights of use",
      "close": "Close",
      "title": "Title",
      "author": "Author",
      "year": "Year",
      "source": "Source",
      "license": "License",
      "thumbnail": "Thumbnail",
      "noCopyrights": "No copyright information available for this content.",
      "downloadDescription": "Download this content as a H5P file.",
      "copyrightsDescription": "View copyright information for this content.",
      "embedDescription": "View the embed code for this content.",
      "h5pDescription": "Visit H5P.org to check out more cool content.",
      "contentChanged": "This content has changed since you last used it.",
      "startingOver": "You'll be starting over.",
      "by": "by",
      "showMore": "Show more",
      "showLess": "Show less",
      "subLevel": "Sublevel"
    } 
  },
  "loadedJs": [],
  "loadedCss": [],
  "contents": {
  },
  "core": { // Only required when Embed Type = iframe
    "scripts": ['/js/h5p/jquery.js', '/js/h5p/h5p.js', '/js/h5p/h5p-content-type.js', '/js/h5p/h5p-event-dispatcher.js', '/js/h5p/h5p-x-api.js', '/js/h5p/h5p-x-api-event.js'], 
    "styles": ['/css/h5p.css']
  }
};

</script>
@endif
@endsection

@if($block->mainLibrary)
<div class="h5p-iframe-wrapper"><iframe id="h5p-iframe-{{$block->id}}" class="h5p-iframe" data-content-id="{{$block->id}}" style="height:1px" src="about:blank" frameBorder="0" scrolling="no"></iframe></div>

<script>
  window.H5PIntegration.contents['cid-{{$block->id}}'] = {!! $block->json_object() !!};
</script>
@else
    <h3>Leeg H5P blok</h3>
@endif
@section('footer')
@if($block->mainLibrary)
<script src="/js/h5p/h5p.js"></script>
<script src="/js/h5p/h5p-content-type.js"></script>
<script src="/js/h5p/h5p-event-dispatcher.js"></script>
<script src="/js/h5p/h5p-x-api.js"></script>
<script src="/js/h5p/h5p-x-api-event.js"></script>
@endif
@endsection