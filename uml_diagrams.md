# Modélisation UML - SmartSchedule

Ce document regroupe les diagrammes UML (Cas d'utilisation, Classes, Séquences) ainsi que les tableaux de scénarios demandés pour le projet de gestion d'emploi du temps **SmartSchedule**.

---

## 1. Diagramme de Cas d'Utilisation (Globale)

Les cas d'utilisation couvrent les interactions possibles de l'utilisateur avec le système (valable pour l'Application Web et Android).

```mermaid
flowchart LR
    User([Utilisateur connecté])

    %% Liste des Use Cases principaux
    Auth((Gérer son compte))
    TaskM((Gérer les tâches))
    CatM((Gérer les catégories))
    DispM((Gérer ses disponibilités))
    GenPlan((Générer le planning optimisé))
    ConsulterC((Consulter le calendrier))
    Stats((Consulter les statistiques et analyses))
    Notif((Gérer les notifications))
    
    %% Relations principales avec l'utilisateur
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
    TaskM -. "extend" .-> Recurrence((Gérer la récurrence))
    TaskM -. "extend" .-> Attachments((Ajouter des pièces jointes))
    DispM -.-> ValiderDisp((Valider les créneaux))
    GenPlan -. "prerequisite" .-> DispM
    GenPlan -. "include" .-> TaskM
    Notif -. "include" .-> Rappels((Recevoir des rappels automatiques))
```

**Notes :**
- La génération du planning nécessite que les disponibilités soient configurées au préalable
- Les tâches doivent exister avant de générer un planning
- Les attachments et la récurrence sont des extensions optionnelles

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
        +datetime updated_at
        --
        +authenticate() boolean
        +generateSchedule() void
        +getAnalytics() json
    }
    
    class Category {
        +int id
        +int user_id
        +string name
        +string color
        +datetime created_at
        +datetime updated_at
        --
        +getTasks() Task[]
    }
    
    class Task {
        +int id
        +int user_id
        +int category_id
        +int? parent_task_id
        +string title
        +string description
        +int duration_minutes
        +int priority
        +datetime deadline
        +enum status
        +boolean is_recurring
        +string recurrence_type
        +json recurrence_days
        +date recurrence_end
        +datetime created_at
        +datetime updated_at
        --
        +generateOccurrences() Task[]
        +updateStatus(status) void
        +getSchedule() Schedule
    }

    class TaskAttachment {
        +int id
        +int task_id
        +string file_path
        +string file_name
        +string file_type
        +int file_size
        +datetime created_at
    }
    
    class Availability {
        +int id
        +int user_id
        +enum day
        +time start_time
        +time end_time
        +boolean is_active
        +datetime created_at
        +datetime updated_at
        --
        +isValidTimeRange() boolean
        +getAvailableSlots() json
    }
    
    class Schedule {
        +int id
        +int task_id
        +int user_id
        +datetime start_time
        +datetime end_time
        +enum status
        +datetime created_at
        +datetime updated_at
        --
        +validateTimeSlot() boolean
        +moveToTime(datetime) void
        +markAsComplete() void
    }

    class Notification {
        +string id
        +int user_id
        +string type
        +int? task_id
        +int? schedule_id
        +json data
        +datetime read_at
        +datetime created_at
        --
        +markAsRead() void
        +sendNotification() void
    }
    
    %% Relations principales
    User "1" *-- "0..*" Category : crée >
    User "1" *-- "0..*" Task : possède >
    User "1" *-- "0..*" Availability : définit >
    User "1" *-- "0..*" Schedule : planifie >
    User "1" *-- "0..*" Notification : reçoit >
    
    %% Relations entités
    Category "1" *-- "0..*" Task : classifie >
    Task "1" -- "0..1" Schedule : génère >
    Task "1" *-- "0..*" TaskAttachment : contient >
    Task "1" *-- "0..*" Task : génère (récurrences) >
    Notification "1" -- "0..1" Task : notifie >
    Notification "1" -- "0..1" Schedule : alerte >
```

**Relations expliquées :**
- Un utilisateur peut avoir plusieurs tâches, catégories, disponibilités et planifications
- Une tâche peut avoir plusieurs attachments et générer des occurrences récurrentes
- Un planning (Schedule) est généré à partir d'une tâche
- Les notifications sont liées à des tâches ou des planifications

---

## 3. Diagrammes de Séquences

### Séquence 1 : Processus d'Authentification (via API Laravel)

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant M as Application (Axios)
    participant A as API Laravel (AuthController)
    participant D as Base de Données
    participant S as Session Storage

    U->>M: Saisit e-mail et mot de passe
    M->>A: POST /api/login (credentials)
    activate A
    
    A->>D: Requête SELECT users WHERE email = ?
    activate D
    D-->>A: User found ou null
    deactivate D
    
    alt Utilisateur trouvé
        A->>A: Hash::check(password, user.password)
        alt Mot de passe correct
            A->>D: Génère et sauvegarde token Sanctum
            activate D
            D-->>A: Token créé
            deactivate D
            A-->>M: HTTP 200 + {user, token}
            M->>S: Sauvegarde Token en AsyncStorage
            M-->>U: ✓ Redirection Dashboard
        else Mot de passe incorrect
            A-->>M: HTTP 401 Unauthorized
            M-->>U: ✗ Affiche "Identifiants invalides"
        end
    else Utilisateur inexistant
        A-->>M: HTTP 401 Unauthorized
        M-->>U: ✗ Affiche "Compte non trouvé"
    end
    
    deactivate A
```

---

### Séquence 2 : Génération Intelligente du Planning

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant M as Application (Web/Android)
    participant A as API Laravel (ScheduleController)
    participant E as Moteur d'Optimisation
    participant D as Base de Données

    U->>M: Clique sur "Générer le planning"
    M->>A: POST /api/schedule/generate
    activate A
    
    %% Récupération des données
    A->>D: SELECT tasks WHERE user_id = ? AND status = 'todo'<br/>ORDER BY priority DESC, deadline ASC
    activate D
    D-->>A: Liste des tâches à planifier
    deactivate D
    
    A->>D: SELECT availabilities WHERE user_id = ? AND is_active = true
    activate D
    D-->>A: Plages horaires disponibles
    deactivate D
    
    %% Optimisation
    A->>E: Appel algorithme d'optimisation(tasks, availabilities)
    activate E
    E->>E: Validation des contraintes
    E->>E: Tri par priorité et deadline
    E->>E: Allocation des créneaux
    E->>E: Vérification des chevauchements
    E->>E: Ajustement des pauses
    
    alt Allocation réussie
        E-->>A: schedules[] validés
        deactivate E
        
        A->>D: DELETE schedules WHERE user_id = ? (Réinitialisation)
        activate D
        D-->>A: Confirmation
        deactivate D
        
        A->>D: INSERT INTO schedules (Nouveaux créneaux)
        activate D
        D-->>A: Confirmation + IDs
        deactivate D
        
        A-->>M: HTTP 200 + {schedules[], success: true}
        M->>M: Met à jour le calendrier
        M-->>U: ✓ Planning généré avec succès
        
    else Impossible d'allouer toutes les tâches
        E-->>A: {schedules[], warnings[], unscheduled[]}
        deactivate E
        
        A->>D: INSERT INTO schedules (Créneaux partiels)
        activate D
        D-->>A: Confirmation
        deactivate D
        
        A-->>M: HTTP 200 + {schedules[], warnings, unscheduled}
        M-->>U: ⚠ Planning généré partiellement (tâches non planifiées)
    end
    
    deactivate A
```

---

### Séquence 3 : Notification de Rappel Automatique (Cron Job / Scheduler)

```mermaid
sequenceDiagram
    participant C as Cron / Scheduler Laravel
    participant CMD as Artisan Command (CheckTaskDeadlines)
    participant D as Base de Données
    participant N as Laravel Notification Service
    participant Q as Queue (Redis/Database)
    participant U as Utilisateur (Web/Mobile)

    C->>CMD: Exécute toutes les heures (0 * * * *)
    activate CMD
    
    CMD->>D: SELECT tasks WHERE deadline BETWEEN now AND now + 24h<br/>AND notified = false
    activate D
    D-->>CMD: Liste des tâches urgentes
    deactivate D
    
    CMD->>CMD: Filtrer les tâches pertinentes
    
    loop Pour chaque tâche
        CMD->>Q: Enqueue TaskDeadlineNotification(task, user)
        activate Q
        Q->>Q: Ajoute à la queue
        deactivate Q
        
        CMD->>D: UPDATE tasks SET notified = true WHERE id = ?
        activate D
        D-->>CMD: Confirmation
        deactivate D
    end
    
    Q->>N: Process notification depuis la queue
    activate N
    
    alt Via Broadcast (temps réel)
        N->>U: Broadcast notification (WebSocket)
        U-->>N: Notification affichée instantanément
    else Via Push Notification (mobile)
        N->>N: Générer push payload
        N->>U: Envoyer via Firebase/APNs
        U-->>N: ✓ Notification reçue
    end
    
    N->>D: INSERT INTO notifications (Historique)
    activate D
    D-->>N: Confirmation
    deactivate D
    
    deactivate N
    
    CMD-->>C: ✓ Terminé
    deactivate CMD
```

---

### Séquence 4 : Changement de Statut de Tâche (Drag & Drop)

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant M as Application (Interface)
    participant A as API Laravel (TaskController)
    participant D as Base de Données
    participant B as Broadcasting

    U->>M: Déplace tâche vers colonne "done"
    M->>M: Optimistic update (UI change)
    M->>A: PATCH /api/tasks/:id {status: 'done'}
    activate A
    
    A->>D: UPDATE tasks SET status = 'done' WHERE id = ? AND user_id = ?
    activate D
    D-->>A: Confirmation
    deactivate D
    
    A->>D: SELECT tasks WHERE id = ?
    activate D
    D-->>A: Updated task
    deactivate D
    
    alt Mise à jour réussie
        A->>B: Broadcast TaskStatusChanged(task)
        A-->>M: HTTP 200 + updated_task
        M->>M: Confirme l'update
        M-->>U: ✓ Tâche marquée comme complétée
        
    else Erreur de mise à jour
        A-->>M: HTTP 400/404 Error
        M->>M: Rollback à l'état précédent
        M-->>U: ✗ Erreur : impossible de mettre à jour
    end
    
    deactivate A
```

---

## 4. Tableau de Scénarios d'Utilisation

Voici la liste des scénarios d'utilisation principaux qui cadrent les tests fonctionnels et l'expérience utilisateur.

| ID | Cas d'Utilisation | Pré-condition | Scénario Nominal (Succès) | Scénario Alternatif (Échec / Exception) | Post-condition |
|:---|:---|:---|:---|:---|:---|
| **SC01** | Connexion à l'application | L'utilisateur possède un compte existant. | L'utilisateur saisit ses identifiants valides (email/password). L'API vérifie les credentials, génère un token JWT/Sanctum. L'utilisateur est redirigé vers le Dashboard. | (A) Email inexistant → Message "Compte non trouvé". (B) Mot de passe incorrect → Message "Identifiants invalides". (C) Serveur indisponible → Message "Erreur de connexion". | L'utilisateur est authentifié, le token est stocké en AsyncStorage, accès à tous les cas d'utilisation accordé. |
| **SC02** | Création d'une tâche | Utilisateur authentifié sur le Dashboard. | L'utilisateur remplit le formulaire : Titre, Description (optionnel), Catégorie, Durée (min), Priority, Deadline. Soumet le formulaire. La tâche est insérée en base de données avec status='todo'. La liste des tâches se met à jour. | (A) Titre vide → Message "Le titre est requis". (B) Durée négative → Message "Durée invalide". (C) Deadline passée → Message d'avertissement "La deadline est passée". (D) Erreur base de données → Rollback, message "Erreur lors de la création". | La tâche apparaît dans la liste avec status='todo', prête à être planifiée. |
| **SC03** | Configuration des disponibilités | Utilisateur authentifié sur la page Disponibilités. | L'utilisateur sélectionne un jour (ex: Lundi), configure les heures (09:00-17:00) avec optionnellement des pauses. Clique "Sauvegarder". Les créneaux sont insérés en base. L'interface affiche les heures configurées. | (A) Heure de fin antérieure à l'heure de début → Message "Heure de fin invalide". (B) Créneau chevauche un existant → Option de remplacer/fusionner. (C) Erreur réseau → Message "Impossible de sauvegarder". | Les disponibilités sont persistées en base, le planning futur tiendra compte de ces créneaux. |
| **SC04** | Génération du Planning | Utilisateur possède des tâches `todo` et des disponibilités valides configurées. | L'utilisateur clique sur "Générer le planning". L'API combine les tâches (triées par priorité/deadline) et les disponibilités. L'algorithme alloue chaque tâche à un créneau disponible. Les planifications sont insérées en base. Le calendrier se met à jour avec les tâches planifiées. | (A) Pas de tâche `todo` → Message "Aucune tâche à planifier". (B) Pas de disponibilités → Message "Configurez d'abord vos disponibilités". (C) Impossible d'allouer toutes les tâches → Planning partiel, liste des tâches non planifiées affichée. | Les tâches planifiées passent à status='scheduled', visibles dans le calendrier avec horaires définis. |
| **SC05** | Changement de statut de tâche (Drag & Drop) | L'utilisateur possède une tâche planifiée dans le calendrier. | L'utilisateur déplace la tâche de la colonne "scheduled" à "done" (drag & drop). L'interface met à jour optimistiquement. L'API envoie PATCH /api/tasks/:id {status: 'done'}. La base de données enregistre la modification. | (A) Déplacement annulé/invalide → Tâche revient à sa position initiale. (B) Erreur réseau lors du PATCH → Rollback, notification d'erreur. (C) Conflit (tâche supprimée entre-temps) → Message "Tâche inexistante". | La tâche est marquée comme complétée (status='done'), l'indicateur visuel (couleur) repasse au vert, elle disparaît de la colonne "scheduled". |
| **SC06** | Configuration de la récurrence | L'utilisateur crée ou modifie une tâche. | L'utilisateur active l'option "Tâche récurrente". Sélectionne le type : Quotidien, Hebdomadaire, Mensuel. Pour Hebdomadaire : sélectionne les jours (ex: Lundi, Mercredi). Configure la date de fin (optionnel). Soumet. La tâche est marquée is_recurring=true, recurrence_type='weekly', recurrence_days=['Monday','Wednesday']. | (A) Aucun jour sélectionné (Hebdo/Mensuel) → Message "Sélectionnez au moins un jour". (B) Date de fin antérieure à aujourd'hui → Message "Date de fin invalide". (C) Récurrence infinie sans fin définie → Demande confirmation. | La tâche principale est créée, des occurrences sont générées automatiquement pour la prochaine période (ex: 12 semaines), chacune planifiable individuellement. |
| **SC07** | Consultation des Analytics | Utilisateur authentifié avec historique de tâches (au minimum quelques tâches terminées). | L'utilisateur ouvre l'onglet "Analytics". L'API calcule : (1) Taux de complétion (done/total), (2) Temps moyen par catégorie, (3) Tâches en retard (deadline passée + status != 'done'), (4) Tendances temporelles. Les graphiques et statistiques s'affichent. | (A) Aucune tâche en historique → Message "Pas assez de données" avec un graphique vide. (B) Serveur ne peut calculer les stats → Message "Erreur lors du calcul des statistiques". (C) Données incohérentes en base → Affiche les données disponibles avec warning. | L'utilisateur visualise ses performances, tendances de productivité, points faibles et points forts. |
| **SC08** | Réception de Notification de Rappel | Une tâche approche de sa deadline (< 24h) et le serveur exécute le Cron Job (CheckTaskDeadlines). | Le Cron détecte la tâche. Crée une notification (type='task_deadline_reminder'). Envoie un Broadcast (temps réel) et/ou Push Notification (mobile). L'utilisateur voit une notification sur son dashboard ou reçoit une alerte mobile. L'historique de notification est enregistré. | (A) Tâche déjà notifiée (notified=true) → Skipped, aucune nouvelle notification. (B) Utilisateur a désactivé les notifications → Notification créée mais pas d'alerte enviée. (C) Service de notification indisponible → Notification enregistrée, tentative d'envoi ultérieure via Queue. | L'utilisateur est alerté de l'urgence de la tâche, peut décider de l'action (compléter, reporter, supprimer). Notification marquée comme lue après consultation. |

---

## 5. Notes d'Implémentation Laravel

### Validation des Contraintes
- **Task.duration_minutes** : Doit être > 0 et ≤ 1440 (24h)
- **Task.priority** : Valeurs acceptées : 1 (basse), 2 (moyenne), 3 (haute)
- **Schedule** : Vérifier absence de chevauchements pour le même utilisateur
- **Availability** : start_time < end_time, pas de doublons par (user_id, day)

### Endpoints API Clés
- `POST /api/login` → Authentification
- `POST /api/tasks` → Créer une tâche
- `GET /api/tasks` → Lister les tâches
- `PATCH /api/tasks/{id}` → Modifier une tâche
- `POST /api/availabilities` → Configurer disponibilités
- `POST /api/schedule/generate` → Générer le planning
- `GET /api/analytics` → Récupérer les statistiques
- `GET /api/notifications` → Lister les notifications

### Cron Jobs
- `schedule:run` → Exécute CheckTaskDeadlines toutes les heures (à configurer dans `app/Console/Kernel.php`)

---

## 6. Considérations de Sécurité & Performance

✅ **Authentification** : Token Sanctum avec expiration  
✅ **Validation** : Côté serveur (Laravel Rules) + côté client (React/Vue)  
✅ **Autorisation** : Vérifier user_id sur chaque requête (policy)  
✅ **Rate Limiting** : Throttle sur /api/schedule/generate  
✅ **Caching** : Cache les disponibilités & tâches avec TTL  
✅ **Index Base de Données** : Créer index sur (user_id, status), (user_id, deadline)

