<x-admin-layout title="Dashboard">
    @php
        $canSeeProduction = auth()->user()->can('projects.view') || auth()->user()->can('clients.view');
        $canSeeAccess = collect([
            auth()->user()->can('roles.view'),
            auth()->user()->can('permissions.view'),
            auth()->user()->can('users.view'),
        ])->contains(true);
    @endphp

    @if (! $canSeeProduction && ! $canSeeAccess)
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
            You're signed in, but your role doesn't have access to any admin sections yet.
            Ask an administrator to grant you a permission from Admin &rarr; Users.
        </div>
    @endif

    @if ($canSeeProduction)
        <!-- What's waiting on us -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            @can('clients.view')
                <a href="{{ route('admin.clients.index', ['status' => 'pending']) }}"
                   class="rounded-xl border bg-white p-6 transition {{ $pendingClientsCount > 0 ? 'border-amber-300 ring-1 ring-amber-100' : 'border-slate-200 hover:border-slate-300' }}">
                    <div class="text-sm font-medium text-slate-500">Awaiting activation</div>
                    <div class="mt-2 text-3xl font-bold {{ $pendingClientsCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                        {{ $pendingClientsCount }}
                    </div>
                    <div class="mt-1 text-xs text-slate-400">accounts need a decision</div>
                </a>

                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <div class="text-sm font-medium text-slate-500">Active clients</div>
                    <div class="mt-2 text-3xl font-bold text-slate-900">{{ $activeClientsCount }}</div>
                </div>
            @endcan

            @can('projects.view')
                <a href="{{ route('admin.projects.index', ['status' => 'active']) }}"
                   class="rounded-xl border border-slate-200 bg-white p-6 transition hover:border-slate-300">
                    <div class="text-sm font-medium text-slate-500">In production</div>
                    <div class="mt-2 text-3xl font-bold text-slate-900">{{ $activeProjectsCount }}</div>
                </a>

                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <div class="text-sm font-medium text-slate-500">Delivered</div>
                    <div class="mt-2 text-3xl font-bold text-slate-900">{{ $completedProjectsCount }}</div>
                </div>
            @endcan
        </div>

        <!-- Pipeline load -->
        @can('projects.view')
            @if ($stages->isNotEmpty())
                <div class="mt-8 rounded-xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-slate-900">Pipeline</h2>
                        @can('pipeline.view')
                            <a href="{{ route('admin.pipeline.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">
                                Manage stages &rarr;
                            </a>
                        @endcan
                    </div>

                    <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
                        @foreach ($stages as $stage)
                            <a href="{{ route('admin.projects.index', ['stage' => $stage->id]) }}"
                               class="min-w-[140px] flex-1 rounded-lg border border-slate-200 p-4 transition hover:border-slate-300">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $stage->badgeClasses() }}">
                                    {{ $stage->name }}
                                </span>
                                <div class="mt-2 text-xl font-bold text-slate-900">{{ $countsByStage[$stage->id] ?? 0 }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mt-8 rounded-xl border border-dashed border-amber-300 bg-amber-50 p-6 text-sm text-amber-900">
                    No pipeline stages exist yet, so uploads have nowhere to go.
                    @can('pipeline.manage')
                        <a href="{{ route('admin.pipeline.create') }}" class="font-semibold underline">Create the first stage</a>.
                    @endcan
                </div>
            @endif

            <div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <span class="font-semibold text-slate-900">Latest uploads</span>
                    <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">All projects &rarr;</a>
                </div>

                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentProjects as $project)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-slate-900 hover:text-amber-600">
                                        {{ $project->title }}
                                    </a>
                                    <div class="font-mono text-xs text-slate-400">{{ $project->reference }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $project->client->name }}</td>
                                <td class="px-6 py-4">
                                    @if ($project->stage)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                            {{ $project->stage->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-400">{{ $project->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-8 text-center text-slate-400">No uploads yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan
    @endif

    @if ($canSeeAccess)
        <div class="mt-10">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Access control</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @can('roles.view')
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Roles</div>
                        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $rolesCount }}</div>
                        <a href="{{ route('admin.roles.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                            {{ auth()->user()->can('roles.manage') ? 'Manage roles' : 'View roles' }} &rarr;
                        </a>
                    </div>
                @endcan

                @can('permissions.view')
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Permissions</div>
                        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $permissionsCount }}</div>
                        <a href="{{ route('admin.permissions.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                            {{ auth()->user()->can('permissions.manage') ? 'Manage permissions' : 'View permissions' }} &rarr;
                        </a>
                    </div>
                @endcan

                @can('users.view')
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Users</div>
                        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $usersCount }}</div>
                        <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">
                            {{ auth()->user()->can('users.manage-roles') ? 'Manage user roles' : 'View users' }} &rarr;
                        </a>
                    </div>
                @endcan
            </div>
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
    @endif
</x-admin-layout>
