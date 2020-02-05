<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $node->title or '' }} - {{ $module->title or '' }} - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/polyfills.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/MediasitePlayerIFrameAPI.js') }}"></script>
    @yield('header')
</head>
<body class="@if($isediting)isediting @endif @if(!empty($node)) {{ $node->setting('template')}} @endif ">
        <nav class="main">
            <ul>
                @if(session('launch_presentation_return_url'))<li class="return"><a href="{{ session('launch_presentation_return_url') }}">@lang('back to') {{session('tool_consumer_instance_name')}}</a></li>@endif
                @can('create', App\Module::class)<li><a href="/">Modules</a></li>@endcan
                <li><a href="#{{ optional(Auth::user())->id }}">{{ optional(Auth::user())->name }}</a></li>
            </ul>
        </nav>
        <div id="main">
            <nav id="sidebar">
                <a id="menu" href="#">Menu</a>
                @yield('sidebar')
            </nav>
            <div id="contentmain">
                <section id="content">
                    <header>
                        <h1>@yield('title')</h1>
                        <div class="tools">
                        @if(!empty($module))
                            @can('update', $module)
                            @if($ismoving)
                                <form id="edittoggle" action="/module/{{$module->id}}/stopmoving" method="post">
                                {{ csrf_field() }}
                                <input type="submit" value="@lang('Cancel move')" />
                                </form>
                            @endif
                            @if($isediting)
                               <form id="edittoggle" action="/module/{{$module->id}}/toggleediting" method="post">
                                {{ csrf_field() }}
                                <input type="submit" value="@lang('Stop editing')" /></form>
                                  
                            @else
                                <form id="edittoggle" action="/module/{{$module->id}}/toggleediting" method="post">
                                {{ csrf_field() }}
                                <input type="submit" value="@lang("Edit module")" />
                                </form>
                            @endif
                            @endcan
                        @endif
                        @yield('pagetools')
                        </div>
                    </header>
                    @yield('content')
                </section>

                <nav id="bottom">
                    <ol>
                        @yield('bottomnav')
                    </ol>
                </nav>
            </div>
        </div>
        <script>
          tinymce.init({
            selector: 'textarea.edit',
            menubar: false,
            toolbar: 'code bullist numlist bold italic strikethrough link image media styleselect formatselect blockquote table undo redo cut copy paste removeformat',
            plugins : 'code, autoresize, link, image, lists, media, table',
            skin: 'lightgray',
            skin_url: '/css/tinymce/skins/lightgray',
            table_appearance_options : false,
            media_live_embeds: true,
            table_class_list: [
                {title: 'Geen', value: ''},
                {title: 'Vergelijk', value: 'compare'},
            ], 
            statusbar: false,
            branding: false,
            style_formats: [
                { title: "Blockquote", block: 'blockquote' },
                { title: "Pull quote", block: 'blockquote', classes: 'alternate' },
                { title: "Lijst zonder nummering", selector: 'ol', classes: 'list-nostyle' },
            ],
          });

          tinymce.init({
            selector: 'textarea.enduser',
            menubar: false,
            toolbar: 'bullist numlist bold italic strikethrough link formatselect blockquote undo redo cut copy paste removeformat',
            plugins : 'link, autoresize, lists',
            skin: 'lightgray',
            skin_url: '/css/tinymce/skins/lightgray',
            media_live_embeds: true,
            statusbar: false,
            branding: false
          });
      </script>
      @themecss()
      @yield('footer')
</body>
</html>