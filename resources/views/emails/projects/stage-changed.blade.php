<x-mail.layout
    title="Project update"
    eyebrow="Production update"
    :heading="$project->title . ' is now in ' . ($history->toStage?->name ?? 'a new stage')"
    :preheader="$project->reference . ' moved to ' . ($history->toStage?->name ?? 'a new stage')"
    :cta-url="$projectUrl"
    cta-label="View your project">

    <x-mail.paragraph>
        Hi {{ $project->client->name }}, your project has moved forward.
    </x-mail.paragraph>

    <x-mail.panel :rows="[
        'Reference' => $project->reference,
        'Previous stage' => $history->fromStage?->name ?? 'Just submitted',
        'Now in' => $history->toStage?->name,
    ]" />

    @if ($history->toStage?->client_message)
        <x-mail.paragraph>{{ $history->toStage->client_message }}</x-mail.paragraph>
    @endif

    @if ($history->note)
        <x-mail.paragraph>
            <strong style="color:#0d0d0f;">Note from the team:</strong><br>
            {{ $history->note }}
        </x-mail.paragraph>
    @endif

</x-mail.layout>
