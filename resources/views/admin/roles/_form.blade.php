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

    @if (auth()->user()->isSuperAdmin())
        {{-- Only a super admin sees this control at all; RoleService
             ignores the field for anyone else, so it can't be posted in. --}}
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <label class="flex items-start gap-3 text-sm text-amber-900">
                <input type="hidden" name="is_superadmin" value="0">
                <input type="checkbox" name="is_superadmin" value="1"
                       @checked(old('is_superadmin', $role->is_superadmin))
                       class="mt-0.5 rounded-sm border-amber-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                <span>
                    <span class="font-semibold">Super admin</span>
                    <span class="block text-xs text-amber-800">
                        Anyone holding this role can open and do everything in the admin panel, whatever
                        permissions are ticked below. Only super admins can grant this.
                    </span>
                </span>
            </label>
            @error('is_superadmin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @elseif ($role->is_superadmin)
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="font-semibold">Super admin role.</span>
            Holders get every ability in the admin panel regardless of the permissions below.
            Only a super admin can change that.
        </div>
    @endif

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

        @if ($role->is_superadmin)
            <p class="mt-2 text-xs text-slate-400">
                Not consulted while this role is flagged super admin — kept so the role still makes
                sense if the flag is ever removed.
            </p>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
            {{ $role->exists ? 'Save changes' : 'Create role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
    </div>
</div>