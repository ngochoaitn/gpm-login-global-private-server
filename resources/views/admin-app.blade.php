<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GPM Global Private Server v{{ \App\Services\SettingService::$server_version }}</title>
    @vite(['resources/css/admin.css', 'resources/js/admin/main.js'])
    <script>
        window.__APP_CONFIG__ = {
            baseUrl: @json(url('/')),
            apiBaseUrl: @json(url('/admin/api')),
            apiV1BaseUrl: @json(url('/api')),
            legacyAdminUrl: @json(url('/admin')),
            logoutUrl: @json(url('/admin/auth/logout')),
            serverVersion: @json(\App\Services\SettingService::$server_version),
            phpVersion: @json(PHP_VERSION),
            csrfToken: @json(csrf_token()),
            initialUser: @json(auth()->user()),
        };
    </script>
</head>

<body>
    <div id="app"></div>
</body>

</html>
