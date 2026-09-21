<x-portal-layout title="Your dashboard" :subtitle="'Welcome back, ' . auth()->user()->name . '.'">
    <div class="grid gap-6 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="text-sm font-medium text-slate-500">In production</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $activeCount }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="text-sm font-medium text-slate-500">Delivered</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $completedCount }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="text-sm font-medium text-slate-500">Files ready for you</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $deliverableCount }}</div>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <span class="font-semibold text-slate-900">Recent projects</span>
            <a href="{{ route('portal.projects.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">See all</a>
        </div>

        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('portal.projects.show', $project) }}" class="font-medium text-slate-900 hover:text-amber-600">
                                {{ $project->title }}
                            </a>
                            <div class="font-mono text-xs text-slate-400">{{ $project->reference }}</div>
                        </td>
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
                    <tr>
                        <td class="px-6 py-10 text-center">
                            <p class="text-sm text-slate-500">You haven't uploaded anything yet.</p>
                            <a href="{{ route('portal.projects.create') }}"
                               class="mt-4 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                Upload your first project
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-portal-layout>
