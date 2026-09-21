<?php

namespace App\Http\Requests\Concerns;

/**
 * Shared upload rules for every screen that accepts media, so the size cap
 * and accepted types are configured once (config/media.php) rather than
 * drifting between the client and admin uploaders.
 */
trait ValidatesMediaUploads
{
    protected function mediaRules(bool $required = false): array
    {
        $extensions = (array) config('media.allowed_extensions', []);

        return [
            'files' => [$required ? 'required' : 'nullable', 'array', 'max:'.config('media.max_files_per_upload', 10)],
            'files.*' => [
                'file',
                'max:'.config('media.max_upload_kb', 2097152),
                'extensions:'.implode(',', $extensions),
                // Belt and braces: the extension list above is checked
                // against the claimed name, this against the sniffed type.
                'mimetypes:'.implode(',', $this->allowedMimeTypes()),
            ],
        ];
    }

    protected function mediaMessages(): array
    {
        $maxMb = round(((int) config('media.max_upload_kb', 2097152)) / 1024);

        return [
            'files.*.max' => "Each file must be {$maxMb} MB or smaller.",
            'files.*.extensions' => 'That file type is not supported. Upload video, audio, images, PDFs or subtitles.',
            'files.*.mimetypes' => 'That file type is not supported. Upload video, audio, images, PDFs or subtitles.',
        ];
    }

    /**
     * Wildcards cover the long tail of container/codec MIME types browsers
     * report for the same handful of extensions.
     */
    protected function allowedMimeTypes(): array
    {
        return [
            'video/*',
            'audio/*',
            'image/*',
            'application/pdf',
            'application/zip',
            'application/x-zip-compressed',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'text/vtt',
            'application/x-subrip',
        ];
    }
}
