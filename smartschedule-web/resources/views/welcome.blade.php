<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartSchedule - Le planning intelligent qui s'adapte à vous</title>
    
    <!-- Chargement du CSS local compilé par Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f7f8] text-gray-900 font-sans antialiased overflow-x-hidden selection:bg-gray-200">
    
    <!-- Navbar / En-tête -->
    <nav class="absolute w-full px-6 py-6 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand text-white rounded-xl flex items-center justify-center font-bold text-xl shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="font-extrabold text-2xl tracking-tight text-brand">SmartSchedule</span>
            </div>
            
            <!-- Boutons Connexion / Inscription -->
            <div class="flex items-center gap-2 sm:gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-black transition">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-black px-4 py-2 transition hidden sm:inline-block">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-semibold bg-brand text-white px-5 py-2.5 rounded-xl shadow-md hover:bg-gray-800 transition transform hover:-translate-y-0.5">S'inscrire</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Section Principale (Hero) -->
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Effet de lueur en fond -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-gradient-to-b from-gray-200/50 to-transparent rounded-full blur-3xl -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-brand mb-6 leading-tight">
                Le planning qui s'adapte <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-700 to-gray-400">à votre rythme de vie.</span>
            </h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Fini le stress de l'organisation. SmartSchedule génère automatiquement votre emploi du temps en fonction de vos disponibilités et des priorités de vos tâches.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-brand text-white font-bold rounded-xl shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1">Commencer gratuitement</a>
                <a href="#features" class="px-8 py-4 bg-white text-brand border border-gray-200 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">Découvrir l'utilité</a>
            </div>
        </div>
    </section>

    <!-- Section Utilité (Fonctionnalités) -->
    <section id="features" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-4xl font-bold text-brand mb-4">Conçu pour maximiser votre productivité</h2>
                <p class="text-gray-500 text-lg">Une suite d'outils intelligents pour gérer votre temps sans aucun effort mental.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Carte 1 -->
                <div class="p-8 rounded-3xl bg-[#f7f7f8] border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-2xl group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-brand">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-brand">Génération Automatique</h3>
                    <p class="text-gray-600 leading-relaxed">Notre algorithme place vos tâches intelligemment dans votre semaine selon leur durée, leur priorité et leur date limite. Plus besoin de jouer à Tetris avec votre agenda.</p>
                </div>
                
                <!-- Carte 2 -->
                <div class="p-8 rounded-3xl bg-[#f7f7f8] border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-2xl group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-brand">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-brand">Écosystème Connecté</h3>
                    <p class="text-gray-600 leading-relaxed">Gérez vos tâches depuis l'interface Web sur votre ordinateur et retrouvez votre planning en temps réel dans votre poche grâce à l'application mobile Android synchronisée.</p>
                </div>
                
                <!-- Carte 3 -->
                <div class="p-8 rounded-3xl bg-[#f7f7f8] border border-gray-100 hover:shadow-xl transition duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-2xl group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-brand">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-brand">Respect de votre Temps</h3>
                    <p class="text-gray-600 leading-relaxed">Indiquez vos heures de disponibilité (travail, soirées, week-ends). SmartSchedule respecte vos pauses et ne planifie jamais rien en dehors de vos horaires choisis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pied de page (Call to Action) -->
    <section class="py-24 bg-brand text-center px-6 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-gray-800 rounded-full blur-3xl opacity-40"></div>
        <div class="relative z-10 max-w-3xl mx-auto">
            <h2 class="text-4xl font-extrabold text-white mb-6">Prêt à reprendre le contrôle de votre temps ?</h2>
            <p class="text-gray-400 mb-10 text-lg">Rejoignez SmartSchedule dès aujourd'hui et laissez l'intelligence artificielle organiser vos journées de manière optimale.</p>
            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-brand font-bold rounded-xl shadow-2xl hover:scale-105 transition duration-300 inline-block">
                Créer mon compte maintenant
            </a>
            
            <div class="mt-16 text-gray-500 text-sm">
                &copy; {{ date('Y') }} SmartSchedule Web & Android. Tous droits réservés.
            </div>
        </div>
    </section>

</body>
</html>
