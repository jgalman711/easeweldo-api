/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banks_company_id_foreign` (`company_id`),
  CONSTRAINT `banks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `biometrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biometrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint unsigned NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `biometrics_company_id_foreign` (`company_id`),
  CONSTRAINT `biometrics_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barangay_town_city_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landline_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sss_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `philhealth_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagibig_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `subscription_id` bigint unsigned NOT NULL,
  `renewed_from_id` bigint unsigned DEFAULT NULL,
  `status` enum('unpaid','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `amount_per_employee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `employee_count` int NOT NULL,
  `months` int NOT NULL DEFAULT '1',
  `amount` decimal(8,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overpaid_balance` decimal(10,2) DEFAULT '0.00',
  `start_date` timestamp NOT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_subscriptions_company_id_foreign` (`company_id`),
  KEY `company_subscriptions_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `company_subscriptions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_subscriptions_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_users_company_id_foreign` (`company_id`),
  KEY `company_users_user_id_foreign` (`user_id`),
  CONSTRAINT `company_users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `earnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `earnings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `taxable` json DEFAULT NULL,
  `non_taxable` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `earnings_company_id_foreign` (`company_id`),
  CONSTRAINT `earnings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employee_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `work_schedule_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `status` enum('inactive','active','upcoming') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `is_clock_required` tinyint(1) NOT NULL DEFAULT '1',
  `flexi_hours_required` tinyint(1) DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_schedules_employee_id_foreign` (`employee_id`),
  KEY `employee_schedules_work_schedule_id_foreign` (`work_schedule_id`),
  CONSTRAINT `employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `employee_schedules_work_schedule_id_foreign` FOREIGN KEY (`work_schedule_id`) REFERENCES `work_schedules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `company_id` bigint unsigned NOT NULL,
  `company_employee_id` bigint unsigned NOT NULL,
  `supervisor_user_id` bigint unsigned DEFAULT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('inactive','active','pending') COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_status` enum('regular','probationary','terminated','separated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_type` enum('full-time','part-time','contract') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barangay_town_city_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_hire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_termination` date DEFAULT NULL,
  `date_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sss_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagibig_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `philhealth_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_identification_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_company_id_foreign` (`company_id`),
  KEY `employees_company_id_index` (`company_id`),
  KEY `employees_deleted_at_index` (`deleted_at`),
  KEY `employees_supervisor_user_id_foreign` (`supervisor_user_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_supervisor_user_id_foreign` FOREIGN KEY (`supervisor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('sick_leave','vacation_leave','emergency_leave','leave_without_pay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hours` decimal(8,2) NOT NULL,
  `date` date NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `submitted_date` timestamp NULL DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('submitted','approved','declined','discarded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_company_id_foreign` (`company_id`),
  KEY `leaves_employee_id_foreign` (`employee_id`),
  KEY `leaves_created_by_foreign` (`created_by`),
  CONSTRAINT `leaves_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `overtime_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtime_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `submitted_date` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overtime_requests_employee_id_foreign` (`employee_id`),
  KEY `overtime_requests_company_id_foreign` (`company_id`),
  KEY `overtime_requests_approved_by_foreign` (`approved_by`),
  KEY `overtime_requests_created_by_foreign` (`created_by`),
  CONSTRAINT `overtime_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `overtime_requests_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `overtime_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `overtime_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pagibig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagibig` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_compensation` double NOT NULL,
  `max_compensation` double NOT NULL,
  `employee_contribution_rate` double NOT NULL,
  `employer_contribution_rate` double NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payroll_taxes_contributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_taxes_contributions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned NOT NULL,
  `withholding_tax` decimal(10,2) NOT NULL,
  `sss_contribution` decimal(10,2) NOT NULL,
  `pagibig_contribution` decimal(10,2) NOT NULL,
  `status` enum('processing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_taxes_contributions_payroll_id_foreign` (`payroll_id`),
  KEY `payroll_taxes_contributions_company_id_foreign` (`company_id`),
  CONSTRAINT `payroll_taxes_contributions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `payroll_taxes_contributions_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `period_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `status` enum('to-pay','paid','cancelled','failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `attendance_earnings` json DEFAULT NULL,
  `leaves` json DEFAULT NULL,
  `taxable_earnings` json DEFAULT NULL,
  `non_taxable_earnings` json DEFAULT NULL,
  `other_deductions` json DEFAULT NULL,
  `holidays` json DEFAULT NULL,
  `holidays_worked` json DEFAULT NULL,
  `sss_contributions` decimal(10,2) DEFAULT NULL,
  `philhealth_contributions` decimal(10,2) DEFAULT NULL,
  `pagibig_contributions` decimal(10,2) DEFAULT NULL,
  `withheld_tax` decimal(10,2) DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payrolls_employee_id_index` (`employee_id`),
  KEY `payrolls_period_id_index` (`period_id`),
  KEY `payrolls_deleted_at_index` (`deleted_at`),
  CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `payrolls_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `company_period_id` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('regular','special','nth_month_pay','final') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtype` enum('semi-monthly','monthly','weekly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `salary_date` date DEFAULT NULL,
  `status` enum('uninitialized','failed','pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `periods_company_id_foreign` (`company_id`),
  CONSTRAINT `periods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `philhealth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `philhealth` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_contribution` double NOT NULL,
  `max_contribution` double NOT NULL,
  `contribution_rate` double NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `process_approval_flow_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_approval_flow_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `process_approval_flow_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `permissions` json DEFAULT NULL,
  `order` int DEFAULT NULL,
  `action` enum('APPROVE','VERIFY','CHECK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'APPROVE',
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `process_approval_flow_steps_process_approval_flow_id_foreign` (`process_approval_flow_id`),
  KEY `process_approval_flow_steps_role_id_index` (`role_id`),
  KEY `process_approval_flow_steps_order_index` (`order`),
  CONSTRAINT `process_approval_flow_steps_process_approval_flow_id_foreign` FOREIGN KEY (`process_approval_flow_id`) REFERENCES `process_approval_flows` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `process_approval_flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_approval_flows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approvable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `process_approval_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_approval_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approvable_id` bigint unsigned NOT NULL,
  `steps` json DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Created',
  `creator_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `process_approval_statuses_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `process_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `process_approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approvable_id` bigint unsigned NOT NULL,
  `process_approval_flow_step_id` bigint unsigned DEFAULT NULL,
  `approval_action` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `approver_name` text COLLATE utf8mb4_unicode_ci,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `process_approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  KEY `process_approvals_process_approval_flow_step_id_foreign` (`process_approval_flow_step_id`),
  KEY `process_approvals_user_id_foreign` (`user_id`),
  CONSTRAINT `process_approvals_process_approval_flow_step_id_foreign` FOREIGN KEY (`process_approval_flow_step_id`) REFERENCES `process_approval_flow_steps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `process_approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salary_computations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_computations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `basic_salary` double(8,2) DEFAULT NULL,
  `hourly_rate` double(8,2) DEFAULT NULL,
  `daily_rate` double(8,2) DEFAULT NULL,
  `non_taxable_earnings` json DEFAULT NULL,
  `taxable_earnings` json DEFAULT NULL,
  `other_deductions` json DEFAULT NULL,
  `working_hours_per_day` int DEFAULT NULL,
  `break_hours_per_day` int NOT NULL DEFAULT '1',
  `working_days_per_week` int DEFAULT NULL,
  `overtime_rate` double(8,2) NOT NULL,
  `night_diff_rate` double(8,2) NOT NULL,
  `regular_holiday_rate` double(8,2) NOT NULL,
  `special_holiday_rate` double(8,2) NOT NULL,
  `total_sick_leave_hours` double(8,2) NOT NULL,
  `total_vacation_leave_hours` double(8,2) NOT NULL,
  `available_sick_leave_hours` double(8,2) NOT NULL,
  `available_vacation_leave_hours` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_computations_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_computations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `period_cycle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_day` text COLLATE utf8mb4_unicode_ci,
  `grace_period` int DEFAULT NULL,
  `minimum_overtime` int DEFAULT NULL,
  `is_ot_auto_approve` tinyint(1) NOT NULL DEFAULT '0',
  `auto_send_email_to_bank` tinyint(1) NOT NULL DEFAULT '0',
  `auto_pay_disbursement` tinyint(1) NOT NULL DEFAULT '0',
  `clock_action_required` tinyint(1) NOT NULL DEFAULT '0',
  `disbursement_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overtime_rate` decimal(8,2) NOT NULL DEFAULT '1.00',
  `leaves_convertible` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_company_id_foreign` (`company_id`),
  CONSTRAINT `settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sss` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_compensation` double NOT NULL,
  `max_compensation` double NOT NULL,
  `employer_contribution` double NOT NULL,
  `employee_contribution` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscription_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `months` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_employee` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_prices_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `subscription_prices_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('core','bundle','trial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `features` blob NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('weekly','semi-monthly','monthly') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_compensation` double NOT NULL,
  `max_compensation` double NOT NULL,
  `base_tax` double NOT NULL,
  `over_compensation_level_rate` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `time_corrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_corrections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_corrections_employee_id_foreign` (`employee_id`),
  KEY `time_corrections_company_id_foreign` (`company_id`),
  CONSTRAINT `time_corrections_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `time_corrections_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `time_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `expected_clock_in` timestamp NULL DEFAULT NULL,
  `expected_clock_out` timestamp NULL DEFAULT NULL,
  `original_clock_in` timestamp NULL DEFAULT NULL,
  `original_clock_out` timestamp NULL DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_records_employee_id_foreign` (`employee_id`),
  KEY `time_records_company_id_foreign` (`company_id`),
  CONSTRAINT `time_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `time_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `temporary_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temporary_password_expires_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `type` enum('standard','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monday_clock_in_time` time DEFAULT NULL,
  `monday_clock_out_time` time DEFAULT NULL,
  `tuesday_clock_in_time` time DEFAULT NULL,
  `tuesday_clock_out_time` time DEFAULT NULL,
  `wednesday_clock_in_time` time DEFAULT NULL,
  `wednesday_clock_out_time` time DEFAULT NULL,
  `thursday_clock_in_time` time DEFAULT NULL,
  `thursday_clock_out_time` time DEFAULT NULL,
  `friday_clock_in_time` time DEFAULT NULL,
  `friday_clock_out_time` time DEFAULT NULL,
  `saturday_clock_in_time` time DEFAULT NULL,
  `saturday_clock_out_time` time DEFAULT NULL,
  `sunday_clock_in_time` time DEFAULT NULL,
  `sunday_clock_out_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_schedules_company_id_foreign` (`company_id`),
  CONSTRAINT `work_schedules_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2023_04_28_160714_update_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2023_04_28_161456_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2023_04_28_184040_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2023_04_29_070311_create_employees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2023_04_30_072506_create_time_records_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2023_04_30_091646_salary_computations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2023_04_30_174908_create_periods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2023_04_30_193236_create_payrolls_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2023_05_01_071911_create_work_schedules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2023_05_01_072354_create_employee_schedules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2023_05_01_072500_create_holidays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2023_05_05_125829_recreate_payrolls_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2023_05_06_164256_create_tax_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2023_05_07_100536_create_ssses_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2023_05_07_120210_create_philhealth_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2023_05_07_124712_create_pagibig_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2023_05_07_155151_create_payroll_taxes_contributions_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2023_05_08_171109_create_leaves_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2023_05_18_025224_create_holidays_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2023_05_19_140717_add_pay_date_in_periods_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2023_05_23_111214_update_salary_computations_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2023_05_23_135352_update_payrolls_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2023_05_26_143633_update_company_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2023_05_27_002103_update_time_records_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2023_05_27_130724_add_details_to_companies_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2023_05_27_133506_create_subscriptions_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2023_05_27_134946_create_company_subscriptions_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2023_05_28_235824_update_employees_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2023_05_29_115418_add_profile_picture_to_employees',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2023_06_01_222551_add_soft_deletes_to_time_records_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2023_06_02_000707_create_settings_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2023_06_06_143604_update_employees_and_add_username_column',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2023_06_08_004247_update_employees_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2023_06_09_025320_update_payrolls_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2023_06_10_115239_change_email_column_name_in_users_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2023_06_11_170001_update_payrolls_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2023_06_18_164646_create_new_payrolls_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2023_06_19_021622_update_employees_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2023_06_20_131745_update_salary_computations_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2023_06_20_135248_update_payrolls_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2023_06_21_004056_update_holidays_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2023_06_24_215943_update_periods_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2023_06_25_173555_create_table_employee_year_to_dates',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2023_06_25_213406_update_payroll_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2023_06_27_200145_add_status_to_payrolls_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2023_07_03_010258_update_payrolls_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2023_07_06_223654_update_settings_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2023_07_10_002453_add_leaves_pay_columns_to_payrolls_and_employee_year_to_dates',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2023_07_30_011257_add_columns_to_payrolls_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2023_07_30_011312_add_columns_to_employee_year_to_dates_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2023_08_02_224728_add_nontaxables_to_table_payrolls',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2023_08_02_230655_add_non_taxable_income_in_salary_computations',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2023_08_02_230930_add_non_taxable_income_ytd_in_employee_year_to_dates',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2023_08_11_113733_drop_columns_from_periods_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2023_08_12_022653_create_biometrics_table',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2023_08_13_020432_create_earnings_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2023_08_13_231916_update_payrolls_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2023_08_14_030026_drop_y_t_d_table',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2023_08_14_030440_update_salary_computation',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2023_08_14_233035_add_company_employee_id_to_employees_table',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2023_08_15_163434_update_payrolls_table',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2023_08_18_171207_update_time_records',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2023_08_21_001930_modify_employees_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2023_08_21_014224_modify_company_subscriptions_table',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2023_08_21_141148_modify_users_table_remove_username_unique',48);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2023_08_22_192553_update_subscriptions_table',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2023_08_25_002937_create_company_users_table',50);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2023_08_26_152443_create_pricing_tiers_table',51);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2023_08_31_161802_update_payrolls_table',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2023_09_03_143717_add_employee_count_to_company_subscriptions',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2023_09_03_233752_create_payment_methods',54);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2023_09_05_205024_add_column_break_hours_per_day_in_salary_computations_table',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (241,'2023_09_06_214052_add_column_months_in_company_subscription_table',56);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (243,'2023_09_07_104733_remove_columns_in_companies_table',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (245,'2023_09_07_153002_add_column_from_subscription_id_in_company_subscription_table',58);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (246,'2023_09_10_182848_update_users_and_employees_table',59);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (248,'2023_09_10_203241_add_user_id_to_employees_table',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (249,'2023_09_11_174134_add_status_in_table_users',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (250,'2023_09_12_120658_add_temporary_password_in_users',62);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (251,'2023_09_12_220720_remove_unique_constraint_from_email_address_in_users',63);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2023_09_14_121055_add_column_type_in_payrolls',64);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2023_09_15_201458_add_pay_date_in_payrolls',65);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (255,'2023_09_17_143439_add_employee_number_in_employees',66);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2023_09_21_194343_update_features_to_blob_in_subscriptions',67);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2023_09_22_195346_create_overtime_requests_table',68);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (262,'2023_09_23_003233_add_columns_to_leaves_table',69);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (264,'2023_10_04_025343_update_leaves_table',70);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2023_10_19_155609_update_employee_schedule',71);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2023_10_27_142459_add_types_in_periods',72);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2023_10_27_195714_make_company_period_id_nullable_in_periods',73);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2023_11_11_215214_update_employees',74);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2024_01_30_235256_create_time_corrections_table',75);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2024_02_01_024953_add_title_to_leaves_table',76);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2024_02_18_011058_add_type_to_work_schedules_table',77);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (282,'2024_02_18_215133_add_remarks_and_status_to_employee_schedules_table',78);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (283,'2024_02_21_234127_add_payroll_number_in_payrolls',79);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2024_02_22_131006_add_holidays_worked_column_to_holidays_table',80);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2024_02_22_155017_create_payroll_attendances_table',81);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2024_02_22_160137_remove_columns_from_payrolls_table',82);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2024_02_22_201831_change_status_enum_in_payrolls_table',83);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2024_02_22_210818_modify_periods_table',84);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2024_02_24_040242_modify_payrolls_table',85);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2024_03_01_031451_make_dates_nullable_in_periods',86);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2024_03_12_010953_update_periods_table_make_start_date_and_end_date_nullable',87);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (298,'2024_03_12_222737_add_deductions_to_payrolls_table',88);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2024_03_15_013232_add_columns_to_company_settings_table',89);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2024_03_15_014042_create_banks_table',89);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2024_03_28_024152_add_deductions_in_salary_computation',90);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2018_08_08_100000_create_telescope_entries_table',91);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (346,'2024_04_03_003232_add_index_to_personal_access_tokens_table',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (347,'2024_04_03_020946_index_payroll_columns',92);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (350,'1_create_process_approval_flows_table',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (351,'2_create_process_approval_flow_steps_table',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (352,'3_create_process_approvals_table',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (353,'4_create_process_approval_statuses_table',93);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (354,'2024_04_09_213603_add_leave_approver_role',94);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (356,'2024_04_09_231309_update_leaves_status_enum',95);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (357,'2024_04_10_145434_update_company_settings',96);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (364,'2024_04_11_004921_add_supervisor_in_employees_table',97);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (365,'2024_04_11_230233_remove_username_in_users',97);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (366,'2024_04_11_230437_add_approver_role',98);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (367,'2024_04_25_022438_add_title_in_leaves',99);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (369,'2024_04_27_015538_add_error_in_payrolls',100);
