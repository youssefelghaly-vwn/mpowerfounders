<x-admin-layout :title="$client->name">
    <div class="grid gap-6 lg:grid-cols-3">

        <!-- Account -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $client->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $client->email }}</p>
                    </div>

                    @php
                        $statusClasses = match ($client->status) {
                            \App\Models\User::STATUS_ACTIVE => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            \App\Models\User::STATUS_SUSPENDED => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                            default => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                        {{ $client->statusLabel() }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Company</dt>
                        <dd class="mt-1 text-slate-700">{{ $client->company ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Phone</dt>
                        <dd class="mt-1 text-slate-700">{{ $client->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Registered</dt>
                        <dd class="mt-1 text-slate-700">{{ $client->created_at?->format('j M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Activated</dt>
                        <dd class="mt-1 text-slate-700">
                            {{ $client->activated_at?->format('j M Y, H:i') ?? 'Not yet' }}
                            @if ($client->activatedBy)
                                <span class="text-xs text-slate-400">by {{ $client->activatedBy->name }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Roles</dt>
                        <dd class="mt-1">
                            @forelse ($client->roles as $role)
                                <span class="mr-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $role->name }}</span>
                            @empty
                                <span class="text-xs text-slate-400">None — activating this account attaches the client role.</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>

                @if ($client->about)
                    <div class="mt-6 rounded-lg bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">What they told us</p>
                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">{{ $client->about }}</p>
                    </div>
                @endif

                @if ($client->admin_notes)
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-amber-700">Internal notes</p>
                        <p class="mt-1 text-sm text-amber-900 whitespace-pre-line">{{ $client->admin_notes }}</p>
                    </div>
                @endif

                @can('clients.manage')
                    <div class="mt-6 flex items-center gap-3">
                        <a href="{{ route('admin.clients.edit', $client) }}"
                           class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Edit details
                        </a>
                        <a href="{{ route('admin.projects.create', ['client' => $client->id]) }}"
                           class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Upload for this client
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Their projects -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-6 py-4 font-semibold text-slate-900">
                    Projects ({{ $projects->count() }})
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($projects as $project)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-slate-900 hover:text-amber-600">
                                        {{ $project->title }}
                                    </a>
                                    <div class="font-mono text-xs text-slate-400">{{ $project->reference }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($project->stage)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $project->stage->badgeClasses() }}">
                                            {{ $project->stage->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-400">{{ $project->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-8 text-center text-slate-400">No projects yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activation panel -->
        @can('clients.manage')
            <div class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="font-semibold text-slate-900">Account access</h3>

                    @if ($client->isPending())
                        <p class="mt-2 text-sm text-slate-500">
                            This account is waiting on you. Activating it attaches the client role, lets them sign in,
                            and emails them a welcome message.
                        </p>
                    @endif

                    <form action="{{ route('admin.clients.status', $client) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Set status</label>
                            <select name="status" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500">
                                <option value="active" @selected($client->isActive())>Active</option>
                                <option value="suspended" @selected($client->status === \App\Models\User::STATUS_SUSPENDED)>Suspended</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Note for the email</label>
                            <textarea name="note" rows="3" placeholder="Optional — included in the message they receive."
                                      class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="notify" value="0">
                            <input type="checkbox" name="notify" value="1" checked
                                   class="rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                            Email the client about this change
                        </label>

                        <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Save status
                        </button>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h3 class="font-semibold text-slate-900">Password link</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Sends a fresh link to set a password — useful for a client we created who never got in.
                    </p>

                    <form action="{{ route('admin.clients.resend-invitation', $client) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Resend password link
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    </div>
</x-admin-layout>
