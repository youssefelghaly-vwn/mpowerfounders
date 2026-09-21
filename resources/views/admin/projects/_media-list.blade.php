{{--
    Shared file table for the project screen — used for the client's source
    material and for each stage's own uploads.

    Expects: $items (Collection<ProjectMedia>), $project.
--}}
<table class="w-full text-sm">
    <tbody class="divide-y divide-slate-100">
        @forelse ($items as $item)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-900">{{ $item->original_name }}</span>
                        @unless ($item->visible_to_client)
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Internal</span>
                        @endunless
                    </div>
                    <div class="text-xs text-slate-400">
                        {{ $item->kindLabel() }} &middot; {{ $item->humanSize() }}
                        @if ($item->uploader) &middot; {{ $item->uploader->name }} @endif
                        &middot; {{ $item->created_at->diffForHumans() }}
                    </div>
                    @if ($item->description)
                        <div class="mt-1 text-xs text-slate-500">{{ $item->description }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-right whitespace-nowrap space-x-3">
                    @if ($item->isVideo() || $item->isAudio())
                        <a href="{{ route('media.show', $item) }}" target="_blank" rel="noopener"
                           class="font-medium text-slate-500 hover:text-slate-800">Play</a>
                    @endif
                    <a href="{{ route('media.download', $item) }}" class="font-medium text-amber-600 hover:text-amber-700">Download</a>
                    @can('projects.manage')
                        <button type="submit" form="delete-media-{{ $item->id }}" class="font-medium text-red-600 hover:text-red-700">
                            Delete
                        </button>
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td class="px-6 py-6 text-center text-sm text-slate-400">No files here yet.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Kept outside the table so these forms never nest inside the upload form. --}}
@can('projects.manage')
    @foreach ($items as $item)
        <form id="delete-media-{{ $item->id }}"
              action="{{ route('admin.projects.media.destroy', [$project, $item]) }}"
              method="POST"
              class="hidden"
              onsubmit="return confirm('Delete {{ $item->original_name }}?');">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endcan
