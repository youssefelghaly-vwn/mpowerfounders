<x-mail.layout
    title="Account access changed"
    eyebrow="Account update"
    heading="Your account access has been paused"
    preheader="Your MPower Founders account has been paused."
    footnote="Think this is a mistake? Reply to this email and we'll sort it out.">

    <x-mail.paragraph>
        Hi {{ $user->name }}, access to your MPower Founders account
        (<strong style="color:#0d0d0f;">{{ $user->email }}</strong>) has been paused, so you won't be able to
        sign in for now. Your projects and files are untouched.
    </x-mail.paragraph>

    @if ($note)
        <x-mail.panel :rows="['Reason' => $note]" />
    @endif

</x-mail.layout>
