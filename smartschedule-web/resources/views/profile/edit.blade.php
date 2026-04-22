<x-app-layout>
    <x-slot name="header">
        <p class="page-title">Mon Profil</p>
        <p class="page-subtitle">Gérez vos informations personnelles et votre mot de passe.</p>
    </x-slot>

    <div class="pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Profile info -->
            <div class="card" style="padding:32px;">
                <h3 style="font-size:1rem;font-weight:700;color:white;margin:0 0 24px;padding-bottom:16px;border-bottom:1px solid rgba(255,255,255,0.07);">Informations du profil</h3>
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Password -->
            <div class="card" style="padding:32px;">
                <h3 style="font-size:1rem;font-weight:700;color:white;margin:0 0 24px;padding-bottom:16px;border-bottom:1px solid rgba(255,255,255,0.07);">Changer le mot de passe</h3>
                @include('profile.partials.update-password-form')
            </div>

            <!-- Delete account -->
            <div class="card" style="padding:32px;border-color:rgba(239,68,68,0.2);">
                <h3 style="font-size:1rem;font-weight:700;color:#fca5a5;margin:0 0 24px;padding-bottom:16px;border-bottom:1px solid rgba(239,68,68,0.1);">Zone de danger</h3>
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>

    {{-- Override Breeze's white form styles for dark theme --}}
    <style>
        .profile-form label, x-input-label {
            color: rgba(255,255,255,0.5) !important;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .profile-form input[type=text],
        .profile-form input[type=email],
        .profile-form input[type=password] {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 10px !important;
            color: #e2e8f0 !important;
            padding: 10px 16px !important;
        }
        .profile-form input:focus {
            border-color: rgba(99,102,241,0.6) !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15) !important;
        }
        .profile-form button[type=submit] {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: white !important;
            border: none !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(99,102,241,0.4) !important;
        }
        .profile-form p.text-gray-600, 
        .profile-form p.text-sm {
            color: rgba(255,255,255,0.4) !important;
        }
    </style>
</x-app-layout>
