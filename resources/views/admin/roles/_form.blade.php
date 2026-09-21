@csrf
@if ($role->exists)
    @method('PUT')
@endif

<div class="space-y-6 max-w-2xl">
    <div>
        <label class="block text-sm font-medium text-slate-700">Name</label>
        <input type="text" name="name" value="{{ old('name', $role->name) }}"
               class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700">Description</label>
        <textarea name="description" rows="2"
                  class="mt-1 block w-full rounded-lg border-slate-300 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">{{ old('description', $role->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Permissions</label>
        <div class="grid grid-cols-2 gap-2 rounded-lg border border-slate-200 p-4 sm:grid-cols-3">
            @foreach ($permissions as $permission)
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                           @checked(collect(old('permissions', $role->permissions->pluck('id')->all()))->contains($permission->id))
                           class="rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                    {{ $permission->name }}
                </label>
            @endforeach
        </div>
        @error('permissions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            {{ $role->exists ? 'Save changes' : 'Create role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
    </div>
</div>