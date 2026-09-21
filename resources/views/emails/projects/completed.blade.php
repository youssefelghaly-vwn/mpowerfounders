<x-mail.layout
    title="Your project is ready"
    eyebrow="Delivered"
    :heading="$project->title . ' is ready'"
    :preheader="'Your finished files for ' . $project->reference . ' are ready to download.'"
    :cta-url="$projectUrl"
    cta-label="Download your files">

    <x-mail.paragraph>
        Hi {{ $project->client->name }} — we're done. Your finished files for
        <strong style="color:#0d0d0f;">{{ $project->reference }}</strong> are ready to play and download from
        your dashboard.
    </x-mail.paragraph>

    @if ($deliverables->isNotEmpty())
        <x-mail.panel :rows="$deliverables->mapWithKeys(fn ($file) => [
            $file->original_name => $file->humanSize(),
        ])->all()" />
    @endif

    <x-mail.paragraph>
        Need a change? Reply to this email and we'll pick the project back up.
    </x-mail.paragraph>

</x-mail.layout>
