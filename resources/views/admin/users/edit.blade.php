<x-admin-layout :title="'Manage roles — ' . $user->name">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="max-w-xl">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="font-semibold text-slate-900">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>

            <div class="mt-6 space-y-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                               @checked($user->roles->pluck('id')->contains($role->id))
                               class="rounded-sm border-slate-300 text-amber-600 focus:ring-2 focus:ring-amber-500">
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Save roles
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
            </div>
        </div>
    </form>
</x-admin-layout>