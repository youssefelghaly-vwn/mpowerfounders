<x-mail.layout
    title="Your account is live"
    eyebrow="Welcome aboard"
    heading="Your account is live, {{ $user->name }}"
    preheader="You can now sign in and upload your first project."
    :cta-url="$portalUrl"
    cta-label="Go to your dashboard"
    footnote="Use the same email and password you registered with. Forgot it? Use the 'Forgot password' link on the sign-in page.">

    <x-mail.paragraph>
        Your MPower Founders account has been activated. You can sign in with
        <strong style="color:#0d0d0f;">{{ $user->email }}</strong> and start sending us work straight away.
    </x-mail.paragraph>

    @if ($note)
        <x-mail.panel :rows="['A note from the team' => $note]" />
    @endif

    <x-mail.paragraph>
        Upload your raw podcast or video, tell us what you want done with it, and we'll take it from there —
        you'll get an email each time your project moves to a new stage of production.
    </x-mail.paragraph>

</x-mail.layout>
