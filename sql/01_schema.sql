-- ALOG ACADEMY - Enterprise Database Schema
-- MySQL 8.0+
-- Optimized for free shared hosting
-- Charset: utf8mb4_unicode_ci

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- CORE TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `group_name` varchar(50) DEFAULT 'general',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`),
  KEY `idx_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` json DEFAULT NULL,
  `level` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `school_levels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_sort` (`sort_order`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `filieres` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `school_level_id` int(11) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug_level` (`slug`,`school_level_id`),
  KEY `idx_level` (`school_level_id`),
  KEY `idx_active` (`is_active`),
  FOREIGN KEY (`school_level_id`) REFERENCES `school_levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL DEFAULT 3,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(50) DEFAULT 'avatar1.png',
  `birth_date` date DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `school_level_id` int(11) unsigned DEFAULT NULL,
  `filiere_id` int(11) unsigned DEFAULT NULL,
  `xp_total` int(11) unsigned NOT NULL DEFAULT 0,
  `xp_current` int(11) unsigned NOT NULL DEFAULT 0,
  `level` int(11) unsigned NOT NULL DEFAULT 1,
  `streak_days` int(11) unsigned NOT NULL DEFAULT 0,
  `streak_last_date` date DEFAULT NULL,
  `plan_id` int(11) unsigned DEFAULT 1,
  `plan_expires_at` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended','pending') DEFAULT 'pending',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  UNIQUE KEY `uk_google_id` (`google_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_level` (`school_level_id`),
  KEY `idx_filiere` (`filiere_id`),
  KEY `idx_plan` (`plan_id`),
  KEY `idx_status` (`status`),
  KEY `idx_xp` (`xp_total`),
  KEY `idx_region` (`region`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`school_level_id`) REFERENCES `school_levels` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_user` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_user` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EDUCATIONAL STRUCTURE
-- ============================================



CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 'book',
  `color` varchar(7) DEFAULT '#0975e4',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subject_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) unsigned NOT NULL,
  `school_level_id` int(11) unsigned NOT NULL,
  `filiere_id` int(11) unsigned DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subject_level` (`subject_id`,`school_level_id`,`filiere_id`),
  KEY `idx_level` (`school_level_id`),
  KEY `idx_filiere` (`filiere_id`),
  FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`school_level_id`) REFERENCES `school_levels` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lessons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) unsigned NOT NULL,
  `school_level_id` int(11) unsigned NOT NULL,
  `filiere_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `youtube_url` varchar(255) DEFAULT NULL,
  `youtube_duration` int(11) DEFAULT 0,
  `pdf_course_url` varchar(500) DEFAULT NULL,
  `pdf_exercises_url` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `xp_reward` int(11) unsigned NOT NULL DEFAULT 10,
  `xp_unlock_cost` int(11) unsigned DEFAULT NULL,
  `plan_id` int(11) unsigned DEFAULT 1,
  `quiz_id` int(11) unsigned DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_subject` (`subject_id`),
  KEY `idx_level` (`school_level_id`),
  KEY `idx_filiere` (`filiere_id`),
  KEY `idx_plan` (`plan_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_quiz` (`quiz_id`),
  FULLTEXT KEY `ft_title_desc` (`title`,`description`),
  FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`school_level_id`) REFERENCES `school_levels` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- LESSON PROGRESS & VIDEO TRACKING
-- ============================================

