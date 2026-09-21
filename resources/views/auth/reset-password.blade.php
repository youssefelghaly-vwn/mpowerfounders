<x-guest-layout title="Reset password">
    <h1 class="auth-title">Reset password</h1>
    <p class="auth-sub">Choose a new password below.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-gold auth-submit">Reset password</button>
    </form>
</x-guest-layout>