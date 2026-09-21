@csrf
@if ($client->exists)
    @method('PUT')
@endif

<div class="max-w-2xl space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" required
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Company</label>
                <input type="text" name="company" value="{{ old('company', $client->company) }}"
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                       class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">What they make</label>
            <textarea name="about" rows="3"
                      class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('about', $client->about) }}</textarea>
            @error('about') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Internal notes</label>
            <textarea name="admin_notes" rows="3" placeholder="Never shown to the client."
                      class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('admin_notes', $client->admin_notes) }}</textarea>
            @error('admin_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @unless ($client->exists)
            <div class="rounded-lg bg-slate-50 p-4">
                <label class="block text-sm font-medium text-slate-700">Note for the welcome email</label>
                <textarea name="welcome_note" rows="2" placeholder="Optional — appears in the email they receive."
                          class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('welcome_note') }}</textarea>
                <p class="mt-2 text-xs text-slate-500">
                    The account is created active. They'll get an email with a link to set their own password —
                    no password is chosen here.
                </p>
                @error('welcome_note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endunless
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            {{ $client->exists ? 'Save changes' : 'Create client and send welcome email' }}
        </button>
        <a href="{{ $client->exists ? route('admin.clients.show', $client) : route('admin.clients.index') }}"
           class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
    </div>
</div>
