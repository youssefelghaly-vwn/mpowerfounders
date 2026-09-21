<x-portal-layout title="My projects" subtitle="Everything you've sent us, and where it is right now.">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Project</th>
                    <th class="px-6 py-3">Stage</th>
                    <th class="px-6 py-3">Files</th>
                    <th class="px-6 py-3">Submitted</th>
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
                            @if ($project->stage)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                    {{ $project->stage->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">Queued</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $project->media_count }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $project->created_at->format('j M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('portal.projects.show', $project) }}" class="font-medium text-amber-600 hover:text-amber-700">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Nothing here yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $projects->links() }}</div>
</x-portal-layout>
