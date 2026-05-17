<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="auth-logo-text">SmartSchedule</span>
        </div>

        <h2 style="text-align:center;font-size:1.3rem;font-weight:700;color:white;margin:0 0 6px;">Connexion</h2>
        <p style="text-align:center;color:rgba(255,255,255,0.4);font-size:0.85rem;margin:0 0 28px;">Bienvenue ! Connectez-vous pour accéder à votre planning.</p>

        <x-auth-session-status class="status-msg" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" x-data="{ showPass: false }">
            @csrf

            <div class="auth-form-group">
                <label for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="vous@exemple.com">
                @error('email')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="auth-form-group">
                <label for="password">Mot de passe</label>
                <div style="position:relative;">
                    <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#aeaeb2;padding:4px;">
                        <svg x-show="!showPass" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="showPass" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;color:#6c6c70;font-size:0.85rem;">
                    <input type="checkbox" name="remember" id="remember_me">
                    Se souvenir de moi
                </label>
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                @endif
            </div>

            <button type="submit" class="btn-submit">Se connecter →</button>

            <p style="text-align:center;margin-top:20px;font-size:0.85rem;color:#6c6c70;">
                Pas encore de compte ?
                <a class="auth-link" href="{{ route('register') }}">S'inscrire</a>
            </p>
        </form>
    </div>
</x-guest-layout>
