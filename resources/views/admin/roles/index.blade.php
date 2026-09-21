<x-admin-layout title="Roles">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">{{ $roles->count() }} role(s)</p>
        @can('roles.manage')
            <a href="{{ route('admin.roles.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                New role
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Permissions</th>
                    <th class="px-6 py-3">Users</th>
                    @can('roles.manage')
                        <th class="px-6 py-3"></th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($roles as $role)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-slate-900">{{ $role->name }}</span>
                                @if ($role->is_superadmin)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        Super admin
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-400">{{ $role->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @if ($role->is_superadmin)
                                <span class="text-xs font-medium text-amber-600">All (bypasses permissions)</span>
                            @else
                                {{ $role->permissions_count }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $role->users_count }}</td>
                        @can('roles.manage')
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="font-medium text-amber-600 hover:text-amber-700">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No roles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>