<x-mail.layout
    title="Upload received"
    eyebrow="Upload received"
    :heading="'We\'ve got ' . $project->title"
    preheader="Your files are uploaded and queued with our production team."
    :cta-url="$projectUrl"
    cta-label="Track this project">

    <x-mail.paragraph>
        Thanks {{ $project->client->name }} — your files are safely uploaded and your project is now in our
        production pipeline.
    </x-mail.paragraph>

    <x-mail.panel :rows="[
        'Reference' => $project->reference,
        'Project' => $project->title,
        'Type' => $project->typeLabel(),
        'Files' => $project->media()->count() . ' file(s)',
        'Current stage' => $project->stage?->name ?? 'Queued',
    ]" />

    @if ($project->brief)
        <x-mail.paragraph>
            <strong style="color:#0d0d0f;">What you asked for:</strong><br>
            {{ $project->brief }}
        </x-mail.paragraph>
    @endif

    <x-mail.paragraph>
        We'll email you each time it moves to a new stage, and again when the finished files are ready to download.
    </x-mail.paragraph>

</x-mail.layout>
