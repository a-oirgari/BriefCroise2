@extends('layouts.auth')
@section('title', 'Invitation')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">

    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mb-2">Vous avez été invité !</h2>
    <p class="text-gray-600 text-sm mb-1">Colocation :</p>
    <p class="text-lg font-semibold text-indigo-600 mb-1">{{ $invitation->colocation->name }}</p>
    <p class="text-xs text-gray-400 mb-6">
        Invitation pour : {{ $invitation->email }} ·
        Expire le {{ $invitation->expires_at->format('d/m/Y') }}
    </p>

    @if(!$user)
        <p class="text-sm text-amber-600 bg-amber-50 rounded-lg px-4 py-3 mb-6">
            Vous devez vous connecter avec l'email <strong>{{ $invitation->email }}</strong> pour accepter.
        </p>
        <div class="flex gap-3">
            <a href="{{ route('login') }}"
               class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                Se connecter
            </a>
            <a href="{{ route('register') }}"
               class="flex-1 border border-indigo-600 text-indigo-600 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-50 transition-colors">
                S'inscrire
            </a>
        </div>
    @else
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm text-left">
                @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif
        <div class="flex gap-3">
            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    ✓ Accepter
                </button>
            </form>
            <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    ✕ Refuser
                </button>
            </form>
        </div>
    @endif

</div>
@endsection