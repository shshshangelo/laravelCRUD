<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="{!! asset('appdev/resources/sap-ui-core.js') !!}"
            id="sap-ui-bootstrap"                
			data-sap-ui-libs="sap.m,sap.ui.commons,sap.ui.unified,sap.ui.ux3,sap.ui.table, sap.uxap, sap.tnt"
            data-sap-ui-theme="sap_bluecrystal">
    </script>
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/app.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/notify.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/normalize.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/ns-default.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/ns-style-growl.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! asset('appdev/resources/css/theme.css') !!}" />

    @yield('styles')
    @yield('content')
</head>
<body class="sapUiBody" role="application">	
	<div id="invisible_content" style="display:none;"></div>
	<div id="google_translate_element"></div>
    <div id="content"></div>      
	@yield('footer')	   
</body>
</html>