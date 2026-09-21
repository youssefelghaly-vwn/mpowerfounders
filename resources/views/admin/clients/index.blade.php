<x-admin-layout title="Clients">
    @if ($pendingCount > 0)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            <strong>{{ $pendingCount }}</strong> account(s) are waiting to be activated. They can't sign in or
            upload anything until you activate them.
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500">Search</label>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Name, email or company"
                       class="mt-1 w-64 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Status</label>
                <select name="status" class="mt-1 rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                    <option value="">All</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Filter
            </button>

            @if (array_filter($filters ?? []))
                <a href="{{ route('admin.clients.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        @can('clients.manage')
            <a href="{{ route('admin.clients.create') }}"
               class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Add client
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Projects</th>
                    <th class="px-6 py-3">Registered</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($clients as $client)
                    <tr class="{{ $client->isPending() ? 'bg-amber-50/40' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $client->name }}</div>
                            <div class="text-xs text-slate-400">{{ $client->email }}</div>
                            @if ($client->company)
                                <div class="text-xs text-slate-400">{{ $client->company }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = match ($client->status) {
                                    \App\Models\User::STATUS_ACTIVE => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    \App\Models\User::STATUS_SUSPENDED => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                                {{ $client->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $client->projects_count }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $client->created_at?->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-amber-600 hover:text-amber-700">
                                {{ $client->isPending() ? 'Review' : 'Open' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">No accounts match those filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $clients->links() }}</div>
</x-admin-layout>
