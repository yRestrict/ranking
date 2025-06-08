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
    <div class="attributes">
        <div class="search-pagination-row">
            <div class="search-content"> 
                <form method="GET" action="{{ route('ranking.index') }}" class="search-form">
                    <input type="text" 
                        name="search" 
                        value="{{ $currentSearch }}" 
                        placeholder="Nick/Steam ID" 
                        class="search-bar">
                    <input type="hidden" name="limit" value="{{ $currentLimit }}">
                    <input type="hidden" name="order" value="{{ $currentOrder }}">
                    <input type="hidden" name="ip_server" value="{{ $currentIp }}">
                    <button type="submit" class="button">Buscar</button>
                </form>
            </div>

            {{-- Paginação --}}
            @if(isset($paginator) && $paginator->hasPages())
            <div class="pagination-content">
                <div class="pagination">
                    {{-- Botão Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span class="btn-page disabled">Anterior</span>
                    @else
                        <a class="btn-page" href="{{ $paginator->previousPageUrl() }}">Anterior</a>
                    @endif

                    {{-- Links das Páginas --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <span class="btn-page active">{{ $i }}</span>
                        @else
                            <a class="btn-page" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Botão Próxima --}}
                    @if ($paginator->hasMorePages())
                        <a class="btn-page" href="{{ $paginator->nextPageUrl() }}">Próxima</a>
                    @else
                        <span class="btn-page disabled">Próxima</span>
                    @endif
                </div>

                {{-- Informações da Paginação
                <div class="pagination-info">
                    <small>
                        Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} 
                        de {{ $paginator->total() }} resultados
                    </small>
                </div>--}}
            </div>
            @endif
        </div>
    </div>
    @endif

    @yield("script")
</body>
</html>
