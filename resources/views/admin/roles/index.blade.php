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
                            <div class="font-medium text-slate-900">{{ $role->name }}</div>
                            <div class="text-xs text-slate-400">{{ $role->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $role->permissions_count }}</td>
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