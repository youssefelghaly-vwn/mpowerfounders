<x-admin-layout :title="$project->title">
    @php
        $entriesByStage = $project->stageEntries->keyBy('pipeline_stage_id');
        $mediaByStage = $project->media->groupBy('pipeline_stage_id');
        $sourceMedia = $mediaByStage->get(null, collect());
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2 space-y-6">

            <!-- Summary -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs text-slate-400">{{ $project->reference }}</span>
                            <span class="text-xs text-slate-300">&middot;</span>
                            <span class="text-xs text-slate-400">{{ $project->typeLabel() }}</span>
                        </div>
                        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $project->title }}</h2>
                        <p class="text-sm text-slate-500">
                            <a href="{{ route('admin.clients.show', $project->client) }}" class="hover:text-amber-600">
                                {{ $project->client->name }}
                            </a>
                            &middot; {{ $project->client->email }}
                        </p>
                    </div>

                    <div class="text-right">
                        @if ($project->stage)
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                {{ $project->stage->name }}
                            </span>
                        @endif
                        <div class="mt-1 text-xs text-slate-400">
                            {{ $project->statusLabel() }}
                            @if ($project->stage_changed_at)
                                &middot; moved {{ $project->stage_changed_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                </div>

                @if ($project->brief)
                    <div class="mt-6 rounded-lg bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Client brief</p>
                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $project->brief }}</p>
                    </div>
                @endif

                <dl class="mt-6 grid gap-4 sm:grid-cols-3 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Submitted</dt>
                        <dd class="mt-1 text-slate-700">{{ $project->created_at->format('j M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Due</dt>
                        <dd class="mt-1 text-slate-700">{{ $project->due_date?->format('j M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Uploaded by</dt>
                        <dd class="mt-1 text-slate-700">{{ $project->creator?->name ?? $project->client->name }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Client source files -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-6 py-4 font-semibold text-slate-900">
                    Source material ({{ $sourceMedia->count() }})
                </div>
                @include('admin.projects._media-list', ['items' => $sourceMedia, 'project' => $project])
            </div>

            <!-- Per-stage workspaces: notes and the files produced there -->
            @foreach ($stages as $stage)
                @php
                    $entry = $entriesByStage->get($stage->id);
                    $stageMedia = $mediaByStage->get($stage->id, collect());
                    $isCurrent = $project->pipeline_stage_id === $stage->id;
                @endphp

                @continue(! $entry && ! $isCurrent && $stageMedia->isEmpty())

                <div class="overflow-hidden rounded-xl border bg-white {{ $isCurrent ? 'border-amber-300 ring-1 ring-amber-100' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $stage->badgeClasses() }}">
                                {{ $stage->name }}
                            </span>
                            @if ($isCurrent)
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-amber-600">Current stage</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400">
                            @if ($entry?->entered_at)
                                entered {{ $entry->entered_at->diffForHumans() }}
                            @endif
                            @if ($entry?->completed_at)
                                &middot; finished {{ $entry->completed_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    @can('projects.manage')
                        <form action="{{ route('admin.projects.stage.entry', [$project, $stage]) }}" method="POST" class="border-b border-slate-100 px-6 py-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Internal notes</label>
                                    <textarea name="notes" rows="3"
                                              class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ $entry?->notes }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Shown to the client</label>
                                    <textarea name="client_summary" rows="3"
                                              class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ $entry?->client_summary }}</textarea>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-end gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Assigned to</label>
                                    <select name="assigned_to" class="mt-1 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                        <option value="">Nobody</option>
                                        @foreach ($staff as $member)
                                            <option value="{{ $member->id }}" @selected($entry?->assigned_to === $member->id)>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Save stage notes
                                </button>
                            </div>
                        </form>

                        <!-- Files produced at this stage -->
                        <form action="{{ route('admin.projects.media.store', $project) }}" method="POST" enctype="multipart/form-data"
                              class="border-b border-slate-100 bg-slate-50 px-6 py-5 space-y-4">
                            @csrf
                            <input type="hidden" name="pipeline_stage_id" value="{{ $stage->id }}">

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Upload to this stage</label>
                                    <input type="file" name="files[]" multiple required
                                           class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-slate-800">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Kind</label>
                                    <select name="kind" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                        @foreach (\App\Models\ProjectMedia::KINDS as $value => $label)
                                            <option value="{{ $value }}" @selected($value === \App\Models\ProjectMedia::KIND_DELIVERABLE)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500">Description</label>
                                <input type="text" name="description" placeholder="e.g. First cut, 12 min, colour graded"
                                       class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="hidden" name="visible_to_client" value="0">
                                    <input type="checkbox" name="visible_to_client" value="1" checked
                                           class="rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                                    Share with the client (sends them an email)
                                </label>

                                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                    Upload
                                </button>
                            </div>
                        </form>
                    @endcan

                    @include('admin.projects._media-list', ['items' => $stageMedia, 'project' => $project])
                </div>
            @endforeach
        </div>

        <!-- Right rail -->
        <div class="space-y-6">
            @can('projects.manage')
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="font-semibold text-slate-900">Move through the pipeline</h3>

                    <form action="{{ route('admin.projects.stage.move', $project) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Stage</label>
                            <select name="pipeline_stage_id" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}" @selected($project->pipeline_stage_id === $stage->id)>
                                        {{ $stage->name }}{{ $stage->is_final ? ' (delivers)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Note to the client</label>
                            <textarea name="note" rows="3" placeholder="Optional — quoted in the update email."
                                      class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="notify_client" value="0">
                            <input type="checkbox" name="notify_client" value="1" checked
                                   class="rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                            Email the client
                        </label>
                        <p class="text-xs text-slate-400">
                            Unticking sends nothing, whatever the stage is set to do by default.
                        </p>

                        <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Move project
                        </button>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="font-semibold text-slate-900">Project details</h3>

                    <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-medium text-slate-500">Title</label>
                            <input type="text" name="title" value="{{ old('title', $project->title) }}" required
                                   class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500">Brief</label>
                            <textarea name="brief" rows="3"
                                      class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('brief', $project->brief) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-500">Type</label>
                                <select name="type" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                    @foreach (\App\Models\Project::TYPES as $value => $label)
                                        <option value="{{ $value }}" @selected(old('type', $project->type) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $project->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500">Due date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                        </div>

                        <button type="submit" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Save details
                        </button>
                    </form>
                </div>
            @endcan

            <!-- History -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="font-semibold text-slate-900">History</h3>

                <ol class="mt-4 space-y-4">
                    @forelse ($project->histories as $history)
                        <li class="border-l-2 border-slate-200 pl-4">
                            <div class="text-sm font-medium text-slate-800">
                                {{ $history->fromStage?->name ?? 'Submitted' }} &rarr; {{ $history->toStage?->name ?? '—' }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $history->created_at->format('j M Y, H:i') }}
                                @if ($history->author) &middot; {{ $history->author->name }} @endif
                                @if ($history->client_notified)
                                    &middot; <span class="text-emerald-600">client emailed</span>
                                @endif
                            </div>
                            @if ($history->note)
                                <p class="mt-1 text-sm text-slate-600">{{ $history->note }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">Nothing recorded yet.</li>
                    @endforelse
                </ol>
            </div>

            @can('projects.manage')
                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Delete {{ $project->reference }} and every file on it? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                        Delete project
                    </button>
                </form>
            @endcan
        </div>
    </div>
</x-admin-layout>
