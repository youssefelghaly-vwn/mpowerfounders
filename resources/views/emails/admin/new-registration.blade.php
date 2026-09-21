<x-mail.layout
    title="New account request"
    eyebrow="Needs review"
    heading="New account request"
    :preheader="$user->name . ' signed up and is waiting for activation.'"
    :cta-url="$reviewUrl"
    cta-label="Review account">

    <x-mail.paragraph>
        Someone signed up and is sitting in the pending queue. They can't sign in or upload anything until
        the account is activated.
    </x-mail.paragraph>

    <x-mail.panel :rows="[
        'Name' => $user->name,
        'Email' => $user->email,
        'Company' => $user->company,
        'Phone' => $user->phone,
        'About' => $user->about,
        'Submitted' => $user->created_at?->format('j M Y, H:i'),
    ]" />

</x-mail.layout>
