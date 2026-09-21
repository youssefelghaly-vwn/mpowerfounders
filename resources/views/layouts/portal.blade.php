@props(['title' => 'Your projects', 'subtitle' => null])

{{--
    Registered as <x-portal-layout> — see AppServiceProvider::boot().

    The client-facing app shell. Deliberately not <x-admin-layout>: clients
    should never see the admin sidebar or its vocabulary. Same Tailwind
    system, warmer surface, and only the three things a client does — see
    their work, upload more, follow a project.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} &middot; MPower Founders</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
        <a href="{{ route('portal.dashboard') }}" class="font-bold tracking-tight text-slate-900">
            MPOWER <span class="text-amber-500">Founders</span>
        </a>

        <nav class="flex items-center gap-1">
            @php
                $links = [
                    ['route' => 'portal.dashboard', 'label' => 'Dashboard', 'match' => 'portal.dashboard'],
                    ['route' => 'portal.projects.index', 'label' => 'My projects', 'match' => 'portal.projects.index'],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs($link['match']) ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <a href="{{ route('portal.projects.create') }}"
               class="ml-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                New upload
            </a>

            {{-- Staff who also hold the client role can hop back. --}}
            @if (auth()->user()?->isStaff())
                <a href="{{ route('admin.dashboard') }}"
                   class="ml-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Admin
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-900">
                    Log out
                </button>
            </form>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

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

<footer class="mx-auto max-w-6xl px-6 pb-10 text-xs text-slate-400">
    &copy; {{ date('Y') }} MPower Founders
</footer>

</body>
</html>
