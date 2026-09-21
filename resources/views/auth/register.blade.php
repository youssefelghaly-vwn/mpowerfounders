<x-guest-layout title="Apply for access">
    <h1 class="auth-title">Apply for access</h1>
    <p class="auth-sub">
        Tell us a little about what you make. We review every application by hand and email you the moment
        your account is live.
    </p>

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
            <label for="company">Company <span style="text-transform:none;letter-spacing:0;">(optional)</span></label>
            <input id="company" type="text" name="company" value="{{ old('company') }}" autocomplete="organization">
            @error('company') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="phone">Phone <span style="text-transform:none;letter-spacing:0;">(optional)</span></label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel">
            @error('phone') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="about">What do you want to make?</label>
            <textarea id="about" name="about" rows="3"
                      placeholder="Podcast, founder interviews, short-form clips…">{{ old('about') }}</textarea>
            @error('about') <p class="auth-error">{{ $message }}</p> @enderror
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

        <button type="submit" class="btn-gold auth-submit">Send application</button>
    </form>

    <p class="auth-foot">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </p>
</x-guest-layout>
