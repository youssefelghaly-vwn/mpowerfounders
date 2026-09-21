<x-admin-layout title="Permissions">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">{{ $permissions->count() }} permission(s)</p>
        @can('permissions.manage')
            <a href="{{ route('admin.permissions.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                New permission
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Group</th>
                    <th class="px-6 py-3">Roles</th>
                    @can('permissions.manage')
                        <th class="px-6 py-3"></th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($permissions as $permission)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $permission->name }}</div>
                            <div class="text-xs text-slate-400">{{ $permission->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $permission->group ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $permission->roles_count }}</td>
                        @can('permissions.manage')
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="font-medium text-amber-600 hover:text-amber-700">Edit</a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this permission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No permissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>