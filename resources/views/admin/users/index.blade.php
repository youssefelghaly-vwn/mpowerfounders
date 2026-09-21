<x-admin-layout title="Users">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Roles</th>
                    @can('users.manage-roles')
                        <th class="px-6 py-3"></th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @forelse ($user->roles as $role)
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 mr-1">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">No roles</span>
                            @endforelse
                        </td>
                        @can('users.manage-roles')
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-amber-600 hover:text-amber-700">Manage roles</a>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>