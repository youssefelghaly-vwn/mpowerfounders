<x-portal-layout title="New upload" subtitle="Send us your raw podcast or video and tell us what you'd like made from it.">
    <form action="{{ route('portal.projects.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-6">
        @csrf

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700">What should we call this?</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Episode 14 — Fundraising with Dana Cho"
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
                    <label class="block text-sm font-medium text-slate-700">Needed by <span class="text-slate-400">(optional)</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">What would you like us to do?</label>
                <textarea name="brief" rows="5" required
                          placeholder="Cut to 8 minutes, pull three 60-second clips for LinkedIn, add captions and our intro..."
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('brief') }}</textarea>
                @error('brief') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Your files</label>
                <input type="file" name="files[]" multiple required
                       class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
                <p class="mt-2 text-xs text-slate-400">
                    Video, audio, images, PDFs or subtitle files — up to
                    {{ round(config('media.max_upload_kb') / 1024) }} MB each, {{ config('media.max_files_per_upload') }} at a time.
                    Large uploads can take a while; leave this tab open.
                </p>
                @error('files') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('files.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                Send to the team
            </button>
            <a href="{{ route('portal.projects.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
        </div>
    </form>
</x-portal-layout>
