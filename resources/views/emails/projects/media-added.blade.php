<x-mail.layout
    title="New files ready"
    eyebrow="Files ready"
    :heading="'New files on ' . $project->title"
    :preheader="$media->count() . ' new file(s) are available on ' . $project->reference"
    :cta-url="$projectUrl"
    cta-label="Open your project"
    footnote="Files are streamed from secure storage — open the project to play or download them.">

    <x-mail.paragraph>
        Hi {{ $project->client->name }}, our team has published new files to your project
        <strong style="color:#0d0d0f;">{{ $project->reference }}</strong>.
    </x-mail.paragraph>

    <x-mail.panel :rows="collect($media)->mapWithKeys(fn ($file) => [
        $file->kindLabel() => $file->original_name . ' · ' . $file->humanSize(),
    ])->all()" />

</x-mail.layout>
