<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <link rel="icon" sizes="16x16" href="{{ asset("favicon.ico") }}"/>
    <title>@yield("title")</title>
    <link rel="stylesheet" href="{{ asset("assets/frontend/css/custom.css") }}"/>
</head>
<body>
    @yield("content")

    @if($allowGlobalView)
    <div class="search-container mb-4">
        <form method="GET" action="{{ route('ranking.index') }}" class="d-flex gap-2">
            <input type="text" 
                   name="search" 
                   value="{{ $currentSearch }}" 
                   placeholder="Nick/Steam ID" 
                   class="form-control search-bar">
            <input type="hidden" name="limit" value="{{ $currentLimit }}">
            <input type="hidden" name="order" value="{{ $currentOrder }}">
            <input type="hidden" name="ip_server" value="{{ $currentIp }}">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
    @endif

    @yield("script")
</body>
</html>
