<x-mail.layout
    title="Set your password"
    eyebrow="Welcome aboard"
    heading="Welcome to MPower Founders"
    preheader="Your account is ready — set a password to get started."
    :cta-url="$setPasswordUrl"
    cta-label="Set your password"
    :footnote="'This link expires in ' . $expiryMinutes . ' minutes. If it lapses, use the \'Forgot password\' link on the sign-in page to get a fresh one.'">

    <x-mail.paragraph>
        Hi {{ $user->name }}, we've set up an MPower Founders account for you under
        <strong style="color:#0d0d0f;">{{ $user->email }}</strong>. Choose a password and it's yours.
    </x-mail.paragraph>

    @if ($note)
        <x-mail.panel :rows="['A note from the team' => $note]" />
    @endif

    <x-mail.paragraph>
        Once you're in you can upload podcasts and video, describe what you need, and follow each project
        through production.
    </x-mail.paragraph>

</x-mail.layout>
