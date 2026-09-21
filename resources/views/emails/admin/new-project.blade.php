<x-mail.layout
    title="New upload"
    eyebrow="New upload"
    :heading="$project->title"
    :preheader="'New footage from ' . $project->client->name . ' (' . $project->reference . ')'"
    :cta-url="$projectUrl"
    cta-label="Open in admin">

    <x-mail.paragraph>
        New material has landed in the pipeline.
    </x-mail.paragraph>

    <x-mail.panel :rows="[
        'Reference' => $project->reference,
        'Client' => $project->client->name . ' (' . $project->client->email . ')',
        'Type' => $project->typeLabel(),
        'Files' => $project->media()->count() . ' file(s)',
        'Stage' => $project->stage?->name ?? 'Unassigned',
        'Due' => $project->due_date?->format('j M Y'),
    ]" />

    @if ($project->brief)
        <x-mail.paragraph>
            <strong style="color:#0d0d0f;">Client brief:</strong><br>
            {{ $project->brief }}
        </x-mail.paragraph>
    @endif

</x-mail.layout>
