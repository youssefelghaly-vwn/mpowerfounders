@csrf
@if ($stage->exists)
    @method('PUT')
@endif

<div class="max-w-2xl space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-6">
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $stage->name) }}" required
                   class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Internal description</label>
            <input type="text" name="description" value="{{ old('description', $stage->description) }}"
                   placeholder="What happens to a project while it sits here"
                   class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Message to the client</label>
            <textarea name="client_message" rows="3"
                      placeholder="Included in the email sent when a project reaches this stage."
                      class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('client_message', $stage->client_message) }}</textarea>
            @error('client_message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Position</label>
                <input type="number" name="position" min="0" max="999"
                       value="{{ old('position', $stage->position) }}"
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Colour</label>
                <select name="color"
                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    @foreach (['slate' => 'Slate', 'amber' => 'Amber', 'blue' => 'Blue', 'violet' => 'Violet', 'emerald' => 'Emerald', 'rose' => 'Rose'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('color', $stage->color) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
        <p class="text-sm font-medium text-slate-700">Behaviour</p>

        {{-- The hidden input makes an unchecked box submit as "0" — without
             it the key is simply absent and the flag can never be turned off. --}}
        <label class="flex items-start gap-3 text-sm text-slate-700">
            <input type="hidden" name="is_default" value="0">
            <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $stage->is_default))
                   class="mt-0.5 rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
            <span>
                <span class="font-medium">Entry point</span>
                <span class="block text-xs text-slate-400">New uploads land here. Only one stage can hold this.</span>
            </span>
        </label>

        <label class="flex items-start gap-3 text-sm text-slate-700">
            <input type="hidden" name="is_final" value="0">
            <input type="checkbox" name="is_final" value="1" @checked(old('is_final', $stage->is_final))
                   class="mt-0.5 rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
            <span>
                <span class="font-medium">Marks the project delivered</span>
                <span class="block text-xs text-slate-400">Reaching this stage completes the project and sends the delivery email.</span>
            </span>
        </label>

        <label class="flex items-start gap-3 text-sm text-slate-700">
            <input type="hidden" name="notifies_client" value="0">
            <input type="checkbox" name="notifies_client" value="1" @checked(old('notifies_client', $stage->exists ? $stage->notifies_client : true))
                   class="mt-0.5 rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
            <span>
                <span class="font-medium">Email the client on arrival</span>
                <span class="block text-xs text-slate-400">Turn off for internal-only stages. Can still be overridden per move.</span>
            </span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            {{ $stage->exists ? 'Save changes' : 'Create stage' }}
        </button>
        <a href="{{ route('admin.pipeline.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
    </div>
</div>
