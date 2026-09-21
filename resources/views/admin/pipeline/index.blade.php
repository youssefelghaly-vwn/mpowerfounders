<x-admin-layout title="Pipeline">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">
            {{ $stages->count() }} stage(s) — every project moves through these in order.
        </p>
        @can('pipeline.manage')
            <a href="{{ route('admin.pipeline.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                New stage
            </a>
        @endcan
    </div>

    {{-- Board view: the pipeline as the team actually thinks about it,
         with the live project count sitting on each stage. --}}
    <div class="mb-8 flex gap-3 overflow-x-auto pb-2">
        @forelse ($stages as $stage)
            <div class="min-w-[180px] flex-1 rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $stage->badgeClasses() }}">
                        {{ $stage->name }}
                    </span>
                    @if ($stage->is_default)
                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Entry</span>
                    @elseif ($stage->is_final)
                        <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Final</span>
                    @endif
                </div>
                <div class="mt-3 text-2xl font-bold text-slate-900">{{ $countsByStage[$stage->id] ?? 0 }}</div>
                <div class="text-xs text-slate-400">project(s) here</div>
            </div>
        @empty
            <div class="w-full rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-400">
                No stages yet. Create the first one to start accepting uploads —
                projects submitted with an empty pipeline have nowhere to go.
            </div>
        @endforelse
    </div>

    <form action="{{ route('admin.pipeline.reorder') }}" method="POST">
        @csrf

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 w-20">Order</th>
                        <th class="px-6 py-3">Stage</th>
                        <th class="px-6 py-3">Client email</th>
                        <th class="px-6 py-3">Projects</th>
                        @can('pipeline.manage')
                            <th class="px-6 py-3"></th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($stages as $stage)
                        <tr>
                            <td class="px-6 py-4">
                                @can('pipeline.manage')
                                    {{-- Submitting the rows in their listed order is what
                                         reorder() persists; the number itself is display only. --}}
                                    <input type="hidden" name="stages[]" value="{{ $stage->id }}">
                                @endcan
                                <span class="font-mono text-xs text-slate-400">{{ $stage->position }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $stage->badgeClasses() }}">
                                        {{ $stage->name }}
                                    </span>
                                    @if ($stage->is_default)
                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Entry point</span>
                                    @endif
                                    @if ($stage->is_final)
                                        <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Marks delivered</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-slate-400">{{ $stage->description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($stage->notifies_client)
                                    <span class="text-xs font-medium text-emerald-600">Sends update</span>
                                @else
                                    <span class="text-xs text-slate-400">Silent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $stage->projects_count }}</td>
                            @can('pipeline.manage')
                                <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('admin.pipeline.edit', $stage) }}" class="font-medium text-amber-600 hover:text-amber-700">Edit</a>
                                    <button type="submit"
                                            form="delete-stage-{{ $stage->id }}"
                                            class="font-medium text-red-600 hover:text-red-700">
                                        Delete
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">No stages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @can('pipeline.manage')
            @if ($stages->isNotEmpty())
                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Save order
                    </button>
                    <p class="text-xs text-slate-400">Positions are renumbered from the order shown above.</p>
                </div>
            @endif
        @endcan
    </form>

    {{-- Delete forms live outside the reorder form: nested <form> elements
         are invalid HTML and the inner one is dropped by the parser. --}}
    @can('pipeline.manage')
        @foreach ($stages as $stage)
            <form id="delete-stage-{{ $stage->id }}"
                  action="{{ route('admin.pipeline.destroy', $stage) }}"
                  method="POST"
                  class="hidden"
                  onsubmit="return confirm('Delete the {{ $stage->name }} stage?');">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endcan
</x-admin-layout>
