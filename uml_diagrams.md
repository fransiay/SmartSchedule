# Modélisation UML - SmartSchedule

Ce document regroupe les diagrammes UML (Cas d'utilisation, Classes, Séquences) ainsi que les tableaux de scénarios demandés pour le projet de gestion d'emploi du temps **SmartSchedule**.

---

## 1. Diagramme de Cas d'Utilisation (Globale)

Les cas d'utilisation couvrent les interactions possibles de l'utilisateur avec le système (valable pour l'Application Web et Android).

```mermaid
flowchart LR
    User([Utilisateur connecté])

    %% Liste des Use Cases
    Auth((Gérer son compte))
    TaskM((Gérer les tâches))
    CatM((Gérer les catégories))
    DispM((Gérer ses disponibilités))
    GenPlan((Générer le planning optimisé))
    ConsulterC((Consulter le calendrier))
    Stats((Consulter les statistiques))
    Notif((Consulter ses notifications))
    
    %% Inclusions (extends/includes représentés par liens)
    User --> Auth
    User --> TaskM
    User --> CatM
    User --> DispM
    User --> GenPlan
    User --> ConsulterC
    User --> Stats
    User --> Notif

    %% Sous-cas optionnels ou inclus
    Auth -.-> Inscription((S'inscrire))
    Auth -.-> Connexion((Se connecter))
    TaskM -. "include" .-> GererPriorites((Définir priorités et durées))
    GenPlan -. "include" .-> DispM
```

---

## 2. Diagramme de Classes

Ce diagramme illustre les entités du système, leurs attributs clés et leurs relations (cardinalités d'après le MCD MySQL).

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string preferred_hours
        +int break_duration
        +datetime created_at
    }
    
    class Category {
        +int id
        +int user_id
        +string name
        +datetime created_at
    }
    
    class Task {
        +int id
        +int user_id
        +int category_id
        +string title
        +string description
        +int duration
        +enum priority
        +datetime deadline
        +enum status
        +datetime created_at
    }
    
    class Availability {
        +int id
        +int user_id
        +enum day
        +time start_time
        +time end_time
        +datetime created_at
    }
    
    class Schedule {
        +int id
        +int task_id
        +int user_id
        +datetime start_time
        +datetime end_time
        +datetime created_at
    }
    
    %% Relations
    User "1" *-- "0..*" Category : crée >
    User "1" *-- "0..*" Task : possède >
    User "1" *-- "0..*" Availability : définit >
    User "1" *-- "0..*" Schedule : a pour >
    Category "1" *-- "0..*" Task : classifie >
    Task "1" -- "0..1" Schedule : planifié en >
```

---

## 3. Diagrammes de Séquences

### Séquence 1 : Processus d'Authentification (via API)

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant M as Application (Axios)
    participant A as API Laravel (AuthController)
    participant D as Base de Données

    U->>M: Saisit e-mail et mot de passe
    M->>A: POST /api/login (credentials)
    activate A
    A->>D: Requête `users` où email = ?
    activate D
    D-->>A: User (ou inexistant) + hash $2y$...
    deactivate D
    A->>A: Auth::attempt() (Vérifie le hash)
    
    alt Identifiants corrects
        A->>D: Sauvegarde le nouveau token Sanctum
        activate D
        D-->>A: Return Token
        deactivate D
        A-->>M: HTTP 200 OK (User Object + Token Bearer)
        M->>M: Stocke le Token (AsyncStorage)
        M-->>U: Redirection vers Tableau de bord
    else Identifiants incorrects
        A-->>M: HTTP 401 Unauthorized
        M-->>U: Affiche message "Identifiants invalides"
    end
    deactivate A
```

### Séquence 2 : Génération Intelligente du Planning

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant M as Application (Web/Android)
    participant A as API Laravel (ScheduleEngine)
    participant D as Base de Données

    U->>M: Clique sur "Générer le planning"
    M->>A: POST /api/schedule/generate
    activate A
    
    A->>D: SELECT * FROM `tasks` WHERE status = 'todo' ORDER BY priority, deadline
    activate D
    D-->>A: Liste des Tâches (filtrée et triée)
    deactivate D
    
    A->>D: SELECT * FROM `availabilities` 
    activate D
    D-->>A: Plages horaires de l'utilisateur
    deactivate D
    
    A->>A: Algorithme d'optimisation (Validation des créneaux, chevauchements, pauses)
    
    A->>D: DELETE FROM `schedules` (Réinitialisation si demandé)
    A->>D: INSERT INTO `schedules` (Nouveaux créneaux validés)
    activate D
    D-->>A: Confirmation d'insertion
    deactivate D
    
    A-->>M: HTTP 200 OK + `schedules` (JSON)
    deactivate A
    
    M-->>U: Met à jour le Composant Calendrier
```

---

## 4. Tableau de Scénarios d'Utilisation

Voici la liste des scénarios d'utilisation principaux qui cadrent les tests fonctionnels et l'expérience utilisateur.

| ID | Cas d'Utilisation | Pré-condition | Scénario Nominal (Succès) | Scénario Alternatif (Échec / Exception) | Post-condition |
|:---|:---|:---|:---|:---|:---|
| **SC01** | Connexion à l'application | L'utilisateur possède un compte existant. | L'utilisateur saisit ses identifiants valides. L'API renvoie un Token JWT. L'utilisateur est redirigé vers l'Accueil. | E-mail ou mot de passe incorrect. Le système refuse l'accès et affiche une erreur. | Une session locale est activée (Token stocké). |
| **SC02** | Création d'une tâche | Utilisateur authentifié sur le Dashboard. | L'utilisateur remplit le formulaire avec un Titre, une Durée (min) et une Deadline. La tâche est insérée en base avec statut `todo`. | Le titre manque ou la durée est invalide. L'API renvoie un code 422 HTTP (Validation Error). | La tâche apparaît dans la liste des tâches à faire. |
| **SC03** | Configuration des disponibilités | Utilisateur authentifié. | L'utilisateur sélectionne un jour (ex: Lundi) et configure la plage 09:00 à 17:00. Il sauvegarde. | Une plage de fin arrive avant la plage de début. L'application bloque l'envoi. | La disponibilité est enregistrée et sera utilisée pour l'algoritme. |
| **SC04** | Génération du Planning | Utilisateur possède des tâches `todo` et des disponibilités valides. | L'utilisateur clique sur "Générer". L'API combine tâches et dispos, insère les données et renvoie le calendrier rempli. | L'utilisateur a 20h de tâches mais seulement 10h de disponibilités. Le serveur notifie que des tâches resteront non-placées. | Le planning visuel est rempli (Calendrier web et app rafraîchi). |
| **SC05** | Changement de statut de tâche (Drag & Drop) | L'utilisateur possède une tâche planifiée dans le calendrier. | L'utilisateur déplace la tâche dans la colonne `done`. L'application envoie une requête PUT. | Problème réseau. La Tâche revient à sa position initiale. | La tâche est marquée comme complétée et l'indicateur repasse au vert. |
