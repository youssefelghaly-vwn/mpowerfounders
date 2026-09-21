<x-guest-layout title="Create account">
    <h1 class="auth-title">Create account</h1>
    <p class="auth-sub">A few details and you're in.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-gold auth-submit">Create account</button>
    </form>

    <p class="auth-foot">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </p>
</x-guest-layout>