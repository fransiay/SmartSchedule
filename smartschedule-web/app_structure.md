   # Structure du Dossier `app` - SmartSchedule

Ce fichier explique le rôle de chaque sous-dossier présent dans le répertoire `app`.

## 1. `Http`
Le cœur des interactions Web et API.
- **Controllers/** : Contient les classes qui traitent les requêtes entrantes, interagissent avec les modèles et renvoient des réponses.
- **Middleware/** : Contient les filtres de requêtes (ex: vérification de l'authentification).
- **Kernel.php** : Configure les middlewares de l'application.

## 2. `Models`
Le répertoire des entités de données (Eloquent Models).
- Chaque fichier correspond à une table de la base de données (User, Task, Schedule, Availability, Category).
- C'est ici que l'on définit les relations entre les données (ex: un utilisateur possède plusieurs tâches).

## 3. `Services`
Logique métier personnalisée.
- **SmartScheduleService.php** : Contient l'algorithme complexe de génération d'emploi du temps. Ce dossier sépare la logique pure du code des contrôleurs pour une meilleure maintenance.

## 4. `Console`
Commandes Artisan personnalisées.
- **Commands/** : Où vous créez vos propres outils en ligne de commande utilisables avec `php artisan`.

## 5. `Providers`
Bootsrapping de l'application.
- Les classes ici servent à enregistrer des services, lier des abstractions à des implémentations et configurer les composants de base de Laravel au démarrage.

## 6. `Exceptions`
Gestion globale des erreurs.
- **Handler.php** : Permet de définir comment l'application doit réagir face à certaines erreurs (redirections, logs spécifiques, etc.).

## 7. `View`
Composants d'interface (Backend).
- Contient les classes PHP associées aux composants Blade (si utilisés) pour gérer la logique d'affichage complexe.
