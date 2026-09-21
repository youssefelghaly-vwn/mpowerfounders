<?php

namespace App\Http\Controllers;

use App\Models\ProjectMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves uploaded media to whoever is allowed to see it — clients for
 * their own project, staff for anything.
 *
 * Nothing is proxied through PHP when the disk can sign: the browser is
 * redirected to a short-lived object-store URL so multi-gigabyte video
 * never touches a worker. Disks that can't sign (the local disk in tests
 * and CI) fall back to a streamed response.
 */
class MediaController extends Controller
{
    public function show(ProjectMedia $medium): RedirectResponse|StreamedResponse
    {
        $this->authorize('view', $medium);

        return $this->serve($medium, download: false);
    }

    public function download(ProjectMedia $medium): RedirectResponse|StreamedResponse
    {
        $this->authorize('view', $medium);

        return $this->serve($medium, download: true);
    }

    private function serve(ProjectMedia $medium, bool $download): RedirectResponse|StreamedResponse
    {
        $disk = Storage::disk($medium->disk);

        abort_unless($disk->exists($medium->path), 404);

        try {
            return redirect()->away($disk->temporaryUrl(
                $medium->path,
                now()->addMinutes((int) config('media.temporary_url_minutes', 30)),
                $download ? [
                    'ResponseContentDisposition' => 'attachment; filename="'.addslashes($medium->original_name).'"',
                ] : [],
            ));
        } catch (\Throwable) {
            // Local/public disks don't support signed URLs.
            return $download
                ? $disk->download($medium->path, $medium->original_name)
                : $disk->response($medium->path, $medium->original_name, [
                    'Content-Type' => $medium->mime_type ?: 'application/octet-stream',
                ]);
        }
    }
}
