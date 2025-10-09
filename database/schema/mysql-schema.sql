/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` char(36) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `backup_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_reports` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` varchar(255) DEFAULT NULL,
  `entity_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `backup_reports_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `backup_reports_user_id_index` (`user_id`),
  KEY `backup_reports_created_at_index` (`created_at`),
  CONSTRAINT `backup_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_message_read_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message_read_receipts` (
  `id` char(36) NOT NULL,
  `chat_message_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_message_read_receipts_chat_message_id_user_id_unique` (`chat_message_id`,`user_id`),
  KEY `chat_message_read_receipts_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_message_read_receipts_chat_message_id_foreign` FOREIGN KEY (`chat_message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_message_read_receipts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` char(36) NOT NULL,
  `chat_room_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `message` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `attachment_type` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_chat_room_id_foreign` (`chat_room_id`),
  KEY `chat_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_messages_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_participants` (
  `id` char(36) NOT NULL,
  `chat_room_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_participants_chat_room_id_user_id_unique` (`chat_room_id`,`user_id`),
  KEY `chat_participants_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_participants_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_rooms` (
  `id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` enum('private','group') NOT NULL DEFAULT 'private',
  `last_message_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_rooms_last_message_id_foreign` (`last_message_id`),
  CONSTRAINT `chat_rooms_last_message_id_foreign` FOREIGN KEY (`last_message_id`) REFERENCES `chat_messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checklist_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklist_answers` (
  `id` char(36) NOT NULL,
  `checklist_id` char(36) NOT NULL,
  `checklist_item_id` char(36) NOT NULL,
  `status` enum('ok','attention','problem') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklist_answers_checklist_id_foreign` (`checklist_id`),
  KEY `checklist_answers_checklist_item_id_foreign` (`checklist_item_id`),
  CONSTRAINT `checklist_answers_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_answers_checklist_item_id_foreign` FOREIGN KEY (`checklist_item_id`) REFERENCES `checklist_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklist_items` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checklists` (
  `id` char(36) NOT NULL,
  `run_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `notes` text DEFAULT NULL,
  `has_defects` tinyint(1) NOT NULL DEFAULT 0,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approver_id` char(36) DEFAULT NULL,
  `approver_comment` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklists_run_id_foreign` (`run_id`),
  KEY `checklists_user_id_foreign` (`user_id`),
  KEY `checklists_approver_id_foreign` (`approver_id`),
  CONSTRAINT `checklists_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `checklists_run_id_foreign` FOREIGN KEY (`run_id`) REFERENCES `runs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `default_passwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `default_passwords` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `default_passwords_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defect_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defect_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `defect_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defect_report_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defect_report_answers` (
  `id` char(36) NOT NULL,
  `defect_report_id` char(36) NOT NULL,
  `defect_report_item_id` char(36) NOT NULL,
  `severity` enum('low','medium','high') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defect_report_answers_defect_report_id_foreign` (`defect_report_id`),
  KEY `defect_report_answers_defect_report_item_id_foreign` (`defect_report_item_id`),
  CONSTRAINT `defect_report_answers_defect_report_id_foreign` FOREIGN KEY (`defect_report_id`) REFERENCES `defect_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defect_report_answers_defect_report_item_id_foreign` FOREIGN KEY (`defect_report_item_id`) REFERENCES `defect_report_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defect_report_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defect_report_items` (
  `id` char(36) NOT NULL,
  `category_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defect_report_items_category_id_foreign` (`category_id`),
  CONSTRAINT `defect_report_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `defect_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `defect_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `defect_reports` (
  `id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `status` enum('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defect_reports_user_id_foreign` (`user_id`),
  KEY `defect_reports_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `defect_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defect_reports_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `digital_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_signatures` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `signature_code` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `digital_signatures_user_id_unique` (`user_id`),
  CONSTRAINT `digital_signatures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fine_processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fine_processes` (
  `id` char(36) NOT NULL,
  `fine_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `stage` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fine_processes_fine_id_foreign` (`fine_id`),
  KEY `fine_processes_user_id_foreign` (`user_id`),
  CONSTRAINT `fine_processes_fine_id_foreign` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fine_processes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fine_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fine_signatures` (
  `id` char(36) NOT NULL,
  `fine_id` char(36) NOT NULL,
  `digital_signature_id` char(36) NOT NULL,
  `signed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fine_signatures_fine_id_unique` (`fine_id`),
  KEY `fine_signatures_digital_signature_id_foreign` (`digital_signature_id`),
  CONSTRAINT `fine_signatures_digital_signature_id_foreign` FOREIGN KEY (`digital_signature_id`) REFERENCES `digital_signatures` (`id`),
  CONSTRAINT `fine_signatures_fine_id_foreign` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fines` (
  `id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `driver_id` char(36) NOT NULL,
  `registered_by_user_id` char(36) NOT NULL,
  `infraction_code` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending_acknowledgement','pending_payment','paid','appealed','cancelled') NOT NULL DEFAULT 'pending_acknowledgement',
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fines_driver_id_foreign` (`driver_id`),
  KEY `fines_registered_by_user_id_foreign` (`registered_by_user_id`),
  KEY `fines_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `fines_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fines_registered_by_user_id_foreign` FOREIGN KEY (`registered_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fines_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fuel_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_types` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fuel_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fuelings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuelings` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `fuel_type_id` char(36) NOT NULL,
  `gas_station_id` char(36) NOT NULL,
  `fueled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `km` int(10) unsigned NOT NULL,
  `liters` decimal(10,3) NOT NULL,
  `value_per_liter` decimal(10,2) NOT NULL,
  `invoice_path` varchar(255) DEFAULT NULL,
  `public_code` varchar(255) NOT NULL,
  `signature_path` text NOT NULL,
  `viewed_by` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`viewed_by`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fuelings_public_code_unique` (`public_code`),
  KEY `fuelings_user_id_foreign` (`user_id`),
  KEY `fuelings_fuel_type_id_foreign` (`fuel_type_id`),
  KEY `fuelings_gas_station_id_foreign` (`gas_station_id`),
  KEY `fuelings_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `fuelings_fuel_type_id_foreign` FOREIGN KEY (`fuel_type_id`) REFERENCES `fuel_types` (`id`),
  CONSTRAINT `fuelings_gas_station_id_foreign` FOREIGN KEY (`gas_station_id`) REFERENCES `gas_stations` (`id`),
  CONSTRAINT `fuelings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fuelings_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gas_stations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gas_stations` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cnpj` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gas_stations_cnpj_unique` (`cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_item_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_item_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_items` (
  `id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity_on_hand` int(10) unsigned NOT NULL DEFAULT 0,
  `unit_of_measure` varchar(255) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `reorder_level` int(10) unsigned NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_items_sku_unique` (`sku`),
  KEY `inventory_items_category_id_foreign` (`category_id`),
  CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `inventory_item_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements` (
  `id` char(36) NOT NULL,
  `inventory_item_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` enum('in','out','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `movement_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_movements_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `inventory_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `inventory_movements_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logbook_permission_secretariats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logbook_permission_secretariats` (
  `logbook_permission_id` char(36) NOT NULL,
  `secretariat_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`logbook_permission_id`,`secretariat_id`),
  KEY `logbook_permission_secretariats_secretariat_id_foreign` (`secretariat_id`),
  CONSTRAINT `logbook_permission_secretariats_logbook_permission_id_foreign` FOREIGN KEY (`logbook_permission_id`) REFERENCES `logbook_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_permission_secretariats_secretariat_id_foreign` FOREIGN KEY (`secretariat_id`) REFERENCES `secretariats` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logbook_permission_vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logbook_permission_vehicles` (
  `logbook_permission_id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`logbook_permission_id`,`vehicle_id`),
  KEY `logbook_permission_vehicles_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `logbook_permission_vehicles_logbook_permission_id_foreign` FOREIGN KEY (`logbook_permission_id`) REFERENCES `logbook_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_permission_vehicles_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logbook_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logbook_permissions` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `scope` enum('all','secretariat','vehicles') NOT NULL DEFAULT 'vehicles',
  `secretariat_id` char(36) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbook_permissions_secretariat_id_foreign` (`secretariat_id`),
  KEY `logbook_permissions_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `logbook_permissions_scope_index` (`scope`),
  CONSTRAINT `logbook_permissions_secretariat_id_foreign` FOREIGN KEY (`secretariat_id`) REFERENCES `secretariats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oil_change_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oil_change_settings` (
  `id` char(36) NOT NULL,
  `vehicle_category_id` char(36) DEFAULT NULL,
  `km_interval` int(10) unsigned NOT NULL DEFAULT 10000,
  `days_interval` int(10) unsigned NOT NULL DEFAULT 180,
  `default_liters` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oil_change_settings_vehicle_category_id_foreign` (`vehicle_category_id`),
  CONSTRAINT `oil_change_settings_vehicle_category_id_foreign` FOREIGN KEY (`vehicle_category_id`) REFERENCES `vehicle_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oil_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oil_changes` (
  `id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `inventory_item_id` char(36) DEFAULT NULL,
  `km_at_change` int(10) unsigned NOT NULL,
  `change_date` date NOT NULL,
  `liters_used` decimal(8,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `next_change_km` int(10) unsigned NOT NULL,
  `next_change_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `service_provider` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oil_changes_vehicle_id_foreign` (`vehicle_id`),
  KEY `oil_changes_user_id_foreign` (`user_id`),
  KEY `oil_changes_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `oil_changes_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  CONSTRAINT `oil_changes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `oil_changes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pdf_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdf_templates` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `header_image` varchar(255) DEFAULT NULL,
  `header_scope` varchar(255) NOT NULL DEFAULT 'all',
  `header_image_align` varchar(255) NOT NULL DEFAULT 'C',
  `header_image_width` int(11) DEFAULT NULL,
  `header_image_height` int(11) DEFAULT NULL,
  `header_text` text DEFAULT NULL,
  `header_text_align` varchar(255) NOT NULL DEFAULT 'C',
  `header_line_height` double NOT NULL DEFAULT 1.2,
  `header_image_vertical_position` varchar(255) NOT NULL DEFAULT 'inline-left',
  `footer_image` varchar(255) DEFAULT NULL,
  `footer_scope` varchar(255) NOT NULL DEFAULT 'all',
  `footer_image_align` varchar(255) NOT NULL DEFAULT 'C',
  `footer_image_width` int(11) DEFAULT NULL,
  `footer_image_height` int(11) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `footer_text_align` varchar(255) NOT NULL DEFAULT 'C',
  `footer_line_height` double NOT NULL DEFAULT 1.2,
  `footer_image_vertical_position` varchar(255) NOT NULL DEFAULT 'inline-left',
  `body_text` text DEFAULT NULL,
  `after_table_text` text DEFAULT NULL,
  `body_line_height` double NOT NULL DEFAULT 1.5,
  `paragraph_spacing` double NOT NULL DEFAULT 5,
  `heading_spacing` double NOT NULL DEFAULT 8,
  `table_style` varchar(255) NOT NULL DEFAULT 'grid',
  `table_header_bg` varchar(255) NOT NULL DEFAULT '#f3f4f6',
  `table_header_text` varchar(255) NOT NULL DEFAULT '#374151',
  `table_row_height` int(11) NOT NULL DEFAULT 10,
  `show_table_lines` tinyint(1) NOT NULL DEFAULT 1,
  `use_zebra_stripes` tinyint(1) NOT NULL DEFAULT 0,
  `table_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_columns`)),
  `cell_text_align_mode` varchar(10) NOT NULL DEFAULT 'auto',
  `cell_transform` varchar(20) NOT NULL DEFAULT 'none',
  `cell_word_wrap` tinyint(1) NOT NULL DEFAULT 1,
  `real_time_preview` tinyint(1) NOT NULL DEFAULT 1,
  `margin_top` int(11) NOT NULL DEFAULT 10,
  `margin_bottom` int(11) NOT NULL DEFAULT 10,
  `margin_left` int(11) NOT NULL DEFAULT 10,
  `margin_right` int(11) NOT NULL DEFAULT 10,
  `font_family` varchar(255) NOT NULL DEFAULT 'helvetica',
  `font_family_body` varchar(255) NOT NULL DEFAULT 'helvetica',
  `header_font_family` varchar(255) NOT NULL DEFAULT 'helvetica',
  `footer_font_family` varchar(255) NOT NULL DEFAULT 'helvetica',
  `font_size_title` int(11) NOT NULL DEFAULT 16,
  `header_font_size` int(11) NOT NULL DEFAULT 12,
  `footer_font_size` int(11) NOT NULL DEFAULT 10,
  `font_size_text` int(11) NOT NULL DEFAULT 12,
  `font_size_table` int(11) NOT NULL DEFAULT 10,
  `font_style_title` varchar(255) DEFAULT NULL,
  `header_font_style` varchar(255) DEFAULT NULL,
  `footer_font_style` varchar(255) DEFAULT NULL,
  `font_style_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prefixes` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prefixes_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hierarchy_level` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `run_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `run_signatures` (
  `id` char(36) NOT NULL,
  `run_id` char(36) NOT NULL,
  `driver_signature_id` char(36) NOT NULL,
  `driver_signed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_signature_id` char(36) DEFAULT NULL,
  `admin_signed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `run_signatures_run_id_unique` (`run_id`),
  KEY `run_signatures_driver_signature_id_foreign` (`driver_signature_id`),
  KEY `run_signatures_admin_signature_id_foreign` (`admin_signature_id`),
  CONSTRAINT `run_signatures_admin_signature_id_foreign` FOREIGN KEY (`admin_signature_id`) REFERENCES `digital_signatures` (`id`),
  CONSTRAINT `run_signatures_driver_signature_id_foreign` FOREIGN KEY (`driver_signature_id`) REFERENCES `digital_signatures` (`id`),
  CONSTRAINT `run_signatures_run_id_foreign` FOREIGN KEY (`run_id`) REFERENCES `runs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `runs` (
  `id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `start_km` bigint(20) unsigned DEFAULT NULL,
  `end_km` bigint(20) unsigned DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `stop_point` varchar(255) DEFAULT NULL,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `runs_user_id_foreign` (`user_id`),
  KEY `runs_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `runs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `runs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `secretariats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `secretariats` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `secretariats_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_order_items` (
  `id` char(36) NOT NULL,
  `service_order_id` char(36) NOT NULL,
  `inventory_item_id` char(36) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_order_items_service_order_id_foreign` (`service_order_id`),
  KEY `service_order_items_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `service_order_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  CONSTRAINT `service_order_items_service_order_id_foreign` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_order_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_order_status_histories` (
  `id` char(36) NOT NULL,
  `service_order_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `stage` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_order_status_histories_service_order_id_foreign` (`service_order_id`),
  KEY `service_order_status_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `service_order_status_histories_service_order_id_foreign` FOREIGN KEY (`service_order_id`) REFERENCES `service_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_order_status_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_orders` (
  `id` char(36) NOT NULL,
  `defect_report_id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `mechanic_id` char(36) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending_quote',
  `quote_status` enum('draft','pending_approval','approved','rejected') NOT NULL DEFAULT 'draft',
  `quote_total_amount` decimal(10,2) DEFAULT NULL,
  `quote_approver_id` char(36) DEFAULT NULL,
  `quote_approved_at` timestamp NULL DEFAULT NULL,
  `approver_notes` text DEFAULT NULL,
  `service_started_at` timestamp NULL DEFAULT NULL,
  `service_completed_at` timestamp NULL DEFAULT NULL,
  `mechanic_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_orders_defect_report_id_unique` (`defect_report_id`),
  KEY `service_orders_mechanic_id_foreign` (`mechanic_id`),
  KEY `service_orders_quote_approver_id_foreign` (`quote_approver_id`),
  KEY `service_orders_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `service_orders_defect_report_id_foreign` FOREIGN KEY (`defect_report_id`) REFERENCES `defect_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_orders_mechanic_id_foreign` FOREIGN KEY (`mechanic_id`) REFERENCES `users` (`id`),
  CONSTRAINT `service_orders_quote_approver_id_foreign` FOREIGN KEY (`quote_approver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `service_orders_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_foreign` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tire_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tire_events` (
  `id` char(36) NOT NULL,
  `tire_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `vehicle_id` char(36) DEFAULT NULL,
  `event_type` enum('Cadastro','Instalação','Rodízio','Troca','Manutenção','Recapagem','Descarte') NOT NULL,
  `description` text NOT NULL,
  `km_at_event` int(11) DEFAULT NULL COMMENT 'KM do veículo no momento do evento',
  `event_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tire_events_tire_id_foreign` (`tire_id`),
  KEY `tire_events_user_id_foreign` (`user_id`),
  KEY `tire_events_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `tire_events_tire_id_foreign` FOREIGN KEY (`tire_id`) REFERENCES `tires` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tire_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tire_events_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tires` (
  `id` char(36) NOT NULL,
  `inventory_item_id` char(36) NOT NULL,
  `brand` varchar(255) NOT NULL COMMENT 'Marca do pneu',
  `model` varchar(255) NOT NULL COMMENT 'Modelo do pneu',
  `serial_number` varchar(255) NOT NULL COMMENT 'Número de série ou de fogo',
  `dot_number` varchar(255) DEFAULT NULL COMMENT 'Código DOT do pneu',
  `purchase_date` date NOT NULL COMMENT 'Data da compra',
  `purchase_price` decimal(10,2) DEFAULT NULL COMMENT 'Valor de compra',
  `lifespan_km` int(11) NOT NULL COMMENT 'Vida útil estimada em KM',
  `current_km` int(11) NOT NULL DEFAULT 0 COMMENT 'KM rodados pelo pneu',
  `status` enum('Em Estoque','Em Uso','Em Manutenção','Recapagem','Descartado') NOT NULL DEFAULT 'Em Estoque',
  `condition` enum('Novo','Bom','Atenção','Crítico') NOT NULL DEFAULT 'Novo',
  `current_vehicle_id` char(36) DEFAULT NULL,
  `current_position` int(11) DEFAULT NULL COMMENT 'Posição no veículo (ex: 1=D.E, 2=D.D, ...)',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tires_serial_number_unique` (`serial_number`),
  KEY `tires_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `tires_current_vehicle_id_foreign` (`current_vehicle_id`),
  CONSTRAINT `tires_current_vehicle_id_foreign` FOREIGN KEY (`current_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tires_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_photos` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `photo_type` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_photos_user_id_photo_type_unique` (`user_id`,`photo_type`),
  CONSTRAINT `user_photos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `cnh` varchar(20) DEFAULT NULL,
  `cnh_expiration_date` date DEFAULT NULL,
  `cnh_category` varchar(5) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `cpf` varchar(255) NOT NULL,
  `role_id` char(36) NOT NULL,
  `secretariat_id` char(36) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_cpf_unique` (`cpf`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_secretariat_id_foreign` (`secretariat_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_secretariat_id_foreign` FOREIGN KEY (`secretariat_id`) REFERENCES `secretariats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_statuses` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_statuses_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_tire_layouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_tire_layouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Ex: Carro (4 Pneus), Caminhão Truck (10 Pneus)',
  `layout_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Estrutura JSON com posições e coordenadas para o diagrama' CHECK (json_valid(`layout_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_transfer_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_transfer_histories` (
  `id` char(36) NOT NULL,
  `vehicle_transfer_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vehicle_transfer_histories_vehicle_transfer_id_foreign` (`vehicle_transfer_id`),
  KEY `vehicle_transfer_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `vehicle_transfer_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `vehicle_transfer_histories_vehicle_transfer_id_foreign` FOREIGN KEY (`vehicle_transfer_id`) REFERENCES `vehicle_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicle_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_transfers` (
  `id` char(36) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `origin_secretariat_id` char(36) NOT NULL,
  `destination_secretariat_id` char(36) NOT NULL,
  `requester_id` char(36) NOT NULL,
  `approver_id` char(36) DEFAULT NULL,
  `type` enum('permanent','temporary') NOT NULL DEFAULT 'permanent',
  `status` enum('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `request_notes` text DEFAULT NULL,
  `approver_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_transfers_origin_secretariat_id_foreign` (`origin_secretariat_id`),
  KEY `vehicle_transfers_destination_secretariat_id_foreign` (`destination_secretariat_id`),
  KEY `vehicle_transfers_requester_id_foreign` (`requester_id`),
  KEY `vehicle_transfers_approver_id_foreign` (`approver_id`),
  KEY `vehicle_transfers_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `vehicle_transfers_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `vehicle_transfers_destination_secretariat_id_foreign` FOREIGN KEY (`destination_secretariat_id`) REFERENCES `secretariats` (`id`),
  CONSTRAINT `vehicle_transfers_origin_secretariat_id_foreign` FOREIGN KEY (`origin_secretariat_id`) REFERENCES `secretariats` (`id`),
  CONSTRAINT `vehicle_transfers_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`),
  CONSTRAINT `vehicle_transfers_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` char(36) NOT NULL,
  `prefix_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `model_year` varchar(255) NOT NULL,
  `plate` varchar(255) NOT NULL,
  `chassis` varchar(255) DEFAULT NULL,
  `renavam` varchar(255) DEFAULT NULL,
  `registration` varchar(255) DEFAULT NULL,
  `fuel_tank_capacity` int(11) NOT NULL,
  `fuel_type_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `status_id` char(36) NOT NULL,
  `secretariat_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_unique` (`plate`),
  UNIQUE KEY `vehicles_chassis_unique` (`chassis`),
  UNIQUE KEY `vehicles_renavam_unique` (`renavam`),
  KEY `vehicles_fuel_type_id_foreign` (`fuel_type_id`),
  KEY `vehicles_category_id_foreign` (`category_id`),
  KEY `vehicles_status_id_foreign` (`status_id`),
  KEY `vehicles_secretariat_id_foreign` (`secretariat_id`),
  KEY `vehicles_prefix_id_foreign` (`prefix_id`),
  CONSTRAINT `vehicles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `vehicle_categories` (`id`),
  CONSTRAINT `vehicles_fuel_type_id_foreign` FOREIGN KEY (`fuel_type_id`) REFERENCES `fuel_types` (`id`),
  CONSTRAINT `vehicles_prefix_id_foreign` FOREIGN KEY (`prefix_id`) REFERENCES `prefixes` (`id`),
  CONSTRAINT `vehicles_secretariat_id_foreign` FOREIGN KEY (`secretariat_id`) REFERENCES `secretariats` (`id`),
  CONSTRAINT `vehicles_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `vehicle_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2025_10_06_151219_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_10_06_151219_create_secretariats_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_10_06_151220_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_10_06_154659_create_fuel_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_10_06_154659_create_vehicle_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_10_06_154659_create_vehicle_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_10_06_154659_create_vehicles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_10_06_160644_create_audit_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_10_06_161328_create_user_photos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_10_06_162150_create_gas_stations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_10_06_165636_create_pdf_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_10_06_170001_create_runs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_10_06_170002_create_checklist_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_10_06_170003_create_checklists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_10_06_170004_create_checklist_answers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_10_06_171925_create_prefixes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_10_06_171926_add_prefix_id_to_vehicles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_10_06_172303_create_fuelings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_10_06_173031_create_defect_report_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_10_06_173031_create_defect_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_10_06_173033_create_defect_report_answers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_10_06_173617_create_defect_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_10_06_173650_modify_category_in_defect_report_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_10_06_175016_create_digital_signatures_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_10_06_175147_create_run_signatures_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_10_06_181030_create_inventory_item_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_10_06_181031_create_inventory_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_10_06_181031_create_inventory_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_10_06_182917_create_fines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_10_06_182918_create_fine_processes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_10_06_182918_create_fine_signatures_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_10_06_184905_create_vehicle_transfers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_10_06_184906_create_vehicle_transfer_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_10_06_190629_create_service_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_10_06_190632_create_service_order_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_10_06_190632_create_service_order_status_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_10_06_200001_create_chat_rooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_10_06_200002_create_chat_participants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_10_06_200003_create_chat_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_10_06_200004_create_chat_message_read_receipts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_10_06_200005_add_last_message_id_to_chat_rooms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_10_07_134707_create_backup_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_10_07_135324_add_cascade_deletes_to_vehicle_related_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_10_07_161729_add_cascade_delete_to_service_orders_defect_report',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_10_07_164312_create_default_passwords_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_10_07_175324_add_missing_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_10_07_180638_remove_redundant_fields_from_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_10_08_111516_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_10_08_112851_make_run_fields_nullable_for_checklist_flow',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_10_08_130235_update_runs_table_replace_origin_with_stop_point',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_10_08_140000_create_logbook_permissions_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_10_08_171500_add_multiple_secretariats_to_logbook_permissions',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_10_08_171537_add_multiple_secretariats_to_logbook_permissions',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_10_08_150216_add_approval_fields_to_checklists_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_10_08_210000_add_unit_cost_to_inventory_items_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_10_08_220000_create_oil_changes_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_10_08_220001_create_oil_change_settings_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_10_09_075137_create_tires_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_10_09_075138_create_tire_events_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_10_09_075138_create_vehicle_tire_layouts_table',4);
