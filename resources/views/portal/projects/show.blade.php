<x-portal-layout :title="$project->title" :subtitle="$project->reference . ' · ' . $project->typeLabel()">
    @php
        $deliverables = $project->media->where('kind', \App\Models\ProjectMedia::KIND_DELIVERABLE);
        $yourFiles = $project->media->where('source', \App\Models\ProjectMedia::SOURCE_CLIENT);
        $otherFiles = $project->media->where('source', \App\Models\ProjectMedia::SOURCE_TEAM)
            ->where('kind', '!=', \App\Models\ProjectMedia::KIND_DELIVERABLE);
        $summaries = $project->stageEntries->filter(fn ($entry) => filled($entry->client_summary));
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">

            <!-- Where it is -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Current stage</p>
                        <div class="mt-1 flex items-center gap-2">
                            @if ($project->stage)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                    {{ $project->stage->name }}
                                </span>
                            @else
                                <span class="text-sm text-slate-500">Queued</span>
                            @endif
                            <span class="text-xs text-slate-400">{{ $project->statusLabel() }}</span>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-400">
                        Submitted {{ $project->created_at->format('j M Y') }}
                        @if ($project->due_date)
                            <div>Needed by {{ $project->due_date->format('j M Y') }}</div>
                        @endif
                    </div>
                </div>

                @if ($project->stage?->client_message)
                    <p class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                        {{ $project->stage->client_message }}
                    </p>
                @endif

                @if ($project->brief)
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Your brief</p>
                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $project->brief }}</p>
                    </div>
                @endif
            </div>

            <!-- Finished files -->
            @if ($deliverables->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-emerald-200 bg-white">
                    <div class="border-b border-emerald-100 bg-emerald-50 px-6 py-4 font-semibold text-emerald-900">
                        Ready for you ({{ $deliverables->count() }})
                    </div>
                    @include('portal.projects._media-list', ['items' => $deliverables, 'project' => $project, 'removable' => false])
                </div>
            @endif

            @if ($otherFiles->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-6 py-4 font-semibold text-slate-900">From our team</div>
                    @include('portal.projects._media-list', ['items' => $otherFiles, 'project' => $project, 'removable' => false])
                </div>
            @endif

            <!-- Your own uploads -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-6 py-4 font-semibold text-slate-900">
                    Your files ({{ $yourFiles->count() }})
                </div>
                @include('portal.projects._media-list', ['items' => $yourFiles, 'project' => $project, 'removable' => true])

                @can('upload', $project)
                    <form action="{{ route('portal.projects.media.store', $project) }}" method="POST" enctype="multipart/form-data"
                          class="border-t border-slate-100 bg-slate-50 px-6 py-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Add more material</label>
                            <input type="file" name="files[]" multiple required
                                   class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-slate-800">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Anything we should know?</label>
                            <input type="text" name="description" placeholder="Optional"
                                   class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                        </div>

                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Upload
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Progress -->
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="font-semibold text-slate-900">Progress</h3>

                <ol class="mt-4 space-y-4">
                    @forelse ($project->histories as $history)
                        <li class="border-l-2 border-amber-200 pl-4">
                            <div class="text-sm font-medium text-slate-800">
                                {{ $history->toStage?->name ?? 'Submitted' }}
                            </div>
                            <div class="text-xs text-slate-400">{{ $history->created_at->format('j M Y, H:i') }}</div>
                            @if ($history->note)
                                <p class="mt-1 text-sm text-slate-600">{{ $history->note }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">We'll update this as your project moves along.</li>
                    @endforelse
                </ol>
            </div>

            @if ($summaries->isNotEmpty())
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="font-semibold text-slate-900">Stage notes</h3>

                    <div class="mt-4 space-y-4">
                        @foreach ($summaries as $entry)
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">{{ $entry->stage?->name }}</p>
                                <p class="mt-1 text-sm text-slate-600 whitespace-pre-line">{{ $entry->client_summary }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
