<x-guest-layout title="Sign in">
    <h1 class="auth-title">Sign in</h1>
    <p class="auth-sub">Welcome back — pick up where you left off.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-row">
            <label class="auth-check">
                <input type="checkbox" name="remember">
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="link-underline">Forgot?</a>
        </div>

        <button type="submit" class="btn-gold auth-submit">Sign in</button>
    </form>

    <p class="auth-foot">
        Don't have an account? <a href="{{ route('register') }}">Register</a>
    </p>
</x-guest-layout>