CREATE TABLE IF NOT EXISTS `lesson_progress` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `lesson_id` int(11) unsigned NOT NULL,
  `video_watched_seconds` int(11) unsigned DEFAULT 0,
  `video_completed` tinyint(1) DEFAULT 0,
  `quiz_completed` tinyint(1) DEFAULT 0,
  `quiz_score` int(11) DEFAULT NULL,
  `quiz_passed` tinyint(1) DEFAULT 0,
  `xp_earned` int(11) unsigned DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `started_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_lesson` (`user_id`,`lesson_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_lesson` (`lesson_id`),
  KEY `idx_completed` (`completed_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- QUIZ SYSTEM
-- ============================================

CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `passing_score` int(11) unsigned NOT NULL DEFAULT 60,
  `xp_reward` int(11) unsigned NOT NULL DEFAULT 20,
  `time_limit_minutes` int(11) unsigned DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lesson` (`lesson_id`),
  KEY `idx_active` (`is_active`),
  FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) unsigned NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('qcm','true_false') NOT NULL DEFAULT 'qcm',
  `options` json NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `explanation` text,
  `points` int(11) unsigned NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_quiz` (`quiz_id`),
  KEY `idx_type` (`question_type`),
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `quiz_id` int(11) unsigned NOT NULL,
  `score` int(11) unsigned DEFAULT 0,
  `total_points` int(11) unsigned DEFAULT 0,
  `percentage` int(11) unsigned DEFAULT 0,
  `passed` tinyint(1) DEFAULT 0,
  `answers` json DEFAULT NULL,
  `time_spent_seconds` int(11) unsigned DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_quiz` (`quiz_id`),
  KEY `idx_passed` (`passed`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SUBSCRIPTION SYSTEM
-- ============================================

CREATE TABLE IF NOT EXISTS `plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text,
  `price_mad` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_usd` decimal(10,2) DEFAULT NULL,
  `duration_days` int(11) unsigned NOT NULL DEFAULT 30,
  `features` json DEFAULT NULL,
  `lesson_access_type` enum('all','limited','none') DEFAULT 'limited',
  `max_lessons_per_day` int(11) unsigned DEFAULT NULL,
  `support_level` enum('none','email','priority','dedicated') DEFAULT 'none',
  `badge` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#0975e4',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `plan_id` int(11) unsigned NOT NULL,
  `status` enum('active','expired','cancelled','pending') DEFAULT 'pending',
  `payment_method` enum('cmi','paypal','whatsapp','free','admin') DEFAULT 'free',
  `payment_status` enum('paid','pending','failed','refunded') DEFAULT 'pending',
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'MAD',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_plan` (`plan_id`),
  KEY `idx_status` (`status`),
  KEY `idx_expires` (`expires_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_percent` int(11) unsigned NOT NULL DEFAULT 0,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `applies_to_plan_id` int(11) unsigned DEFAULT NULL,
  `max_uses` int(11) unsigned DEFAULT NULL,
  `used_count` int(11) unsigned NOT NULL DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_active` (`is_active`),
  KEY `idx_expires` (`expires_at`),
  FOREIGN KEY (`applies_to_plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- XP & GAMIFICATION
-- ============================================

CREATE TABLE IF NOT EXISTS `xp_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `type` enum('lesson','quiz','streak','achievement','purchase','bonus','penalty','admin') NOT NULL,
  `source_id` int(11) unsigned DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_created` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `achievements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT 'trophy',
  `color` varchar(7) DEFAULT '#ffd700',
  `xp_bonus` int(11) unsigned DEFAULT 0,
  `requirement_type` enum('lessons','quizzes','streak','xp','subscription','custom') NOT NULL,
  `requirement_value` int(11) unsigned NOT NULL DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_achievements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `achievement_id` int(11) unsigned NOT NULL,
  `earned_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_achievement` (`user_id`,`achievement_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `weekly_rankings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `week_start` date NOT NULL,
  `week_end` date NOT NULL,
  `xp_earned` int(11) unsigned NOT NULL DEFAULT 0,
  `lessons_completed` int(11) unsigned NOT NULL DEFAULT 0,
  `quizzes_completed` int(11) unsigned NOT NULL DEFAULT 0,
  `rank_position` int(11) unsigned DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `school_level_id` int(11) unsigned DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_week` (`user_id`,`week_start`),
  KEY `idx_week` (`week_start`),
  KEY `idx_rank` (`rank_position`),
  KEY `idx_region` (`region`),
  KEY `idx_level` (`school_level_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BLOG SYSTEM
-- ============================================

CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `views_count` int(11) unsigned NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_author` (`author_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_published` (`published_at`),
  KEY `idx_featured` (`is_featured`),
  FULLTEXT KEY `ft_content` (`title`,`content`,`excerpt`),
  FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_tag` (
  `post_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EVENTS SYSTEM
-- ============================================

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `event_type` enum('online','offline','mock_exam','discord','webinar') DEFAULT 'online',
  `image_url` varchar(500) DEFAULT NULL,
  `registration_url` varchar(500) DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_participants` int(11) unsigned DEFAULT NULL,
  `current_participants` int(11) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_date` (`event_date`),
  KEY `idx_type` (`event_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `registered_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('registered','attended','cancelled','no_show') DEFAULT 'registered',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_event_user` (`event_id`,`user_id`),
  KEY `idx_user` (`user_id`),
  FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ADMIN AUDIT & LOGS
-- ============================================

CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_created` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONTACT & FAQ
-- ============================================

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) DEFAULT 'general',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA
-- ============================================

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `permissions`, `level`) VALUES
(1, 'Super Admin', 'super_admin', 'Full system control', '["*"]', 100),
(2, 'Content Manager', 'content_manager', 'Manage lessons and educational content', '["lessons.*","subjects.*","levels.*","filieres.*","quizzes.*"]', 50),
(3, 'Student', 'student', 'Regular platform user', '["profile.*","lessons.read","quizzes.take","blog.read","events.read"]', 1),
(4, 'Blogger', 'blogger', 'Write and manage blog posts', '["blog.*","media.upload"]', 40),
(5, 'Moderator', 'moderator', 'Moderate users and content', '["users.read","users.update","lessons.read","blog.read","comments.moderate"]', 60),
(6, 'Support', 'support', 'Handle support tickets and user issues', '["users.read","contacts.*","subscriptions.read"]', 30);

INSERT INTO `plans` (`id`, `name`, `slug`, `description`, `price_mad`, `price_usd`, `duration_days`, `features`, `lesson_access_type`, `max_lessons_per_day`, `support_level`, `badge`, `color`, `is_active`, `sort_order`) VALUES
(1, 'Gratuit', 'free', 'Accès limité aux leçons de base. Parfait pour commencer.', 0.00, 0.00, 36500, '["3 leçons/jour","Accès basique","Support communautaire","Classement régional"]', 'limited', 3, 'none', 'Free', '#6c757d', 1, 1),
(2, 'Pro', 'pro', 'Accès complet à toutes les leçons. Support email prioritaire.', 99.00, 9.99, 30, '["Leçons illimitées","Quiz premium","Support email","Classement national","Badge Pro","Sans publicités"]', 'all', NULL, 'email', 'Pro', '#0975e4', 1, 2),
(3, 'Ultra', 'ultra', 'Expérience premium complète avec support dédié et avantages exclusifs.', 249.00, 24.99, 30, '["Tout Pro+","Support prioritaire","Événements exclusifs","Coaching groupe","Badge Ultra","Accès anticipé"]', 'all', NULL, 'priority', 'Ultra', '#7b2cbf', 1, 3);

INSERT INTO `school_levels` (`name`, `slug`, `description`, `sort_order`) VALUES
('Tronc Commun', 'tronc-commun', 'Niveau Tronc Commun Scientifique et Technique', 1),
('1ère Bac', '1ere-bac', 'Première Année Baccalauréat', 2),
('2ème Bac', '2eme-bac', 'Deuxième Année Baccalauréat', 3),
('Cours Préparatoires', 'cpge', 'Classes Préparatoires aux Grandes Écoles', 4),
('Licence', 'licence', 'Cycle Licence Universitaire', 5),
('Master', 'master', 'Cycle Master Universitaire', 6);

INSERT INTO `filieres` (`school_level_id`, `name`, `slug`, `description`, `sort_order`) VALUES
(1, 'Sciences', 'sciences', 'Filière Sciences Tronc Commun', 1),
(1, 'Technologie', 'technologie', 'Filière Technologie Tronc Commun', 2),
(2, 'Sciences Mathématiques', 'sciences-maths', 'Filière Sciences Mathématiques', 1),
(2, 'Sciences Expérimentales', 'sciences-exp', 'Filière Sciences Expérimentales', 2),
(2, 'Sciences Économiques', 'sciences-eco', 'Filière Sciences Économiques et Gestion', 3),
(3, 'Sciences Mathématiques A', 'sciences-maths-a', ' Sciences Mathématiques Option A', 1),
(3, 'Sciences Mathématiques B', 'sciences-maths-b', 'Sciences Mathématiques Option B', 2),
(3, 'Sciences Physiques', 'sciences-physiques', 'Sciences Physiques', 3),
(3, 'Sciences de la Vie et de la Terre', 'svt', 'SVT', 4);

INSERT INTO `subjects` (`name`, `slug`, `description`, `icon`, `color`) VALUES
('Mathématiques', 'mathematiques', 'Cours et exercices de mathématiques', 'calculator', '#e63946'),
('Physique-Chimie', 'physique-chimie', 'Sciences physiques et chimie', 'atom', '#f4a261'),
('Sciences de la Vie', 'svt', 'Biologie, géologie et écologie', 'leaf', '#2a9d8f'),
('Français', 'francais', 'Langue et littérature française', 'book', '#264653'),
('Anglais', 'anglais', 'Langue anglaise', 'language', '#e9c46a'),
('Philosophie', 'philosophie', 'Réflexion philosophique', 'brain', '#6a4c93'),
('Histoire-Géographie', 'histoire-geo', 'Histoire et géographie du Maroc et du monde', 'globe', '#8d99ae');

INSERT INTO `subject_level` (`subject_id`, `school_level_id`, `filiere_id`) VALUES
(1, 1, 1), (1, 2, 2), (1, 2, 3), (1, 3, 5), (1, 3, 6),
(2, 1, 1), (2, 2, 2), (2, 2, 3), (2, 3, 5), (2, 3, 7),
(3, 1, 1), (3, 2, 3), (3, 3, 8),
(4, 1, NULL), (4, 2, NULL), (4, 3, NULL),
(5, 1, NULL), (5, 2, NULL), (5, 3, NULL),
(6, 3, NULL),
(7, 1, NULL), (7, 2, NULL), (7, 3, NULL);

INSERT INTO `achievements` (`name`, `slug`, `description`, `icon`, `color`, `xp_bonus`, `requirement_type`, `requirement_value`) VALUES
('Premier Pas', 'premier-pas', 'Compléter votre première leçon', 'footprints', '#4cc9f0', 50, 'lessons', 1),
('Étudiant Assidu', 'etudiant-assidu', 'Compléter 10 leçons', 'book-open', '#4895ef', 150, 'lessons', 10),
('Génie des Maths', 'genie-maths', 'Réussir 20 quiz de mathématiques', 'calculator', '#3f37c9', 300, 'quizzes', 20),
('Série Inarrêtable', 'serie-inarretable', 'Maintenir une série de 7 jours', 'flame', '#f72585', 200, 'streak', 7),
('Champion du Maroc', 'champion-maroc', 'Atteindre le top 10 national', 'crown', '#ffd700', 500, 'xp', 5000),
('Perfect Score', 'perfect-score', 'Obtenir 100% à un quiz difficile', 'star', '#ff9f1c', 250, 'custom', 1);

INSERT INTO `blog_categories` (`name`, `slug`, `description`, `meta_title`, `meta_description`) VALUES
('Conseils d\'étude', 'conseils-etude', 'Méthodologie et astuces pour réussir', 'Conseils d\'étude - ALOG Academy', 'Découvrez nos meilleurs conseils pour réussir vos examens marocains'),
('Actualités', 'actualites', 'Nouvelles et mises à jour de la plateforme', 'Actualités - ALOG Academy', 'Restez informé des dernières nouvelles d\'ALOG Academy'),
('Orientation', 'orientation', 'Guide d\'orientation scolaire et universitaire', 'Orientation Scolaire - ALOG Academy', 'Trouvez votre voie avec nos guides d\'orientation détaillés'),
('Concours', 'concours', 'Préparation aux concours nationaux', 'Préparation Concours - ALOG Academy', 'Préparez-vous aux grands concours marocains avec ALOG Academy');

INSERT INTO `faqs` (`question`, `answer`, `category`, `sort_order`) VALUES
('Comment puis-je m\'inscrire sur ALOG Academy ?', 'L\'inscription est simple et gratuite. Cliquez sur "S\'inscrire", remplissez le formulaire avec vos informations, sélectionnez votre niveau scolaire et filière, puis validez votre email.', 'general', 1),
('Quelle est la différence entre les plans Free, Pro et Ultra ?', 'Le plan Free offre 3 leçons par jour avec accès basique. Pro donne accès illimité avec support email. Ultra ajoute le support prioritaire, les événements exclusifs et le coaching.', 'pricing', 1),
('Comment fonctionne le système de points XP ?', 'Vous gagnez des XP en regardant des vidéos, en réussissant des quiz et en maintenant votre série quotidienne. Les XP déterminent votre classement et votre niveau.', 'gamification', 1),
('Puis-je changer de niveau ou de filière ?', 'Oui, mais seulement une fois tous les 3 mois pour préserver l\'intégrité du classement. Votre XP est conservé mais votre progression dans les leçons est réinitialisée.', 'account', 1),
('Comment sont stockés les cours PDF ?', 'Les PDF sont hébergés sur GitHub via des liens directs et affichés dans notre lecteur intégré. Aucun téléchargement local n\'est nécessaire sur nos serveurs.', 'technical', 1),
('Le paiement par CMI est-il sécurisé ?', 'Absolument. Les paiements CMI sont traités via leur API officielle avec cryptage SSL. Nous ne stockons jamais les informations de carte bancaire.', 'payment', 1),
('Comment puis-je contacter le support ?', 'Les membres Pro et Ultra peuvent utiliser le support email prioritaire. Tous les utilisateurs peuvent nous contacter via WhatsApp ou le formulaire de contact.', 'support', 1);

INSERT INTO `settings` (`setting_key`, `setting_value`, `group_name`) VALUES
('site_name', 'ALOG Academy', 'general'),
('site_tagline', 'La meilleure plateforme éducative du Maroc', 'general'),
('site_email', 'contact@alogacademy.ma', 'general'),
('site_phone', '+212 5XX-XXXXXX', 'general'),
('site_address', 'Casablanca, Maroc', 'general'),
('maintenance_mode', '0', 'system'),
('registration_open', '1', 'system'),
('default_plan_id', '1', 'system'),
('min_password_length', '8', 'security'),
('login_max_attempts', '5', 'security'),
('login_lockout_minutes', '30', 'security'),
('cmi_merchant_id', '', 'payment'),
('cmi_api_key', '', 'payment'),
('paypal_client_id', '', 'payment'),
('whatsapp_number', '+212600000000', 'payment'),
('seo_title_suffix', '- ALOG Academy', 'seo'),
('seo_description', 'ALOG Academy - Plateforme éducative premium pour les étudiants marocains. Cours, quiz, classements et préparation aux examens.', 'seo'),
('analytics_id', '', 'analytics'),
('facebook_page', 'https://facebook.com/alogacademy', 'social'),
('instagram_page', 'https://instagram.com/alogacademy', 'social'),
('youtube_channel', 'https://youtube.com/@alogacademy', 'social'),
('discord_invite', 'https://discord.gg/alogacademy', 'social');

SET FOREIGN_KEY_CHECKS = 1;