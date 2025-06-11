@extends('frontend.master')

@section('content')
<div class="container">
    <table>
        <thead>
            <tr>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => '13']) }}">#</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '0' ? '14' : '0']) }}">XP</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '1' ? '15' : '1']) }}">Nick</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '2' ? '16' : '2']) }}">Kills</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '4' ? '18' : '4']) }}">Deaths</a>
                </th>
                <th>K/D</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '6' ? '20' : '6']) }}">HS %</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '3' ? '17' : '3']) }}">T Bandeiras</a>
                </th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order' => $currentOrder == '12' ? '26' : '12']) }}">Rank</a>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranking as $index => $player)
            <tr>
                <td class="colunm-content">{{ ($currentPage - 1) * $currentLimit + $index + 1 }}</td>
                <td class="colunm-content">
                    <div class="xp{{
                        $player->XP >= 10000 ? 6 :
                        ($player->XP >= 5000 ? 5 :
                        ($player->XP >= 2500 ? 4 :
                        ($player->XP >= 1000 ? 3 :
                        ($player->XP >= 500 ? 2 : 1))))
                    }}">
                        <div class="number-content">
                            <div class="number">{{ number_format($player->XP) }}</div>
                        </div>
                    </div>
                </td>
                <td class="colunm-content">
                    <div class="playernick">
                    @php
                        $borderClass = $player->Online ? 'avatar-online' : 'avatar-offline';
                    @endphp

                    @if($player->Avatar)
                        <img src="{{ $player->Avatar }}" alt="Avatar"
                            width="30" height="30"
                            class="avatar-circle border {{ $borderClass }}">
                    @else
                        <img src="{{ asset('assets/frontend/img/default_avatar.jpg') }}" alt="Default Avatar"
                            width="30" height="30"
                            class="avatar-circle border {{ $borderClass }}">
                    @endif
                    
                    <a>{{ $player->Nick }}</a>
                    @if($player->Steam)
                        <img src="{{ asset('assets/frontend/img/icon-steam.png') }}" alt="Steam"
                            width="10" height="10">                    
                    @endif
                    
                    <span class="server-name-simple">
                        {{ $serverNames[$player->ServerIp] ?? $player->ServerIp }}
                    </span>
                   
                   <div>
                </td>
                <td class="colunm-content">{{ number_format($player->Kills) }}</td>
                <td class="colunm-content">{{ number_format($player->Deaths) }}</td>
                <td class="colunm-content">{{ $player->Deaths > 0 ? number_format($player->Kills / $player->Deaths, 2) : number_format($player->Kills, 2) }}</td>
                <td class="colunm-content">
                    <div class="hs-content">
                    {{ number_format($player->Headshots) }}
                        <div class="progress-container" title="{{ $player->hsPercentage }}%">
                            <a>{{ $player->hsPercentage }}%</a>
                                <div class="progress-bar" style="width: {{ $player->hsPercentage }}%"></div>
                        </div>
                    </div>
                </td>
                <td class="colunm-content">
                    {{ number_format(($player->Stolen ?? 0) + ($player->Captured ?? 0) + ($player->Recupered ?? 0)) }}
                </td>
                <td class="colunm-content">
                    @php
                        $rank = $player->Level + 1;
                        $rankImage = asset('assets/frontend/img/ranks/rank' . ($rank - 1) . '.png');
                    @endphp

                    <div class="rank-cell" style="background-image: url('{{ $rankImage }}');"></div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection