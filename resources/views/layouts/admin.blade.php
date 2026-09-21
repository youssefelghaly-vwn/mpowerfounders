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
            // Grouped so the day-to-day production work sits apart from
            // the access-control screens, which are configuration.
            $navGroups = [
                'Production' => [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'match' => 'admin.dashboard', 'permission' => null, 'badge' => null],
                    ['route' => 'admin.projects.index', 'label' => 'Projects', 'match' => 'admin.projects.*', 'permission' => 'projects.view', 'badge' => null],
                    ['route' => 'admin.pipeline.index', 'label' => 'Pipeline', 'match' => 'admin.pipeline.*', 'permission' => 'pipeline.view', 'badge' => null],
                    ['route' => 'admin.clients.index', 'label' => 'Clients', 'match' => 'admin.clients.*', 'permission' => 'clients.view', 'badge' => 'pending'],
                ],
                'Access' => [
                    ['route' => 'admin.roles.index', 'label' => 'Roles', 'match' => 'admin.roles.*', 'permission' => 'roles.view', 'badge' => null],
                    ['route' => 'admin.permissions.index', 'label' => 'Permissions', 'match' => 'admin.permissions.*', 'permission' => 'permissions.view', 'badge' => null],
                    ['route' => 'admin.users.index', 'label' => 'Users', 'match' => 'admin.users.*', 'permission' => 'users.view', 'badge' => null],
                ],
            ];

            // How many accounts are sitting in the queue waiting to be
            // activated — the one number worth carrying in the nav.
            $pendingClients = auth()->user()->can('clients.view')
                ? \App\Models\User::pending()->count()
                : 0;
        @endphp

        <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto">
            @foreach ($navGroups as $group => $items)
                @php
                    $visible = collect($items)->filter(fn ($item) =>
                        is_null($item['permission']) || auth()->user()->can($item['permission']));
                @endphp

                @if ($visible->isNotEmpty())
                    <div class="space-y-1">
                        <p class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-600">{{ $group }}</p>

                        @foreach ($visible as $item)
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                                      {{ request()->routeIs($item['match']) ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <span>{{ $item['label'] }}</span>

                                @if ($item['badge'] === 'pending' && $pendingClients > 0)
                                    <span class="rounded-full bg-amber-400 px-2 py-0.5 text-[11px] font-bold text-slate-900">
                                        {{ $pendingClients }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-slate-800 px-3 py-4 space-y-1">
            {{-- Ways out of the panel. The portal link only appears for
                 someone who actually holds the client role, since that is
                 what /portal is gated on. --}}
            <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-slate-400 transition hover:bg-slate-800 hover:text-white">
                View public site
            </a>

            @if (auth()->user()->isClient())
                <a href="{{ route('portal.dashboard') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-slate-400 transition hover:bg-slate-800 hover:text-white">
                    Client portal
                </a>
            @endif

            <p class="px-3 pt-2 text-xs text-slate-500">
                Signed in as {{ auth()->user()->name ?? 'Guest' }}
                @if (auth()->user()?->isSuperAdmin())
                    <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wide text-amber-400">Super admin</span>
                @endif
            </p>
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

            {{-- Errors raised outside a form (a stage that can't be deleted,
                 for instance) would otherwise vanish silently. --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>