<x-admin-layout title="Upload for a client">
    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-6">
        @csrf

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700">Client</label>
                <select name="client_id" required
                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    <option value="">Choose a client…</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected((int) old('client_id', $selectedClient) === $client->id)>
                            {{ $client->name }} — {{ $client->email }}
                        </option>
                    @endforeach
                </select>
                @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @if ($clients->isEmpty())
                    <p class="mt-2 text-sm text-amber-700">
                        No activated clients yet — <a href="{{ route('admin.clients.create') }}" class="underline">add one first</a>.
                    </p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Project title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Type</label>
                    <select name="type" class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                        @foreach (\App\Models\Project::TYPES as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'video') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Due date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Brief</label>
                <textarea name="brief" rows="4" placeholder="What the client wants doing with this material."
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('brief') }}</textarea>
                @error('brief') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Source files</label>
                <input type="file" name="files[]" multiple
                       class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
                <p class="mt-2 text-xs text-slate-400">
                    Up to {{ round(config('media.max_upload_kb') / 1024) }} MB per file. Video, audio, images, PDFs and subtitles.
                </p>
                @error('files') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Create project
            </button>
            <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
        </div>
    </form>
</x-admin-layout>
