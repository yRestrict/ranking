@extends('frontend.master')


@section('title', 'Player Stats')

@section('content')
    @php
        $stats_css = $validated['style'] ? asset('css/statscustum.css') : asset('css/stats_nonsteam.css');
        $currentRankImage = asset('css/img/ranks/rank' . $player->Level . '.png');
        $nextRankImage = asset('css/img/ranks/rank' . ($player->Level + 1) . '.png');
        $flagImagePath = asset('css/img/countries/' . $player->flag . '.png');
        $color = '#FFFFFF'; // Cor padrão, pode ser substituída por lógica de flags
        $color_skill = '#FFFFFF'; // Cor padrão para skill
        
        $online = $player->Online ? '<img src="' . asset('css/img/online.png') . '" class="playerstatus"></img>' : 
                                    '<img src="' . asset('css/img/offline.png') . '" class="playerstatus"></img>';
        
        $profile = $player->Steam ? '<a href="' . $player->Profile . '"><img border="0" src="' . $player->Avatar . '"></a>' : 
                                    '<img src="' . $player->Avatar . '">';
        
        $top15Url = route('ranking', [
            'top' => $validated['top'] ?? null,
            'player' => $validated['me'] ?? null,
            'style' => $validated['style'] ?? 1,
            'order' => $validated['order'] ?? null,
            'default_order' => $validated['default_order'] ?? null,
            'page' => $validated['page'] ?? 1,
            'search' => $validated['search'] ?? null,
            'srv' => $validated['global'] ? '' : $validated['srv']
        ]);
        
        $show_link = '<a id="url" href="' . $top15Url . '"><p>↵ Show Top Stats</p></a>';
    @endphp

    @if($color != '#FFFFFF' && $validated['style'])
        <style>.glow { text-shadow: 1px 1px 10px {{ $color }}; }</style>
    @endif

    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ $stats_css }}">
    {!! $show_link !!}
    
    <table>
        <td class="player-profile">
            <div class="player-card">
                {!! $profile !!}
                <div class="player-nick">
                    <a style="color:{{ $color }}" class="glow">{{ $player->Nick }}</a>
                </div> 
                <div class="player-rank">
                    <div class="player-flags" style="background-image: url('{{ $flagImagePath }}');">
                        <div class="player-city">{{ $player->city }} {{ $player->country }}
                            <div class="player-position">Posição: <a>{{ $player->rank }}º</a></div>
                        </div>
                    </div>
                </div>
                <style>.skill { background: {{ $color_skill }}; }</style>
            </div>
            <div class="player-status">
                <div class="rank-staky" style="background-image: url('{{ $currentRankImage }}');"><a>Atual</a></div>
                <div id="h">
                    <p id="i">{{ $player->RankName }}</p>
                    <div id="j"><div class="progress" style="width:{{ $player->rankProgress }}%"></div></div>
                    <p id="k">{{ $player->XP }}XP (+{{ $player->nextXpNeeded }})</p>
                </div>
                <div class="rank-staky1" style="background-image: url('{{ $nextRankImage }}');"><a>Próximo</a></div>
            </div>
            <div class="player-status">
                <p id="mvp">Most Valuable Player:<a id="g">{{ $player->MVP }}</a></p>
                <p id="rwn">Rounds Ganhos:<a id="g">{{ $player->RoundsWon }}</a></p>
                <p id="bp">Bandeira Roubada:<a id="g">{{ $player->Stolen }}</a></p>
                <p id="bc">Bandeira Recuperada:<a id="g">{{ $player->Recupered }}</a></p>
                <p id="di">Bandeira Entregue:<a id="g">{{ $player->Captured }}</a></p>
            </div>
        </td>

        <td id="n">
            <div class="player-card"><div id="f">Estatísticas</div></div>
            <div id="l1">
                <p id="kills">Kills:<a>{{ $player->Kills }}</a></p>
                <p id="deaths">Deaths:<a>{{ $player->Deaths }}</a></p>
                <p id="assists">Assists:<a>{{ $player->Assists }}</a></p>
                <p id="headshots">Headshots:<a>{{ $player->Headshots }} ({{ number_format($player->hsPercentage, 1) }}%)</a></p>
                <p id="kdratio">K/D Ratio:<a>{{ number_format($player->kdRatio, 1) }}</a></p>
            </div>
            <div id="l2">
                <p id="shots">Tiros Disparados:<a>{{ $player->Shots }}</a></p>
                <p id="hits">Acertos:<a>{{ $player->Hits }}</a></p>
                <p id="damage">Dano:<a>{{ $player->Damage }}</a></p>
                <p id="accuracy">Precisão:<a>{{ number_format($player->accuracy, 1) }}%</a></p>
                <p id="efficiency">Eficiência:<a>{{ number_format($player->efficiency, 1) }}%</a></p>
            </div>
            <div id="l3">
                <p id="firstlogin">Primeiro Login:<a>{{ $player->FirstLogin }}</a></p>
                <p id="lastlogin">Ultimo Login:<a>{{ $player->LastLogin }}</a></p>
                <p id="playedtime">Tempo Jogado:<a>{{ $player->playedTimeFormatted }}</a></p>
            </div>
        </td>
        <td id="o">
            <div class="player-card"><div id="f">Top Weapons</div></div>
            
            @foreach($topWeapons as $weapon)
                <div id="m">
                    <p>{{ $weapon->name ?: 'n/a' }}</p>
                    <div id="w{{ $weapon->id + 1 }}">{{ $weapon->kills }} Kills</div>
                </div>
            @endforeach
        </td>
    </table>
@endsection