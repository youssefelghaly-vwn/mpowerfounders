<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Disk
    |--------------------------------------------------------------------------
    |
    | Where uploaded podcasts, videos and deliverables are stored. In every
    | real environment this is the 's3' disk — in local development that is
    | MinIO, which speaks the S3 API (set AWS_ENDPOINT and
    | AWS_USE_PATH_STYLE_ENDPOINT=true; see .env.example).
    |
    | The disk name is recorded on each project_media row, so switching this
    | later does not orphan files already uploaded.
    |
    */

    'disk' => env('MEDIA_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    |
    | Validation caps, in kilobytes. PHP's own upload_max_filesize and
    | post_max_size must be at least as large or the request never reaches
    | Laravel's validator.
    |
    */

    'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 2048 * 1024), // 2 GB

    'max_files_per_upload' => (int) env('MEDIA_MAX_FILES_PER_UPLOAD', 10),

    /*
    |--------------------------------------------------------------------------
    | Accepted Types
    |--------------------------------------------------------------------------
    |
    | Extensions accepted by the uploaders. Checked with the 'extensions'
    | rule alongside a MIME-group check, so a renamed executable does not
    | slip through on its extension alone.
    |
    */

    'allowed_extensions' => [
        // video
        'mp4', 'mov', 'm4v', 'avi', 'mkv', 'webm',
        // audio
        'mp3', 'wav', 'm4a', 'aac', 'flac', 'ogg',
        // supporting material
        'pdf', 'png', 'jpg', 'jpeg', 'gif', 'txt', 'docx', 'srt', 'vtt', 'zip',
    ],

    /*
    |--------------------------------------------------------------------------
    | Temporary URL Lifetime
    |--------------------------------------------------------------------------
    |
    | Minutes a signed object-store URL stays valid. Playback and downloads
    | go straight to S3/MinIO with one of these rather than streaming
    | multi-gigabyte files back through PHP.
    |
    */

    'temporary_url_minutes' => (int) env('MEDIA_TEMPORARY_URL_MINUTES', 30),

];
