<x-admin-layout title="Projects">
    <!-- Pipeline overview: click a stage to filter the table below it. -->
    <div class="mb-8 flex gap-3 overflow-x-auto pb-2">
        @foreach ($stages as $stage)
            <a href="{{ route('admin.projects.index', ['stage' => $stage->id]) }}"
               class="min-w-[150px] flex-1 rounded-xl border bg-white p-4 transition
                      {{ (string) ($filters['stage'] ?? '') === (string) $stage->id ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200 hover:border-slate-300' }}">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $stage->badgeClasses() }}">
                    {{ $stage->name }}
                </span>
                <div class="mt-3 text-2xl font-bold text-slate-900">{{ $countsByStage[$stage->id] ?? 0 }}</div>
            </a>
        @endforeach
    </div>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500">Search</label>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Reference, title or client"
                       class="mt-1 w-64 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Stage</label>
                <select name="stage" class="mt-1 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    <option value="">All stages</option>
                    @foreach ($stages as $stage)
                        <option value="{{ $stage->id }}" @selected((string) ($filters['stage'] ?? '') === (string) $stage->id)>{{ $stage->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Status</label>
                <select name="status" class="mt-1 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    <option value="">All</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Filter
            </button>

            @if (array_filter($filters ?? []))
                <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        @can('projects.manage')
            <a href="{{ route('admin.projects.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Upload for a client
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Project</th>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3">Stage</th>
                    <th class="px-6 py-3">Files</th>
                    <th class="px-6 py-3">Updated</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $project->title }}</div>
                            <div class="font-mono text-xs text-slate-400">{{ $project->reference }} &middot; {{ $project->typeLabel() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-slate-700">{{ $project->client->name }}</div>
                            <div class="text-xs text-slate-400">{{ $project->client->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($project->stage)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                    {{ $project->stage->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">Unassigned</span>
                            @endif
                            @if ($project->status !== \App\Models\Project::STATUS_ACTIVE)
                                <div class="mt-1 text-[11px] uppercase tracking-wide text-slate-400">{{ $project->statusLabel() }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $project->media_count }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $project->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-amber-600 hover:text-amber-700">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No projects match those filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $projects->links() }}</div>
</x-admin-layout>
