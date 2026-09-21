<x-admin-layout title="Dashboard">
    @php
        $visibleCards = collect([
            auth()->user()->can('roles.view'),
            auth()->user()->can('permissions.view'),
            auth()->user()->can('users.view'),
        ])->contains(true);
    @endphp

    @if (! $visibleCards)
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
            You're signed in, but your role doesn't have access to any admin sections yet.
            Ask an administrator to grant you a permission from Admin &rarr; Users.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @can('roles.view')
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-medium text-slate-500">Roles</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $rolesCount }}</div>
                @can('roles.manage')
                    <a href="{{ route('admin.roles.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        Manage roles &rarr;
                    </a>
                @else
                    <a href="{{ route('admin.roles.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        View roles &rarr;
                    </a>
                @endcan
            </div>
        @endcan

        @can('permissions.view')
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-medium text-slate-500">Permissions</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $permissionsCount }}</div>
                @can('permissions.manage')
                    <a href="{{ route('admin.permissions.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        Manage permissions &rarr;
                    </a>
                @else
                    <a href="{{ route('admin.permissions.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        View permissions &rarr;
                    </a>
                @endcan
            </div>
        @endcan

        @can('users.view')
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="text-sm font-medium text-slate-500">Users</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $usersCount }}</div>
                @can('users.manage-roles')
                    <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        Manage user roles &rarr;
                    </a>
                @else
                    <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                        View users &rarr;
                    </a>
                @endcan
            </div>
        @endcan
    </div>

    @can('roles.view')
        <div class="mt-8 rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-4 font-semibold text-slate-900">Recently added roles</div>
            <ul class="divide-y divide-slate-100">
                @forelse ($recentRoles as $role)
                    <li class="flex items-center justify-between px-6 py-3">
                        <span class="font-medium text-slate-800">{{ $role->name }}</span>
                        <span class="text-xs text-slate-400">{{ $role->created_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="px-6 py-6 text-sm text-slate-400">No roles yet.</li>
                @endforelse
            </ul>
        </div>
    @endcan
</x-admin-layout>