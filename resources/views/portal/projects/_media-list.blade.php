{{--
    Client-side file list. Expects $items, $project and $removable — the
    last only permits the control; ProjectMediaPolicy::delete() is what
    actually decides, per file.
--}}
<table class="w-full text-sm">
    <tbody class="divide-y divide-slate-100">
        @forelse ($items as $item)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-slate-900">{{ $item->original_name }}</div>
                    <div class="text-xs text-slate-400">
                        {{ $item->kindLabel() }} &middot; {{ $item->humanSize() }} &middot; {{ $item->created_at->diffForHumans() }}
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
                    @if ($removable && auth()->user()->can('delete', $item))
                        <button type="submit" form="remove-media-{{ $item->id }}" class="font-medium text-red-600 hover:text-red-700">
                            Remove
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td class="px-6 py-6 text-center text-sm text-slate-400">Nothing here yet.</td></tr>
        @endforelse
    </tbody>
</table>

@if ($removable)
    @foreach ($items as $item)
        @continue(! auth()->user()->can('delete', $item))
        <form id="remove-media-{{ $item->id }}"
              action="{{ route('portal.projects.media.destroy', [$project, $item]) }}"
              method="POST"
              class="hidden"
              onsubmit="return confirm('Remove {{ $item->original_name }}?');">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
