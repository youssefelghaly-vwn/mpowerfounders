<x-mail.layout
    title="Application received"
    eyebrow="Application received"
    heading="Thanks, {{ $user->name }} — we've got it"
    preheader="Your MPower Founders application is with our team for review."
    footnote="Questions in the meantime? Just reply to this email.">

    <x-mail.paragraph>
        Your application for <strong style="color:#0d0d0f;">{{ $user->email }}</strong> has landed with our team.
        Accounts are reviewed by a person before they go live, so there's nothing else for you to do right now.
    </x-mail.paragraph>

    <x-mail.panel :rows="[
        'Name' => $user->name,
        'Email' => $user->email,
        'Company' => $user->company,
        'Submitted' => $user->created_at?->format('j M Y, H:i'),
    ]" />

    <x-mail.paragraph>
        As soon as your account is activated we'll email you again — that message is your cue to sign in and
        start uploading podcasts and video for us to work on.
    </x-mail.paragraph>

</x-mail.layout>
