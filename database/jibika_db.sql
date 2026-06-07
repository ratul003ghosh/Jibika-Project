-- ============================================================
-- Jibika Project (জীবিকা) - Complete Database Dump
-- Database   : jibika_db
-- Server     : MariaDB 10.4.32 / Port: 3307
-- Default PW : password123 (bcrypt hashed)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- ============================================================
-- TABLE ORDER (dependency chain):
--   districts
--   users
--   subscribers
--   upazilas          (-> districts)
--   wards             (-> upazilas)
--   jobs              (-> districts, upazilas, wards)
--   employer_profiles (-> users)
--   admin_profiles    (-> users)
--   job_seeker_profiles (-> users, districts, upazilas, wards)
--   skills            (-> users)
--   employment_status (-> users)
--   employment_status_logs (-> users)
--   applications      (-> jobs, users)
--   saved_jobs        (-> jobs, users)
--   activity_logs     (-> users)
-- ============================================================


-- ============================================================
-- 1. districts
-- ============================================================
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `district_id`   int(11)      NOT NULL AUTO_INCREMENT,
  `district_name` varchar(100) NOT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `district_name` (`district_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `districts` VALUES
(5,'Barishal'),(2,'Chattogram'),(1,'Dhaka'),(3,'Khulna'),
(8,'Mymensingh'),(4,'Rajshahi'),(7,'Rangpur'),(6,'Sylhet');


-- ============================================================
-- 2. users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `full_name`  varchar(100) DEFAULT NULL,
  `email`      varchar(100) DEFAULT NULL,
  `phone`      varchar(20)  DEFAULT NULL,
  `password`   varchar(255) DEFAULT NULL,
  `role`       enum('job_seeker','employer','admin') NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- All accounts share password: password123
INSERT INTO `users` VALUES
(1, 'Test User',    'testuser@gmail.com',        '01728248170','$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','job_seeker','2026-04-05 19:01:07'),
(3, 'Employee1',    'employee1@gmail.com',        '01728248170','$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','employer',  '2026-04-05 19:46:36'),
(7, 'SHARIF AHMED', 'sharifahmedadmin@gmail.com', '01728248170','$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','employer',  '2026-04-05 20:50:36'),
(8, 'SHARIF AHMED', 'sharifahmed@gmail.com',      '01728248170','$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','admin',     '2026-04-05 21:10:56'),
(9, 'Ratul',        'ratul@gmail.com',             '4424',       '$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','job_seeker','2026-04-10 06:06:48'),
(12,'Ratul',        'ratu22l@gmail.com',            '01222222222','$2y$10$mNSKeYgjE7HvY1xb/ARGMuYnpO4qdUKU4j61xdHqkf2lMfgeQSerK','employer',  '2026-04-10 16:36:25'),
(13,'Job Seeker 1', 'jobseeker1@gmail.com',         NULL,         '$2y$10$UvDq90gRXgCsYiMYe6LaGeB8L.Hk4eFEu.1Jcz.BlwZ1G605zCtCK', 'job_seeker','2026-05-01 00:00:00'),
(14,'Tuhin',        'tuhin123@gmail.com',            NULL,         '$2y$10$UvDq90gRXgCsYiMYe6LaGeB8L.Hk4eFEu.1Jcz.BlwZ1G605zCtCK', 'job_seeker','2026-05-01 00:00:00'),
(15,'Test Seeker',  'testseeker@example.com',        NULL,         '$2y$10$cBXvsOn1XFX19ZBYfh6sg.5LiS5nkmWaXzhhLqRTJfSodn.AVTP8q',  'job_seeker','2026-05-01 00:00:00');


-- ============================================================
-- 3. subscribers
-- ============================================================
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `email`         varchar(150) NOT NULL,
  `subscribed_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- 4. upazilas  (depends on districts)
-- ============================================================
DROP TABLE IF EXISTS `upazilas`;
CREATE TABLE `upazilas` (
  `upazila_id`   int(11)      NOT NULL AUTO_INCREMENT,
  `district_id`  int(11)      NOT NULL,
  `upazila_name` varchar(100) NOT NULL,
  PRIMARY KEY (`upazila_id`),
  KEY `district_id` (`district_id`),
  CONSTRAINT `upazilas_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `upazilas` VALUES
(1,1,'Dhamrai'),(2,1,'Savar'),(3,1,'Keraniganj'),
(4,2,'Patiya'),(5,2,'Raozan'),
(6,3,'Batiaghata'),
(7,4,'Paba'),
(8,5,'Babuganj'),
(9,6,'Beanibazar'),
(10,7,'Pirgacha'),
(11,8,'Trishal');


-- ============================================================
-- 5. wards  (depends on upazilas)
-- ============================================================
DROP TABLE IF EXISTS `wards`;
CREATE TABLE `wards` (
  `ward_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `upazila_id` int(11)      NOT NULL,
  `ward_name`  varchar(100) NOT NULL,
  PRIMARY KEY (`ward_id`),
  KEY `upazila_id` (`upazila_id`),
  CONSTRAINT `wards_ibfk_1` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`upazila_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `wards` VALUES
(1,1,'Ward 1'),(2,1,'Ward 2'),
(3,2,'Ward 1'),(4,2,'Ward 2'),
(5,3,'Ward 1'),(6,4,'Ward 1'),(7,5,'Ward 1'),
(8,6,'Ward 1'),(9,7,'Ward 1'),(10,8,'Ward 1'),
(11,9,'Ward 1'),(12,10,'Ward 1'),(13,11,'Ward 1');


-- ============================================================
-- 6. jobs  (depends on districts, upazilas, wards)
--    FIXED: added job_category, job_type, experience_required,
--           status, application_deadline, vacancy,
--           education_required, salary_type
-- ============================================================
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `job_id`               int(11)      NOT NULL AUTO_INCREMENT,
  `employer_id`          int(11)      DEFAULT NULL,
  `title`                varchar(150) DEFAULT NULL,
  `description`          text         DEFAULT NULL,
  `location`             varchar(100) DEFAULT NULL,
  `salary`               varchar(50)  DEFAULT NULL,
  `created_at`           timestamp    NOT NULL DEFAULT current_timestamp(),
  `district_id`          int(11)      DEFAULT NULL,
  `upazila_id`           int(11)      DEFAULT NULL,
  `ward_id`              int(11)      DEFAULT NULL,
  `job_category`         varchar(100) DEFAULT NULL,
  `job_type`             varchar(50)  DEFAULT 'Full-time',
  `experience_required`  varchar(100) DEFAULT NULL,
  `status`               enum('active','closed') NOT NULL DEFAULT 'active',
  `application_deadline` date         DEFAULT NULL,
  `vacancy`              int(11)      DEFAULT 1,
  `education_required`   varchar(150) DEFAULT NULL,
  `salary_type`          varchar(20)  DEFAULT 'Negotiable',
  PRIMARY KEY (`job_id`),
  KEY `fk_jobs_district` (`district_id`),
  KEY `fk_jobs_upazila`  (`upazila_id`),
  KEY `fk_jobs_ward`     (`ward_id`),
  CONSTRAINT `fk_jobs_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jobs_upazila`  FOREIGN KEY (`upazila_id`)  REFERENCES `upazilas`  (`upazila_id`)  ON DELETE SET NULL,
  CONSTRAINT `fk_jobs_ward`     FOREIGN KEY (`ward_id`)     REFERENCES `wards`     (`ward_id`)     ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `jobs`
  (`job_id`,`employer_id`,`title`,`description`,`location`,`salary`,`created_at`,
   `district_id`,`upazila_id`,`ward_id`,
   `job_category`,`job_type`,`experience_required`,`status`,`application_deadline`,
   `vacancy`,`education_required`,`salary_type`)
VALUES
(1,3,'Web Developer','Need a junior web developer with HTML, CSS skills',
 'Dhaka','15000','2026-04-05 19:49:21',NULL,NULL,NULL,
 'IT & Computer','Full-time','Entry Level','active',NULL,1,NULL,'Fixed'),
(2,3,'Plumber','Plumber Need Emergency',
 'Bogura','158000','2026-04-05 21:49:57',5,9,10,
 'Office Support','Full-time','Entry Level','active',NULL,2,NULL,'Fixed');


-- ============================================================
-- 7. employer_profiles  (depends on users) -- NEW TABLE
-- ============================================================
DROP TABLE IF EXISTS `employer_profiles`;
CREATE TABLE `employer_profiles` (
  `employer_profile_id` int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`             int(11)      NOT NULL,
  `company_name`        varchar(150) DEFAULT NULL,
  `company_description` text         DEFAULT NULL,
  `company_website`     varchar(200) DEFAULT NULL,
  `company_phone`       varchar(20)  DEFAULT NULL,
  `company_email`       varchar(100) DEFAULT NULL,
  `company_address`     text         DEFAULT NULL,
  `district_id`         int(11)      DEFAULT NULL,
  `upazila_id`          int(11)      DEFAULT NULL,
  `ward_id`             int(11)      DEFAULT NULL,
  `created_at`          timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`employer_profile_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `employer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `employer_profiles` (`user_id`,`company_name`,`company_description`,`company_phone`,`company_email`) VALUES
(3, 'Employee1',    'A registered employer on Jibika.','01728248170','employee1@gmail.com'),
(7, 'SHARIF AHMED', 'A registered employer on Jibika.','01728248170','sharifahmedadmin@gmail.com'),
(12,'Ratul',        'A registered employer on Jibika.','01222222222','ratu22l@gmail.com');


-- ============================================================
-- 8. admin_profiles  (depends on users)
-- ============================================================
DROP TABLE IF EXISTS `admin_profiles`;
CREATE TABLE `admin_profiles` (
  `admin_profile_id` int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`          int(11)      NOT NULL,
  `designation`      varchar(100) DEFAULT NULL,
  `department`       varchar(100) DEFAULT NULL,
  `created_at`       timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_profile_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `admin_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- 9. job_seeker_profiles  (depends on users, districts, upazilas, wards)
-- ============================================================
DROP TABLE IF EXISTS `job_seeker_profiles`;
CREATE TABLE `job_seeker_profiles` (
  `profile_id`  int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)      NOT NULL,
  `nid`         varchar(30)  DEFAULT NULL,
  `district`    varchar(100) DEFAULT NULL,
  `upazila`     varchar(100) DEFAULT NULL,
  `education`   varchar(150) DEFAULT NULL,
  `skills`      text         DEFAULT NULL,
  `about`       text         DEFAULT NULL,
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  `district_id` int(11)      DEFAULT NULL,
  `upazila_id`  int(11)      DEFAULT NULL,
  `ward_id`     int(11)      DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `user_id`             (`user_id`),
  KEY `fk_profile_district` (`district_id`),
  KEY `fk_profile_upazila`  (`upazila_id`),
  KEY `fk_profile_ward`     (`ward_id`),
  CONSTRAINT `fk_profile_district`       FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_profile_upazila`        FOREIGN KEY (`upazila_id`)  REFERENCES `upazilas`  (`upazila_id`)  ON DELETE SET NULL,
  CONSTRAINT `fk_profile_ward`           FOREIGN KEY (`ward_id`)     REFERENCES `wards`     (`ward_id`)     ON DELETE SET NULL,
  CONSTRAINT `job_seeker_profiles_ibfk_1` FOREIGN KEY (`user_id`)   REFERENCES `users`     (`user_id`)     ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `job_seeker_profiles` VALUES
(1,1,'0102992193','Bogura','Shajahanpur','BSCSE','Nai','Ki r boli','2026-04-05 19:21:48',4,4,4);


-- ============================================================
-- 10. skills  (depends on users)
-- ============================================================
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `skill_id`   int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`    int(11)      DEFAULT NULL,
  `skill_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`skill_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `skills` VALUES
(1,1,'HTML',   '2026-04-05 19:40:33'),
(3,1,'DRIVING','2026-04-05 19:40:50'),
(4,1,'CSS',    '2026-04-05 21:25:57');


-- ============================================================
-- 11. employment_status  (depends on users)
-- ============================================================
DROP TABLE IF EXISTS `employment_status`;
CREATE TABLE `employment_status` (
  `status_id`      int(11) NOT NULL AUTO_INCREMENT,
  `user_id`        int(11) NOT NULL,
  `current_status` enum('unemployed','employed','training','self_employed') NOT NULL DEFAULT 'unemployed',
  `remarks`        varchar(255) DEFAULT NULL,
  `updated_at`     timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `employment_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `employment_status` VALUES
(1,1,'training','Under Training','2026-04-05 22:58:56');


-- ============================================================
-- 12. employment_status_logs  (depends on users) -- NEW TABLE
-- ============================================================
DROP TABLE IF EXISTS `employment_status_logs`;
CREATE TABLE `employment_status_logs` (
  `log_id`     int(11)     NOT NULL AUTO_INCREMENT,
  `user_id`    int(11)     NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(11)     DEFAULT NULL,
  `changed_at` timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- 13. applications  (depends on jobs, users)
-- ============================================================
DROP TABLE IF EXISTS `applications`;
CREATE TABLE `applications` (
  `application_id` int(11)     NOT NULL AUTO_INCREMENT,
  `job_id`         int(11)     NOT NULL,
  `user_id`        int(11)     NOT NULL,
  `status`         varchar(50) DEFAULT 'Pending',
  `applied_at`     timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`application_id`),
  KEY `job_id`  (`job_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`)  REFERENCES `jobs`  (`job_id`)  ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `applications` VALUES
(1,1,1,'Rejected','2026-04-05 19:56:58'),
(2,2,1,'Accepted','2026-04-05 21:50:44'),
(3,1,9,'Pending', '2026-04-10 08:20:51');


-- ============================================================
-- 14. saved_jobs  (depends on jobs, users) -- NEW TABLE
-- ============================================================
DROP TABLE IF EXISTS `saved_jobs`;
CREATE TABLE `saved_jobs` (
  `id`       int(11)   NOT NULL AUTO_INCREMENT,
  `user_id`  int(11)   NOT NULL,
  `job_id`   int(11)   NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_save` (`user_id`,`job_id`),
  KEY `job_id` (`job_id`),
  CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`)  REFERENCES `jobs`  (`job_id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- 15. activity_logs  (depends on users)
-- ============================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `activity_id` int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)      DEFAULT NULL,
  `action`      varchar(150) NOT NULL,
  `description` text         DEFAULT NULL,
  `ip_address`  varchar(50)  DEFAULT NULL,
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`activity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` VALUES
(1,3,'Application Accepted','Employer accepted applicant and system updated employment status to employed','::1','2026-05-08 13:48:26'),
(2,3,'Application Accepted','Employer accepted applicant and system updated employment status to employed','::1','2026-05-08 13:48:30'),
(3,3,'Application Accepted','Employer accepted applicant and system updated employment status to employed','::1','2026-05-08 13:57:30'),
(4,3,'Application Accepted','Employer accepted applicant and system updated employment status to employed','::1','2026-05-08 14:56:11'),
(5,3,'Application Accepted','Employer accepted applicant and system updated employment status to employed','::1','2026-05-08 16:53:44');


-- ============================================================
-- Restore settings
-- ============================================================
COMMIT;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- ============================================================
-- TEST ACCOUNTS  (password for ALL accounts: password123)
-- ============================================================
-- Job Seeker : testuser@gmail.com       / password123
-- Employer   : employee1@gmail.com      / password123
-- Admin      : sharifahmed@gmail.com    / password123
-- App URL    : http://localhost:8000/
-- ============================================================
