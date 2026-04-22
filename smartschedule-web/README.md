# SmartSchedule 📅✨

SmartSchedule est une application de gestion de temps intelligente. Conçue pour optimiser votre productivité, elle ne se contente pas d'être un énième gestionnaire de tâches (To-Do List). Elle prend vos tâches, vos priorités, et vos disponibilités pour **générer automatiquement un planning optimisé**.

> **Note** : Ce dépôt contient la partie Backend (API) et Frontend Web (Laravel/Blade). Une version mobile Android (React Native/Expo) a été pensée en parallèle pour interagir avec la même API.

## Fonctionnalités Principales

*   **Tableau de bord (Dashboard)** : Résumé de votre journée, indicateurs clés (KPIs) de complétion et un graphique circulaire (Chart.js) montrant l'avancée de vos tâches (À faire, En cours, Terminées).
*   **Gestion des Tâches (Tasks)** : Création de tâches avec durée estimée, statut, priorité, et date limite (deadline).
*   **Catégories (Categories)** : Classement des tâches par thématiques.
*   **Disponibilités (Availabilities)** : Interface pour définir vos plages horaires de travail ("Je suis disponible le Lundi de 09h à 17h, le Mardi de 14h à 22h", etc.).
*   **Autopilote d'Agenda (Générateur de Planning)** : L'algorithme se charge de répartir intelligemment toutes vos tâches en attente dans vos disponibilités.
*   **Calendrier Interactif v2 (FullCalendar)** : Visualisez en arrière-plan vos plages horaires disponibles (en gris clair), au-dessus desquelles viennent flotter les tâches générées par l'algorithme. Le calendrier vous téléporte automatiquement à la semaine de votre première tâche générée.

## L'Algorithme "Min-Load Balancer" 🧠

L'une des forces majeures de SmartSchedule est son **Algorithme de Répartition** codé en PHP (`SmartScheduleService`). 
Au lieu d'utiliser une méthode gourmande basique (qui remplirait le Lundi à 100% avant de toucher au Mardi), l'application utilise une stratégie de **Load-Balancing (Équilibrage de charge)** :

1.  **Regroupement par jour** : Le système analyse vos 30 prochains jours en fonction de vos disponibilités.
2.  **Attribution Min-Load** : Pour chaque tâche (triée par urgence et priorité), l'algorithme cherche la journée libre qui **cumule le moins d'heures de travail**. La tâche y est assignée.
3.  **Découpage Anti-Épuisement** : Si une tâche dure plus de 2 heures, elle est plafonnée `120 minutes` par jour. L'algorithme coupera la tâche en deux et assignera le reste de la charge sur une *autre* journée, forçant ainsi de l'étalement (spread) naturel.

## Stack Technique & Architecture

L'application a été construite autour d'un socle robuste, tout en gardant une interface premium (Thème Light/Grey minimaliste façon Apple/Notion).

*   **Backend** : Laravel 11.x (PHP) utilisant l'authentification `Sanctum` (Cookies de session pour le web).
*   **Base de Données** : SQLite local (configurable pour MySQL). L'ORM `Eloquent` gère les modèles (User, Task, Category, Availability, Schedule).
*   **API** : Architecture hybride. Des routes `/web-api/*` gèrent spécifiquement l'interface Blade avec un retour 100% JSON (comme une Single Page Application).
*   **Frontend Web** : 
    *   **Blade** (Moteur de template Laravel).
    *   **Alpine.js** (Framework réactif hyper-léger pour gérer l'ouverture de modals, la soumission asynchrone des requêtes `fetch`, le rendu `x-show`/`x-for`).
    *   **Styling** : Du CSS Vanilla premium, sans fioritures mais utilisant des variables structurelles pointues (effets hover poussés, glassmorphism, flexbox responsives).
    *   **Chart.js** : Graphiques d'analyses statistiques intégrés au dashboard.
    *   **FullCalendar.js** : Interface avancée de calendrier.

## Installation 🚀

1.  Clonez ce dépôt.
2.  Installez les dépendances PHP et Node :
    ```bash
    composer install
    npm install
    ```
3.  Préparez le fichier d'environnement et générez la clé d'application Laravel :
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  Configurez votre base de données dans `.env` (ex: `DB_CONNECTION=sqlite`). Puis, exécutez les migrations :
    ```bash
    php artisan migrate
    ```
5.  Lancez les serveurs de développement :
    ```bash
    npm run dev
    php artisan serve
    ```
6.  Ouvrez votre navigateur à l'adresse `http://127.0.0.1:8000` et commencez à organiser votre temps !
