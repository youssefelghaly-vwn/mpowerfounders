<x-guest-layout title="Application received">
    <h1 class="auth-title">Application received</h1>
    <p class="auth-sub">
        Thanks — we've got your details and sent you a confirmation email.
    </p>

    <p style="font-size:14.5px;line-height:1.6;color:var(--graphite-dark);margin-bottom:24px;">
        Every account is reviewed by a person before it goes live, so there's nothing else for you to do right now.
        As soon as yours is activated we'll email you again — that message is your cue to sign in and upload your
        first podcast or video.
    </p>

    <a href="{{ route('login') }}" class="btn-gold auth-submit" style="display:inline-flex;text-align:center;">
        Back to sign in
    </a>
</x-guest-layout>
