SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expo_push_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_user_id_foreign` (`user_id`),
  CONSTRAINT `categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `parent_task_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration_minutes` int NOT NULL,
  `priority` int NOT NULL DEFAULT '3',
  `deadline` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todo',
  `modifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurrence_days` json DEFAULT NULL,
  `recurrence_end` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_user_id_foreign` (`user_id`),
  KEY `tasks_category_id_foreign` (`category_id`),
  KEY `tasks_parent_task_id_foreign` (`parent_task_id`),
  CONSTRAINT `tasks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_parent_task_id_foreign` FOREIGN KEY (`parent_task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `availabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `day` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `availabilities_user_id_foreign` (`user_id`),
  CONSTRAINT `availabilities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `task_id` bigint unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_user_id_foreign` (`user_id`),
  KEY `schedules_task_id_foreign` (`task_id`),
  CONSTRAINT `schedules_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `task_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `type` enum('image','audio','pdf','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_attachments_task_id_foreign` (`task_id`),
  CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Data for table `availabilities`
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (1, 3, 'Mon', '09:00', '17:00', '2026-04-16 14:52:45', '2026-04-16 14:52:45');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (2, 3, 'Tue', '14:00', '22:00', '2026-04-16 14:53:02', '2026-04-16 14:53:02');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (3, 2, 'Sun', '09:00', '17:00', '2026-04-19 14:16:51', '2026-04-19 14:16:51');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (4, 2, 'Mon', '09:00', '17:00', '2026-04-19 14:16:56', '2026-04-19 14:16:56');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (5, 2, 'Tue', '09:00', '17:00', '2026-04-19 14:17:00', '2026-04-19 14:17:00');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (6, 2, 'Wed', '09:00', '17:00', '2026-04-19 14:17:05', '2026-04-19 14:17:05');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (7, 2, 'Thu', '09:00', '17:00', '2026-04-19 14:17:07', '2026-04-19 14:17:07');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (8, 2, 'Fri', '09:00', '17:00', '2026-04-19 14:17:10', '2026-04-19 14:17:10');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (9, 2, 'Sat', '09:00', '17:00', '2026-04-19 14:17:19', '2026-04-19 14:17:19');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (10, 1, 'Sun', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (11, 1, 'Mon', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (12, 1, 'Tue', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (13, 1, 'Wed', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (14, 1, 'Thu', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (15, 1, 'Fri', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (16, 1, 'Sat', '09:00:00', '17:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (17, 3, 'Sat', '09:00', '17:00', '2026-04-23 03:51:11', '2026-04-23 03:51:11');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (18, 6, 'Mon', '09:00', '17:00', '2026-04-23 08:24:14', '2026-04-23 08:24:14');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (19, 4, 'Sun', '12:00', '16:00', '2026-04-23 08:30:49', '2026-04-23 08:30:49');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (20, 4, 'Mon', '21:00', '23:30', '2026-04-23 08:32:10', '2026-04-23 08:32:10');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (21, 4, 'Tue', '21:00', '23:30', '2026-04-23 08:32:30', '2026-04-23 08:32:30');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (22, 4, 'Wed', '12:00', '14:00', '2026-04-23 08:33:03', '2026-04-23 08:33:03');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (23, 4, 'Thu', '12:00', '14:00', '2026-04-23 08:33:52', '2026-04-23 08:33:52');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (24, 4, 'Thu', '21:00', '23:00', '2026-04-23 08:34:03', '2026-04-23 08:34:07');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (25, 4, 'Fri', '18:30', '22:00', '2026-04-23 08:35:52', '2026-04-23 08:35:52');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (26, 8, 'Sun', '09:00', '17:00', '2026-05-13 13:12:53', '2026-05-13 13:12:53');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (27, 8, 'Mon', '09:00', '17:00', '2026-05-13 13:12:55', '2026-05-13 13:12:55');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (28, 8, 'Tue', '09:00', '17:00', '2026-05-13 13:12:58', '2026-05-13 13:12:58');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (29, 8, 'Fri', '09:00', '17:00', '2026-05-13 13:13:01', '2026-05-13 13:13:01');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (30, 8, 'Thu', '09:00', '17:00', '2026-05-13 13:13:04', '2026-05-13 13:13:04');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (31, 8, 'Wed', '09:00', '17:00', '2026-05-13 13:13:10', '2026-05-13 13:13:10');
INSERT INTO `availabilities` (`id`, `user_id`, `day`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (32, 8, 'Sat', '09:00', '17:00', '2026-05-13 13:13:12', '2026-05-13 13:13:12');

-- Data for table `categories`
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (1, 2, 'course', '2026-04-13 13:52:37', '2026-04-13 13:52:37');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (2, 2, 'révision', '2026-04-13 13:52:48', '2026-04-13 13:52:48');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (3, 3, 'Travail', '2026-04-16 14:43:07', '2026-04-16 14:43:07');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (4, 3, 'course', '2026-04-16 14:43:20', '2026-04-16 14:43:20');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (5, 3, 'Révision', '2026-04-16 14:43:58', '2026-04-16 14:43:58');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (6, 2, 'cours', '2026-04-19 14:47:19', '2026-04-19 14:47:19');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (7, 4, 'Travail', '2026-04-23 08:23:37', '2026-04-23 08:23:37');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (8, 4, 'Course', '2026-04-23 08:23:53', '2026-04-23 08:23:53');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (9, 4, 'Révision', '2026-04-23 08:24:06', '2026-04-23 08:24:06');
INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES (10, 4, 'Rendez vous', '2026-04-23 08:24:27', '2026-04-23 08:24:27');

-- Data for table `notifications`
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('ab121c41-2edb-46d1-9a58-e51cfdd08231', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":23,\"title\":\"sortir les poubelles\",\"message\":\"Nouvelle t\\u00e2che cr\\u00e9\\u00e9e : sortir les poubelles\",\"type\":\"success\",\"action\":\"\\/tasks\"}', '2026-05-13 12:43:03', '2026-05-04 21:05:11', '2026-05-13 12:43:03');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('fbadfcdc-6815-46c4-8f2c-11bc832bc859', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":24,\"title\":\"Aller voir le gyn\\u00e9cologue\",\"message\":\"Nouvelle t\\u00e2che cr\\u00e9\\u00e9e : Aller voir le gyn\\u00e9cologue\",\"type\":\"success\",\"action\":\"\\/tasks\"}', '2026-05-10 21:24:07', '2026-05-04 21:12:20', '2026-05-10 21:24:07');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('0f4eb264-0ffd-4474-aea2-bceded79f6ed', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 7, '{\"task_id\":25,\"title\":\"Test Task Antigravity\",\"message\":\"Nouvelle t\\u00e2che cr\\u00e9\\u00e9e : Test Task Antigravity\",\"type\":\"success\",\"action\":\"\\/tasks\"}', NULL, '2026-05-10 19:33:48', '2026-05-10 19:33:48');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('661e84cc-7c81-40a8-b016-2bccd01f5eab', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":26,\"title\":\"operation zen\",\"message\":\"Nouvelle t\\u00e2che cr\\u00e9\\u00e9e : operation zen\",\"type\":\"success\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:27:48', '2026-05-11 14:27:48');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('93cef029-c290-4fcd-bb8f-876cc7b806f1', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"Statut de la t\\u00e2che \'TESTER L\'APPLI\' mis \\u00e0 jour : \\u00c0 faire\",\"type\":\"info\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:28:03', '2026-05-11 14:28:03');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('a16cd4f5-27bf-44b9-9e7b-7f9de01cab36', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"Statut de la t\\u00e2che \'TESTER L\'APPLI\' mis \\u00e0 jour : Termin\\u00e9e\",\"type\":\"success\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:28:08', '2026-05-11 14:28:08');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('ba3aa9c9-dc86-4797-bff9-d77096ba8d25', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":26,\"title\":\"operation zen\",\"message\":\"Statut de la t\\u00e2che \'operation zen\' mis \\u00e0 jour : En cours\",\"type\":\"info\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:28:12', '2026-05-11 14:28:12');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('03bd824a-91dc-44af-af10-40329c1b27c0', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 1, '{\"task_id\":10,\"title\":\"Test Task\",\"message\":\"\\u26a0\\ufe0f \\u00ab Test Task \\u00bb est en retard de 20 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('657e73dc-ed7d-441b-b3ab-e1ce662a093a', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":1,\"title\":\"aller faire les courses avant le pilate\",\"message\":\"\\u26a0\\ufe0f \\u00ab aller faire les courses avant le pilate \\u00bb est en retard de 16 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-11 14:53:44', '2026-05-11 14:37:27', '2026-05-11 14:53:44');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('afdadc50-cff1-461b-a525-812563927d8c', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":2,\"title\":\"Faire mon exercice d math\",\"message\":\"\\u26a0\\ufe0f \\u00ab Faire mon exercice d math \\u00bb est en retard de 28 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-11 14:53:56', '2026-05-11 14:37:27', '2026-05-11 14:53:56');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('2624188d-f1b6-41b2-aa30-a438108418e1', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":3,\"title\":\"Prendre rendez-vous pour faire ma prise de sang\",\"message\":\"\\u26a0\\ufe0f \\u00ab Prendre rendez-vous pour faire ma prise de sang \\u00bb est en retard de 27 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-11 15:02:36', '2026-05-11 14:37:27', '2026-05-11 15:02:36');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('4c20a6e7-a612-40c6-abe1-b13ac1f514aa', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":12,\"title\":\"R\\u00e9viser CEJM\",\"message\":\"\\u26a0\\ufe0f \\u00ab R\\u00e9viser CEJM \\u00bb est en retard de 18 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('d911dbaf-8402-42d7-91da-999e14007ccd', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":13,\"title\":\"acheter du pain\",\"message\":\"\\u26a0\\ufe0f \\u00ab acheter du pain \\u00bb est en retard de 18 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('768245c7-7de0-46f9-84e4-8c8094d25599', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":23,\"title\":\"sortir les poubelles\",\"message\":\"\\u26a0\\ufe0f \\u00ab sortir les poubelles \\u00bb est en retard de 6 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('5790dfa2-70f4-43f0-9241-5c014b48fe95', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":5,\"title\":\"MATHS\",\"message\":\"\\u26a0\\ufe0f \\u00ab MATHS \\u00bb est en retard de 20 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('1d0a64af-1f3f-4515-8c39-bbe2ebd01df1', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":6,\"title\":\"EXAMEN BTS\",\"message\":\"\\u26a0\\ufe0f \\u00ab EXAMEN BTS \\u00bb est en retard de 23 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('b6f38671-4297-4b2c-a4b4-ee51ad153996', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":24,\"title\":\"Aller voir le gyn\\u00e9cologue\",\"message\":\"\\u26a0\\ufe0f \\u00ab Aller voir le gyn\\u00e9cologue \\u00bb est en retard de 5 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('d5068db2-9a2c-474e-a2dd-5e04c688757b', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":26,\"title\":\"operation zen\",\"message\":\"\\ud83d\\udd34 \\u00ab operation zen \\u00bb expire aujourd\'hui ! Statut actuel : En cours.\",\"type\":\"warning\",\"action\":\"\\/tasks\",\"reminder_type\":\"today\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('f6cc12fb-3139-403e-8960-eadc7bf4d0e1', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":14,\"title\":\"lire\",\"message\":\"\\u26a0\\ufe0f \\u00ab lire \\u00bb est en retard de 12 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('0a55d25f-3d99-40c0-8dec-7197f940d93f', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":15,\"title\":\"faire ma prise de sang\",\"message\":\"\\u26a0\\ufe0f \\u00ab faire ma prise de sang \\u00bb est en retard de 16 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('5b3950ab-c9f3-4714-aaa3-d45f3fe9e9f2', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":16,\"title\":\"Reviser CEJM\",\"message\":\"\\u26a0\\ufe0f \\u00ab Reviser CEJM \\u00bb est en retard de 15 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('4b768418-8387-41f5-b3c5-511fc193e7d1', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":17,\"title\":\"faire exode maths\",\"message\":\"\\u26a0\\ufe0f \\u00ab faire exode maths \\u00bb est en retard de 11 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('f54a8cb2-bcc6-429d-bfed-b52fee9f785f', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":18,\"title\":\"Finir les widget\",\"message\":\"\\u26a0\\ufe0f \\u00ab Finir les widget \\u00bb est en retard de 18 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('5428e29c-233e-4c9b-9d1a-ff67275d1791', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":19,\"title\":\"manger\",\"message\":\"\\u26a0\\ufe0f \\u00ab manger \\u00bb est en retard de 21 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('835dbc65-90f4-47fc-90a4-d0c910e419e2', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 4, '{\"task_id\":21,\"title\":\"Pr\\u00e9parer pr\\u00e9sentation\",\"message\":\"\\u26a0\\ufe0f \\u00ab Pr\\u00e9parer pr\\u00e9sentation \\u00bb est en retard de 18 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:37:27', '2026-05-11 14:37:27');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('fd4561bd-19f6-4cbf-9d9b-5639aad051fb', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"Statut de la t\\u00e2che \'TESTER L\'APPLI\' mis \\u00e0 jour : En cours\",\"type\":\"info\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:44:06', '2026-05-11 14:44:06');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('f45f4871-2b8b-4ff3-8ac8-79def8d99c5c', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"Statut de la t\\u00e2che \'TESTER L\'APPLI\' mis \\u00e0 jour : \\u00c0 faire\",\"type\":\"info\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:44:08', '2026-05-11 14:44:08');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('61484702-9b76-469a-994d-46b01a48b513', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":24,\"title\":\"Aller voir le gyn\\u00e9cologue\",\"message\":\"Statut de la t\\u00e2che \'Aller voir le gyn\\u00e9cologue\' mis \\u00e0 jour : En cours\",\"type\":\"info\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:53:01', '2026-05-11 14:53:01');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('ce81c637-4e7d-4380-80fc-bb5595c49431', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":24,\"title\":\"Aller voir le gyn\\u00e9cologue\",\"message\":\"Statut de la t\\u00e2che \'Aller voir le gyn\\u00e9cologue\' mis \\u00e0 jour : Termin\\u00e9e\",\"type\":\"success\",\"action\":\"\\/tasks\"}', NULL, '2026-05-11 14:53:03', '2026-05-11 14:53:03');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('4a94427f-6f84-40f5-8565-fdeb196653ed', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"\\u26a0\\ufe0f \\u00ab TESTER L\'APPLI \\u00bb est en retard de 25 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-11 14:53:06', '2026-05-11 14:53:06');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('8a46b736-2a47-4e2e-8971-c1aedc4bb466', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":2,\"title\":\"Faire mon exercice d math\",\"message\":\"\\u26a0\\ufe0f \\u00ab Faire mon exercice d math \\u00bb est en retard de 29 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-12 11:16:59', '2026-05-12 11:08:22', '2026-05-12 11:16:59');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('57c79eb3-e61a-4ce5-a1e0-6a4c9d79de0a', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":3,\"title\":\"Prendre rendez-vous pour faire ma prise de sang\",\"message\":\"\\u26a0\\ufe0f \\u00ab Prendre rendez-vous pour faire ma prise de sang \\u00bb est en retard de 28 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-12 11:14:22', '2026-05-12 11:08:22', '2026-05-12 11:14:22');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('7f2e4dae-5d8d-416e-96ee-d75502b93236', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":12,\"title\":\"R\\u00e9viser CEJM\",\"message\":\"\\u26a0\\ufe0f \\u00ab R\\u00e9viser CEJM \\u00bb est en retard de 19 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-12 11:08:22', '2026-05-12 11:08:22');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('464444fb-751c-40d2-a214-74ad7135dc27', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":13,\"title\":\"acheter du pain\",\"message\":\"\\u26a0\\ufe0f \\u00ab acheter du pain \\u00bb est en retard de 19 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-12 11:08:22', '2026-05-12 11:08:22');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('82a1e8f2-3506-4b2f-b633-850e7aef6e04', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":23,\"title\":\"sortir les poubelles\",\"message\":\"\\u26a0\\ufe0f \\u00ab sortir les poubelles \\u00bb est en retard de 7 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-12 11:08:22', '2026-05-12 11:08:22');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('4eb520d7-f3ac-45bd-8fec-caee6c99bc53', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"\\u26a0\\ufe0f \\u00ab TESTER L\'APPLI \\u00bb est en retard de 26 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-12 13:48:56', '2026-05-12 13:33:33', '2026-05-12 13:48:56');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('d66700a9-2cd4-4ff5-99ca-101f484f6dc5', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":5,\"title\":\"MATHS\",\"message\":\"\\u26a0\\ufe0f \\u00ab MATHS \\u00bb est en retard de 21 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-12 13:49:01', '2026-05-12 13:33:33', '2026-05-12 13:49:01');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('87085f83-e267-4bdd-8245-c7871df31ae5', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":6,\"title\":\"EXAMEN BTS\",\"message\":\"\\u26a0\\ufe0f \\u00ab EXAMEN BTS \\u00bb est en retard de 24 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-12 13:33:33', '2026-05-12 13:33:33');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('42f2d6b5-bb5d-4605-9c3c-5d319f4a2402', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":26,\"title\":\"operation zen\",\"message\":\"\\u26a0\\ufe0f \\u00ab operation zen \\u00bb est en retard de 1 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-12 13:33:33', '2026-05-12 13:33:33');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('f0f1a606-c309-4269-b5a3-f8a4cdf370a6', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":2,\"title\":\"Faire mon exercice d math\",\"message\":\"\\u26a0\\ufe0f \\u00ab Faire mon exercice d math \\u00bb est en retard de 30 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-13 12:41:49', '2026-05-13 12:41:36', '2026-05-13 12:41:49');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('a6f1363d-fa92-4d32-aadb-448618fc1ace', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":3,\"title\":\"Prendre rendez-vous pour faire ma prise de sang\",\"message\":\"\\u26a0\\ufe0f \\u00ab Prendre rendez-vous pour faire ma prise de sang \\u00bb est en retard de 29 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-13 12:41:55', '2026-05-13 12:41:36', '2026-05-13 12:41:55');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('85801ac0-f066-4a88-9a69-52b0ce0d69fb', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":12,\"title\":\"R\\u00e9viser CEJM\",\"message\":\"\\u26a0\\ufe0f \\u00ab R\\u00e9viser CEJM \\u00bb est en retard de 20 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-13 12:41:58', '2026-05-13 12:41:36', '2026-05-13 12:41:58');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('982850bc-12a1-4b24-b294-a3a7af15abef', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":13,\"title\":\"acheter du pain\",\"message\":\"\\u26a0\\ufe0f \\u00ab acheter du pain \\u00bb est en retard de 20 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', '2026-05-13 12:42:02', '2026-05-13 12:41:36', '2026-05-13 12:42:02');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('941d9354-3b1d-46f1-ab61-59a219f76e23', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 2, '{\"task_id\":23,\"title\":\"sortir les poubelles\",\"message\":\"\\u26a0\\ufe0f \\u00ab sortir les poubelles \\u00bb est en retard de 8 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-13 12:41:36', '2026-05-13 12:41:36');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('91452a13-59ed-4b88-b5c7-69a4bf202c04', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":4,\"title\":\"TESTER L\'APPLI\",\"message\":\"\\u26a0\\ufe0f \\u00ab TESTER L\'APPLI \\u00bb est en retard de 27 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-13 13:09:39', '2026-05-13 13:09:39');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('bbee3bf3-ecfa-43c6-8820-374f29b34d53', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":5,\"title\":\"MATHS\",\"message\":\"\\u26a0\\ufe0f \\u00ab MATHS \\u00bb est en retard de 22 jour(s) ! Statut : \\u00c0 faire.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-13 13:09:39', '2026-05-13 13:09:39');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('0f12cf6c-f7fe-4645-b772-03f8e32504ed', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":6,\"title\":\"EXAMEN BTS\",\"message\":\"\\u26a0\\ufe0f \\u00ab EXAMEN BTS \\u00bb est en retard de 25 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-13 13:09:39', '2026-05-13 13:09:39');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('d3a9dacf-72dc-4b05-85f2-a5aabcc097f6', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 3, '{\"task_id\":26,\"title\":\"operation zen\",\"message\":\"\\u26a0\\ufe0f \\u00ab operation zen \\u00bb est en retard de 2 jour(s) ! Statut : En cours.\",\"type\":\"error\",\"action\":\"\\/tasks\",\"reminder_type\":\"overdue\"}', NULL, '2026-05-13 13:09:39', '2026-05-13 13:09:39');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('34ac6a00-03ed-436f-975a-b4077b6c2fa8', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 8, '{\"task_id\":27,\"title\":\"test\",\"message\":\"Nouvelle t\\u00e2che cr\\u00e9\\u00e9e : test\",\"type\":\"success\",\"action\":\"\\/tasks\"}', '2026-05-13 13:43:11', '2026-05-13 13:12:40', '2026-05-13 13:43:11');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('46a16cff-b78d-43ef-8826-b06fd5b09934', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 8, '{\"task_id\":27,\"title\":\"test\",\"message\":\"\\ud83d\\udd34 \\u00ab test \\u00bb expire aujourd\'hui ! Statut actuel : \\u00c0 faire.\",\"type\":\"warning\",\"action\":\"\\/tasks\",\"reminder_type\":\"today\"}', '2026-05-13 13:19:30', '2026-05-13 13:12:43', '2026-05-13 13:19:30');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('202b73c2-f6c9-4603-a450-6ded1c1c6906', 'App\\Notifications\\TaskNotification', 'App\\Models\\User', 8, '{\"task_id\":28,\"title\":\"TACHE DE MENAGE A FAIRE\",\"message\":\"\\ud83d\\udd14 Rappel : La t\\u00e2che \\u00ab TACHE DE MENAGE A FAIRE \\u00bb expire demain. Statut : \\u00c0 faire.\",\"type\":\"warning\",\"action\":\"\\/tasks\",\"reminder_type\":\"tomorrow\"}', '2026-05-13 13:44:02', '2026-05-13 13:43:56', '2026-05-13 13:44:02');

-- Data for table `personal_access_tokens`
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (1, 'App\\Models\\User', 5, 'auth_token', 'c5e0277b9ee280c2c59c4c05d134fa16763b1bdaa079efc3ca29aa687c2d4054', '[\"*\"]', NULL, NULL, '2026-04-23 08:22:48', '2026-04-23 08:22:48');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (2, 'App\\Models\\User', 6, 'auth_token', '2651117293edb60347c3944dee8dfa9d64ccfdacb924600a5812e96a6632779c', '[\"*\"]', NULL, NULL, '2026-04-23 08:23:44', '2026-04-23 08:23:44');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (3, 'App\\Models\\User', 6, 'auth_token', 'c2db195c5f64c5b49b9ef258953e60532cc296a2b780b0ba51778286ffcdaa12', '[\"*\"]', '2026-04-23 08:23:57', NULL, '2026-04-23 08:23:56', '2026-04-23 08:23:57');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (5, 'App\\Models\\User', 3, 'auth_token', '0afb17020cd1fc8433c2eca2347ce68bd66186605890030cf70c1e2541ae4d5c', '[\"*\"]', NULL, NULL, '2026-05-04 19:24:56', '2026-05-04 19:24:56');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (7, 'App\\Models\\User', 7, 'auth_token', 'c981b31b4c860d62bd3b24a728d7effcb4e5f728ebd3aa6cf2f554844ec204ce', '[\"*\"]', NULL, NULL, '2026-05-10 19:32:53', '2026-05-10 19:32:53');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (8, 'App\\Models\\User', 7, 'auth_token', '623b5fcc57a4e1eac6e89e724c62a308b693ed7a750ad89cd8dcea11fe6ad595', '[\"*\"]', '2026-05-10 19:33:47', NULL, '2026-05-10 19:33:47', '2026-05-10 19:33:47');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (9, 'App\\Models\\User', 3, 'auth_token', 'b3bb6c7da38caa9e06633c0f91df13118f69abe9d916de4bf14804e0fd2337b2', '[\"*\"]', '2026-05-10 21:29:33', NULL, '2026-05-10 21:29:32', '2026-05-10 21:29:33');

-- Data for table `schedules`
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (34, 10, 1, '2026-04-19 15:30:00', '2026-04-19 16:59:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (35, 10, 1, '2026-04-20 09:00:00', '2026-04-20 11:00:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (36, 10, 1, '2026-04-21 09:00:00', '2026-04-21 10:31:00', '2026-04-19 15:26:17', '2026-04-19 15:26:17');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (94, 14, 4, '2026-04-23 12:00:00', '2026-04-23 13:30:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (95, 16, 4, '2026-04-24 18:30:00', '2026-04-24 19:50:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (96, 17, 4, '2026-04-26 12:00:00', '2026-04-26 13:00:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (97, 18, 4, '2026-04-23 13:45:00', '2026-04-23 14:00:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (98, 18, 4, '2026-04-23 21:00:00', '2026-04-23 22:45:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (99, 15, 4, '2026-04-24 20:05:00', '2026-04-24 21:05:00', '2026-04-23 10:42:56', '2026-04-23 10:42:56');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (106, 1, 2, '2026-05-12 11:30:00', '2026-05-12 12:00:00', '2026-05-12 11:16:27', '2026-05-12 11:16:27');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (111, 28, 8, '2026-05-13 14:00:00', '2026-05-13 15:00:00', '2026-05-13 13:47:07', '2026-05-13 13:47:07');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (112, 27, 8, '2026-05-13 15:15:00', '2026-05-13 16:15:00', '2026-05-13 13:47:07', '2026-05-13 13:47:07');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (113, 30, 8, '2026-05-14 09:00:00', '2026-05-14 11:00:00', '2026-05-13 13:47:07', '2026-05-13 13:47:07');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (114, 30, 8, '2026-05-15 09:00:00', '2026-05-15 09:40:00', '2026-05-13 13:47:07', '2026-05-13 13:47:07');
INSERT INTO `schedules` (`id`, `task_id`, `user_id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES (115, 29, 8, '2026-05-15 09:55:00', '2026-05-15 10:55:00', '2026-05-13 13:47:07', '2026-05-13 13:47:07');

-- Data for table `task_attachments`
INSERT INTO `task_attachments` (`id`, `task_id`, `type`, `original_name`, `path`, `mime_type`, `size`, `created_at`, `updated_at`) VALUES (1, 1, 'pdf', 'cc_Yael (1).pdf', 'attachments/2/x3SNdAXUObZmrphgQNEyO8NMJEU2ZgnZIfFASB9u.pdf', 'application/pdf', 349526, '2026-05-11 14:42:11', '2026-05-11 14:42:11');

-- Data for table `tasks`
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (1, 2, 1, 'aller faire les courses avant le pilate', 'acheter du lait
un paque d\'eau
de la viande', 30, 2, '2026-05-25 00:00:00', 'in_progress', '2026-04-13 13:49:03', '2026-05-11 14:43:10', 1, 'weekly', '[3]', '2026-05-13 00:00:00', NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (2, 2, 2, 'Faire mon exercice d math', NULL, 60, 3, '2026-04-13 00:00:00', 'todo', '2026-04-13 13:56:15', '2026-04-23 04:21:41', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (3, 2, NULL, 'Prendre rendez-vous pour faire ma prise de sang', NULL, 10, 1, '2026-04-14 00:00:00', 'in_progress', '2026-04-13 13:58:11', '2026-04-23 03:31:51', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (4, 3, 3, 'TESTER L\'APPLI', NULL, 60, 3, '2026-04-16 00:00:00', 'todo', '2026-04-16 14:53:56', '2026-05-11 14:44:08', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (5, 3, 5, 'MATHS', NULL, 60, 3, '2026-04-21 00:00:00', 'todo', '2026-04-16 14:55:23', '2026-04-16 14:55:23', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (6, 3, 3, 'EXAMEN BTS', NULL, 60, 1, '2026-04-18 00:00:00', 'in_progress', '2026-04-16 14:57:01', '2026-04-16 14:57:01', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (7, 2, NULL, 'Session beauté', 'aller à mes rendez vous beauté', 120, 5, '2026-04-19 00:00:00', 'done', '2026-04-19 14:42:48', '2026-04-23 03:31:48', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (9, 2, NULL, 'Allé au cours d\'anglais', NULL, 60, 4, '2026-04-22 00:00:00', 'done', '2026-04-19 14:46:30', '2026-04-23 03:32:07', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (10, 1, NULL, 'Test Task', NULL, 300, 'high', '2026-04-21 15:26:03', 'todo', '2026-04-19 15:26:03', '2026-04-19 15:26:03', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (12, 2, 2, 'Réviser CEJM', NULL, 90, 4, '2026-04-23 00:00:00', 'todo', '2026-04-23 04:19:34', '2026-04-23 04:19:47', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (13, 2, 1, 'acheter du pain', NULL, 30, 4, '2026-04-23 00:00:00', 'todo', '2026-04-23 04:22:52', '2026-04-23 04:22:52', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (14, 4, 8, 'lire', NULL, 90, 4, '2026-04-29 00:00:00', 'todo', '2026-04-23 08:36:54', '2026-04-23 10:36:47', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (15, 4, 10, 'faire ma prise de sang', NULL, 60, 2, '2026-04-25 00:00:00', 'todo', '2026-04-23 08:38:02', '2026-04-23 08:41:01', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (16, 4, 9, 'Reviser CEJM', NULL, 80, 3, '2026-04-26 00:00:00', 'in_progress', '2026-04-23 08:39:14', '2026-04-23 08:41:22', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (17, 4, 9, 'faire exode maths', NULL, 60, 3, '2026-04-30 00:00:00', 'todo', '2026-04-23 08:39:47', '2026-04-23 08:39:47', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (18, 4, 7, 'Finir les widget', NULL, 120, 2, '2026-04-23 00:00:00', 'in_progress', '2026-04-23 08:40:43', '2026-04-23 08:41:14', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (19, 4, NULL, 'manger', NULL, 60, 3, '2026-04-20 00:00:00', 'in_progress', '2026-04-23 10:38:05', '2026-04-23 10:39:05', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (20, 4, NULL, 'faire à manger', NULL, 20, 3, '2026-04-23 00:00:00', 'done', '2026-04-23 10:38:34', '2026-04-23 10:38:55', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (21, 4, 9, 'Préparer présentation', NULL, 180, 2, '2026-04-23 00:00:00', 'todo', '2026-04-23 10:40:45', '2026-04-23 10:40:45', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (22, 4, NULL, 'Préparer présentation', NULL, 180, 4, '2026-04-23 00:00:00', 'done', '2026-04-23 10:41:45', '2026-04-23 10:42:50', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (23, 2, 2, 'sortir les poubelles', NULL, 15, 3, '2026-05-05 00:00:00', 'todo', '2026-05-04 21:05:11', '2026-05-04 21:05:11', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (24, 3, NULL, 'Aller voir le gynécologue', NULL, 60, 3, '2026-05-06 00:00:00', 'done', '2026-05-04 21:12:20', '2026-05-11 14:53:03', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (25, 7, NULL, 'Test Task Antigravity', NULL, 30, 1, '2026-12-31 00:00:00', 'todo', '2026-05-10 19:33:47', '2026-05-10 19:33:47', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (26, 3, NULL, 'operation zen', NULL, 60, 3, '2026-05-11 00:00:00', 'in_progress', '2026-05-11 14:27:48', '2026-05-11 14:28:12', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (27, 8, NULL, 'test', NULL, 60, 3, '2026-05-13 00:00:00', 'todo', '2026-05-13 13:12:40', '2026-05-13 13:12:40', 1, 'weekly', '[4,3,2]', '2026-05-28 00:00:00', NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (28, 8, NULL, 'TACHE DE MENAGE A FAIRE', NULL, 60, 5, '2026-05-14 00:00:00', 'todo', '2026-05-13 13:43:50', '2026-05-13 13:43:50', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (29, 8, NULL, 'MATHS', NULL, 60, 1, '2026-05-15 00:00:00', 'todo', '2026-05-13 13:45:16', '2026-05-13 13:45:16', 0, NULL, NULL, NULL, NULL);
INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `duration_minutes`, `priority`, `deadline`, `status`, `created_at`, `updated_at`, `is_recurring`, `recurrence_type`, `recurrence_days`, `recurrence_end`, `parent_task_id`) VALUES (30, 8, NULL, 'FRANCAIS', NULL, 160, 3, '2026-05-17 00:00:00', 'todo', '2026-05-13 13:47:03', '2026-05-13 13:47:03', 0, NULL, NULL, NULL, NULL);

-- Data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (1, 'jean Dupont', 'admin@admin.com', NULL, '$2y$10$NTPlhyTMc7XdvfqzqLnk9uFEr6phrcDYSRCvszYo2N2cs.lm6g5e.', NULL, 15, NULL, '2026-04-04 15:53:16', '2026-04-04 15:53:16', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (2, 'Yayou', 'josesfares@gmail', NULL, '$2y$10$ws3a5yf0tHWI8jk7spcoCuZzijsLzUTkE/uF0IOM4ozmejzfGQSxW', NULL, 15, 'AKv9fBR2OtcVpD1zHuY9jp0UJyW7zr4UhIVtuZETeizymL8LrIIJdxgAaN7x', '2026-04-13 13:46:55', '2026-04-13 13:46:55', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (3, 'Test User', 'test@example.com', NULL, '$2y$10$6B8zmwWvKWfCX5c14Wb9AOQIDnl05iJb2PJ4Owdi5mHrjLSj5IrF6', NULL, 15, '9T5BP3aOe10uyYk6qxQL3kQcgYLuOZKsIoJRxtrhYy0TDUIZs3GLzJSe1TNg', '2026-04-16 14:37:36', '2026-05-04 19:24:51', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (4, 'mariem', 'marie@gmail.com', NULL, '$2y$10$gzsRIncyhcTkKwXGe5rrzOdDxECxw5eUtdn0rDaXmC9nyet6/Alwu', NULL, 15, '0z8eiIxVznMghSnwHr4kv00SwZR1sab7wOip2ADFae0D9tvs1vvSTvAv04Ct', '2026-04-23 08:13:45', '2026-04-23 08:13:45', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (5, 'Test User', 'test@smartschedule.fr', NULL, '$2y$10$4tUuVaKxWzligUXnXD9jR.JgoPFvv9qhbDa5V7U0jauro/ubEFfte', NULL, 15, NULL, '2026-04-23 08:22:48', '2026-04-23 08:22:48', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (6, 'Test User', 'testmobile@smartschedule.fr', NULL, '$2y$10$.cL8pt8c62wwbpC41vRQQOzU68ZAR4qlkfiIKK0xfH8OTN59pW9Wm', NULL, 15, NULL, '2026-04-23 08:23:44', '2026-04-23 08:23:44', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (7, 'Antigravity Test', 'antigravity@test.com', NULL, '$2y$10$E01inhP.qYR.WzN.HHmp9OX00XVbNq2jbrHUuKN5b/iWn6T1eLrHu', NULL, 15, NULL, '2026-05-10 19:32:39', '2026-05-10 19:32:39', NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `preferred_hours`, `break_duration`, `remember_token`, `created_at`, `updated_at`, `expo_push_token`) VALUES (8, 'Fafares', 'fafares@gmail.com', NULL, '$2y$10$5Q/.yT8eWbOw9bQZcExkre0tIZF7b62nx.21xmaXRpQLW.jSM8OPm', NULL, 15, NULL, '2026-05-13 13:11:40', '2026-05-13 13:11:40', NULL);


SET FOREIGN_KEY_CHECKS=1;
