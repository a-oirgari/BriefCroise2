<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyColoc - @yield('title', 'Gestion de colocation')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#6366f1', dark: '#4f46e5', light: '#a5b4fc' },
                        surface: '#f8fafc',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">


<nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
    
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-primary font-bold text-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                EasyColoc
            </a>

            
            @if(session('user_id'))
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('dashboard') }}"
                   class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
                    Dashboard
                </a>
                <a href="{{ route('colocations.index') }}"
                   class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">
                    Ma Colocation
                </a>
                @if(isset($authUser) && $authUser->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                     Admin
                </a>
                @endif
            </div>

            
            <div class="flex items-center gap-3">
                <a href="{{ route('profile') }}"
                   class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="hidden md:block">{{ $authUser->name ?? '' }}</span>
                    @if(isset($authUser) && $authUser->reputation != 0)
                        <span class="text-xs {{ $authUser->reputation > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $authUser->reputation > 0 ? '+' : '' }}{{ $authUser->reputation }}⭐
                        </span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm text-gray-400 hover:text-red-500 transition-colors px-2 py-1">
                        Déconnexion
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</nav>


<div class="max-w-7xl mx-auto w-full px-4 pt-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5h2v2H9v-2zm0-8h2v6H9V5z" clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>


<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
</main>


<footer class="border-t border-gray-200 bg-white mt-auto py-4 text-center text-xs text-gray-400">
    EasyColoc &copy; {{ date('Y') }} — Gestion de colocation simplifiée
</footer>

</body>
</html>