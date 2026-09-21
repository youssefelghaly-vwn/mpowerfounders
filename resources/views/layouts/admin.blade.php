@props(['title' => 'Admin'])

{{--
    Registered as <x-admin-layout> — see AppServiceProvider::boot() note.
    Separate from the marketing site's <x-app-layout>: this is a
    functional dashboard shell (sidebar + topbar + content), built with
    Tailwind utility classes rather than the hand-rolled site CSS.

    Usage:
        <x-admin-layout title="Roles">
            ... page content ...
        </x-admin-layout>
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} &middot; Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-white tracking-tight">
                MPOWER <span class="text-amber-400">Admin</span>
            </a>
        </div>

        @php
            // Dashboard is always visible to anyone who has at least one
            // role (that's the route-level gate). Everything else only
            // shows up if the signed-in user actually holds the matching
            // permission — same slugs the routes themselves check.
            $navItems = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'match' => 'admin.dashboard', 'permission' => null],
                ['route' => 'admin.roles.index', 'label' => 'Roles', 'match' => 'admin.roles.*', 'permission' => 'roles.view'],
                ['route' => 'admin.permissions.index', 'label' => 'Permissions', 'match' => 'admin.permissions.*', 'permission' => 'permissions.view'],
                ['route' => 'admin.users.index', 'label' => 'Users', 'match' => 'admin.users.*', 'permission' => 'users.view'],
            ];
        @endphp

        <nav class="flex-1 px-3 py-6 space-y-1">
            @foreach ($navItems as $item)
                @if (is_null($item['permission']) || auth()->user()->can($item['permission']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                              {{ request()->routeIs($item['match']) ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="px-6 py-4 border-t border-slate-800 text-xs text-slate-500">
            Signed in as {{ auth()->user()->name ?? 'Guest' }}
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 flex items-center justify-between border-b border-slate-200 bg-white px-6">
            <h1 class="text-lg font-semibold text-slate-900">{{ $title }}</h1>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-900">
                    Log out
                </button>
            </form>
        </header>

        <main class="flex-1 p-6">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>