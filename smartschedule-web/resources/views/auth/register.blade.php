<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="auth-logo-text">SmartSchedule</span>
        </div>

        <h2 style="text-align:center;font-size:1.3rem;font-weight:700;color:#0f0f10;margin:0 0 6px;">Créer un compte</h2>
        <p style="text-align:center;color:#aeaeb2;font-size:0.85rem;margin:0 0 28px;">Rejoignez SmartSchedule et organisez votre temps intelligemment.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="auth-form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Jean Dupont">
                @error('name')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="auth-form-group">
                <label for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="vous@exemple.com">
                @error('email')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="auth-form-group">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 caractères">
                @error('password')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="auth-form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Répétez votre mot de passe">
                @error('password_confirmation')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-submit">Créer mon compte →</button>

            <p style="text-align:center;margin-top:20px;font-size:0.85rem;color:#6c6c70;">
                Déjà inscrit ?
                <a class="auth-link" href="{{ route('login') }}">Se connecter</a>
            </p>
        </form>
    </div>
</x-guest-layout>
