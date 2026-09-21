<x-guest-layout title="Forgot password">
    <h1 class="auth-title">Forgot password</h1>
    <p class="auth-sub">We'll email you a link to reset it.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-gold auth-submit">Email reset link</button>
    </form>

    <p class="auth-foot">
        <a href="{{ route('login') }}">Back to sign in</a>
    </p>
</x-guest-layout>