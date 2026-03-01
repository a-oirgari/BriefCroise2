@extends('layouts.app')
@section('title', 'Administration')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Administration</h1>
        <p class="text-sm text-gray-500 mt-1">Vue d'ensemble de la plateforme EasyColoc</p>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $statCards = [
            ['label' => 'Utilisateurs', 'value' => $stats['total_users'], 'color' => 'indigo'],
            ['label' => 'Bannis', 'value' => $stats['banned_users'], 'color' => 'red'],
            ['label' => 'Colocations', 'value' => $stats['total_colocations'], 'color' => 'blue'],
            ['label' => 'Actives', 'value' => $stats['active_colocations'], 'color' => 'green'],
            ['label' => 'Dépenses', 'value' => $stats['total_expenses'], 'color' => 'yellow'],
            ['label' => 'Total (dh)', 'value' => number_format($stats['total_amount'], 0), 'color' => 'purple'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Utilisateurs</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <th class="pb-3 pr-4">Nom</th>
                        <th class="pb-3 pr-4">Email</th>
                        <th class="pb-3 pr-4">Rôle</th>
                        <th class="pb-3 pr-4">Réputation</th>
                        <th class="pb-3 pr-4">Statut</th>
                        <th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $u)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 pr-4 font-medium text-gray-800">{{ $u->name }}</td>
                        <td class="py-3 pr-4 text-gray-500">{{ $u->email }}</td>
                        <td class="py-3 pr-4">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 {{ $u->reputation >= 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                            {{ $u->reputation >= 0 ? '+' : '' }}{{ $u->reputation }}
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $u->is_banned ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $u->is_banned ? 'Banni' : 'Actif' }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($u->id !== session('user_id'))
                                @if($u->is_banned)
                                    <form method="POST" action="{{ route('admin.users.unban', $u->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium">
                                            Débannir
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.ban', $u->id) }}" class="inline"
                                          onsubmit="return confirm('Bannir {{ $u->name }} ?')">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                                            Bannir
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Colocations</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <th class="pb-3 pr-4">Nom</th>
                        <th class="pb-3 pr-4">Owner</th>
                        <th class="pb-3 pr-4">Statut</th>
                        <th class="pb-3">Créée le</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($colocations as $coloc)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 pr-4 font-medium text-gray-800">{{ $coloc->name }}</td>
                        <td class="py-3 pr-4 text-gray-500">{{ $coloc->owner->name ?? '—' }}</td>
                        <td class="py-3 pr-4">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $coloc->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $coloc->status }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-400 text-xs">{{ $coloc->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $colocations->links() }}</div>
    </div>

</div>
@endsection