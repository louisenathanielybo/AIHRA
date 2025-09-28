-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for aihra
CREATE DATABASE IF NOT EXISTS `aihra` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `aihra`;

-- Dumping structure for table aihra.announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employeeNum` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` text,
  `image` blob,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `isActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.announcements: ~0 rows (approximately)

-- Dumping structure for table aihra.category
CREATE TABLE IF NOT EXISTS `category` (
  `categoryID` varchar(50) NOT NULL,
  `categoryName` enum('Employment','Benefits','Promotion','Employee Development') DEFAULT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.category: ~0 rows (approximately)

-- Dumping structure for table aihra.chathistory
CREATE TABLE IF NOT EXISTS `chathistory` (
  `historyID` varchar(50) NOT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`historyID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `chathistory_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.chathistory: ~0 rows (approximately)

-- Dumping structure for table aihra.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

-- Dumping data for table aihra.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table aihra.feedback
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedbackID` varchar(50) NOT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `queryID` varchar(50) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `suggestion` text,
  `timeStamp` datetime DEFAULT NULL,
  PRIMARY KEY (`feedbackID`),
  KEY `employeeNum` (`employeeNum`),
  KEY `queryID` (`queryID`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`),
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`queryID`) REFERENCES `queries` (`queryID`),
  CONSTRAINT `feedback_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.feedback: ~0 rows (approximately)

-- Dumping structure for table aihra.flaggedresponse
CREATE TABLE IF NOT EXISTS `flaggedresponse` (
  `flaggedID` varchar(50) NOT NULL,
  `queryID` varchar(50) DEFAULT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `reasonID` varchar(50) DEFAULT NULL,
  `timeStamp` datetime DEFAULT NULL,
  `status` enum('Pending','Reviewed','Resolved') DEFAULT NULL,
  PRIMARY KEY (`flaggedID`),
  KEY `queryID` (`queryID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `flaggedresponse_ibfk_1` FOREIGN KEY (`queryID`) REFERENCES `queries` (`queryID`),
  CONSTRAINT `flaggedresponse_ibfk_2` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.flaggedresponse: ~0 rows (approximately)

-- Dumping structure for table aihra.guidedquery
CREATE TABLE IF NOT EXISTS `guidedquery` (
  `GQ_ID` varchar(50) NOT NULL,
  `knowledgeID` varchar(50) DEFAULT NULL,
  `categoryID` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`GQ_ID`),
  KEY `knowledgeID` (`knowledgeID`),
  KEY `categoryID` (`categoryID`),
  CONSTRAINT `guidedquery_ibfk_1` FOREIGN KEY (`knowledgeID`) REFERENCES `knowledgebase` (`knowledgeID`),
  CONSTRAINT `guidedquery_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `category` (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.guidedquery: ~0 rows (approximately)

-- Dumping structure for table aihra.interactionlog
CREATE TABLE IF NOT EXISTS `interactionlog` (
  `interactionID` varchar(50) NOT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `historyID` varchar(50) DEFAULT NULL,
  `queryID` varchar(50) DEFAULT NULL,
  `interactionType` varchar(50) DEFAULT NULL,
  `timeStamp` datetime DEFAULT NULL,
  PRIMARY KEY (`interactionID`),
  KEY `employeeNum` (`employeeNum`),
  KEY `historyID` (`historyID`),
  KEY `queryID` (`queryID`),
  CONSTRAINT `interactionlog_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`),
  CONSTRAINT `interactionlog_ibfk_2` FOREIGN KEY (`historyID`) REFERENCES `chathistory` (`historyID`),
  CONSTRAINT `interactionlog_ibfk_3` FOREIGN KEY (`queryID`) REFERENCES `queries` (`queryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.interactionlog: ~0 rows (approximately)

-- Dumping structure for table aihra.kblog
CREATE TABLE IF NOT EXISTS `kblog` (
  `logID` varchar(50) NOT NULL,
  `knowledgeID` varchar(50) DEFAULT NULL,
  `action` enum('Add','Edit','Delete','Import','Export') DEFAULT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `timeStamp` datetime DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `knowledgeID` (`knowledgeID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `kblog_ibfk_1` FOREIGN KEY (`knowledgeID`) REFERENCES `knowledgebase` (`knowledgeID`),
  CONSTRAINT `kblog_ibfk_2` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.kblog: ~0 rows (approximately)

-- Dumping structure for table aihra.knowledgebase
CREATE TABLE IF NOT EXISTS `knowledgebase` (
  `knowledgeID` varchar(50) NOT NULL,
  `categoryID` varchar(50) DEFAULT NULL,
  `sampleQuestion` text,
  `answer` text,
  `lastUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`knowledgeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.knowledgebase: ~0 rows (approximately)

-- Dumping structure for table aihra.knowledge_base
CREATE TABLE IF NOT EXISTS `knowledge_base` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sampleQuestion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table aihra.knowledge_base: ~0 rows (approximately)

-- Dumping structure for table aihra.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table aihra.migrations: ~4 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(2, '2019_08_19_000000_create_failed_jobs_table', 1),
	(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(4, '2025_09_24_043038_update_users_table', 2),
	(5, '2025_01_01_000000_create_knowledge_base_table', 3);

-- Dumping structure for table aihra.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table aihra.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table aihra.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
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

-- Dumping data for table aihra.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table aihra.queries
CREATE TABLE IF NOT EXISTS `queries` (
  `queryID` varchar(50) NOT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `question` text,
  `response` text,
  `confidenceScore` float DEFAULT NULL,
  `queryType` enum('Employment','Benefits','Promotion','Employee Development','Dialogflow') NOT NULL,
  `questionTime` datetime DEFAULT NULL,
  `responseTime` datetime DEFAULT NULL,
  `isEscalated` tinyint(1) DEFAULT '0',
  `handledBy` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`queryID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `queries_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.queries: ~23 rows (approximately)
INSERT INTO `queries` (`queryID`, `employeeNum`, `question`, `response`, `confidenceScore`, `queryType`, `questionTime`, `responseTime`, `isEscalated`, `handledBy`) VALUES
	('0ec55b61-d2ff-4a77-950a-f6d24ba2542d', '0', 'leave credits', '"Leave credits vary: College faculty have 3 vacation and 3 sick leave credits, Basic Education teachers have 12 vacation and 12 sick leave credits, while office and general services staff have 15 vacation and 15 sick leave credits per year. Unused credits may be monetized."', 0.873461, 'Dialogflow', '2025-09-25 00:30:04', '2025-09-25 00:30:04', 0, 'Bot'),
	('1bc666bd-c5e3-4094-92db-fb08d64202be', '0', 'thank you', 'It\'s my pleasure to help.', 1, 'Dialogflow', '2025-09-28 13:56:45', '2025-09-28 13:56:45', 0, 'Bot'),
	('1dec4355-057b-4239-9717-0a57bb1270a7', 'SYSTEM', 'test', 'ok', 1, 'Dialogflow', '2025-09-24 23:22:21', '2025-09-24 23:22:21', 0, 'Bot'),
	('23ca19c5-f4f6-46ee-8cbc-34218aeb256f', '0', 'hi', '“Good day! What HR-related concern would you like me to check for you?”', 1, 'Dialogflow', '2025-09-28 13:54:35', '2025-09-28 13:54:35', 0, 'Bot'),
	('3bad64af-2948-4040-b927-71e9811d2bd6', '0', 'sss?', 'Sorry, can you say that again?', 1, 'Dialogflow', '2025-09-25 00:27:55', '2025-09-25 00:27:55', 0, 'Bot'),
	('4964762e-6966-4ff8-b471-1fc03897cfae', '0', 'sss?', 'I didn\'t get that. Can you say it again?', 1, 'Dialogflow', '2025-09-24 23:42:06', '2025-09-24 23:42:06', 0, 'Bot'),
	('4f7deef4-7d98-445b-9443-df94912aa608', '0', 'bye', '“Goodbye! Have a productive day at DWCC.”', 1, 'Dialogflow', '2025-09-28 13:55:48', '2025-09-28 13:55:48', 0, 'Bot'),
	('553800ea-4cfd-4ee6-85fb-4492d1f90cf3', '0', 'can we apply for loans?', 'Employees may avail of multi-purpose and housing loans through Pag-IBIG, and retirement benefits as mandated by law. The school also manages retirement plans in compliance with labor regulations.', 1, 'Dialogflow', '2025-09-25 00:29:21', '2025-09-25 00:29:21', 0, 'Bot'),
	('5f72647d-53ab-4339-ac6d-0cdf357ea5b9', '0', 'requirements for sss?', 'Say that one more time?', 1, 'Dialogflow', '2025-09-25 00:28:12', '2025-09-25 00:28:12', 0, 'Bot'),
	('6442fdf4-9998-11f0-83e4-e86cc7778971', 'EMP001', 'hi', 'I didn\'t get that. Can you repeat?', 1, 'Dialogflow', '2025-09-25 06:47:04', '2025-09-25 06:47:04', 0, 'Bot'),
	('685c7920-9686-4098-9fac-7fc6dbc31bca', '0', 'can you appeal termination?', '"If performance is unsatisfactory, termination may occur after evaluation. Employees may appeal within 10 days to the Administrative Council, whose decision is final and non-appealable."', 0.73102, 'Dialogflow', '2025-09-25 00:31:05', '2025-09-25 00:31:05', 0, 'Bot'),
	('699122ab-ca35-41cb-b0c2-c47a5e45901b', '0', 'leave requirements?', '"Employees are entitled to vacation and sick leave, which can be monetized if unused. Emergency leave may be granted for serious accidents, natural calamities, or urgent personal matters. Faculty and staff may also request leave without pay for up to one year for health, studies, or other valid reasons."', 0.734444, 'Dialogflow', '2025-09-28 13:55:34', '2025-09-28 13:55:34', 0, 'Bot'),
	('81b0c711-a457-4b28-957d-414aed26e34c', '0', 'hi bot', '“Hello! 👋 I’m AIHRA, your HR assistant. How can I help you today?”', 0.798738, 'Dialogflow', '2025-09-24 23:40:59', '2025-09-24 23:40:59', 0, 'Bot'),
	('969585c9-18f6-446a-8519-eddc406eb3d6', '0', 'salary?', '"Employee salaries at DWCC comply with labor laws. Non-teaching staff follow the school’s salary scale, while teaching staff salaries depend on their teaching load and rank. Promotions and step increases affect salary adjustments."', 0.507328, 'Dialogflow', '2025-09-25 00:28:40', '2025-09-25 00:28:40', 1, 'HR'),
	('ae3df3fa-f99e-45b8-b8de-16cb2f43690d', '0', 'bye bye', '“See you next time. Don’t hesitate to ask if you have more HR-related questions!”', 1, 'Dialogflow', '2025-09-28 13:56:51', '2025-09-28 13:56:51', 0, 'Bot'),
	('b440935d-e410-4005-b3f0-118f018bd94b', '0', 'i love you', 'Thanks! The feeling is mutual.', 1, 'Dialogflow', '2025-09-28 13:56:07', '2025-09-28 13:56:07', 0, 'Bot'),
	('b6cb7e34-109d-413e-8365-293fd93df9da', '0', 'leave application', '"Employees are entitled to vacation and sick leave, which can be monetized if unused. Emergency leave may be granted for serious accidents, natural calamities, or urgent personal matters. Faculty and staff may also request leave without pay for up to one year for health, studies, or other valid reasons."', 0.734444, 'Dialogflow', '2025-09-25 00:34:50', '2025-09-25 00:34:50', 0, 'Bot'),
	('b8b5f0d8-ff36-4e4b-9a29-6bce380ebfe9', '0', 'overtime?', '"Overtime must be pre-approved using an Overtime Authorization Slip. Authorized overtime is compensated according to the employee’s salary rate."', 0.820354, 'Dialogflow', '2025-09-25 00:31:17', '2025-09-25 00:31:17', 0, 'Bot'),
	('d70a60b3-0bb6-4459-a593-51061c74f24b', '0', 'sanaol mahal', 'Sorry, can you say that again?', 1, 'Dialogflow', '2025-09-28 13:56:33', '2025-09-28 13:56:33', 0, 'Bot'),
	('e1a0462b-96a7-41a3-b962-266c5ea0fdd9', '0', 'beneits for leave?', '"Employees are entitled to vacation and sick leave, which can be monetized if unused. Emergency leave may be granted for serious accidents, natural calamities, or urgent personal matters. Faculty and staff may also request leave without pay for up to one year for health, studies, or other valid reasons."', 0.681796, 'Dialogflow', '2025-09-24 23:43:39', '2025-09-24 23:43:39', 0, 'Bot'),
	('eacbc778-c61f-4ba5-841c-a3ecead699ff', '0', 'bye', '“See you next time. Don’t hesitate to ask if you have more HR-related questions!”', 1, 'Dialogflow', '2025-09-24 23:41:59', '2025-09-24 23:41:59', 0, 'Bot'),
	('ee7aba13-6ba3-4d9c-899c-526df6174267', '0', 'thank you', 'Anytime. That\'s what I\'m here for.', 1, 'Dialogflow', '2025-09-28 13:55:59', '2025-09-28 13:55:59', 0, 'Bot'),
	('f05dc502-b516-4e5e-9f60-3f158089e5d5', '0', 'hello', '“Hi there! I can assist you with HR inquiries like employment, benefits, promotion, or development.”', 1, 'Dialogflow', '2025-09-28 14:03:49', '2025-09-28 14:03:49', 0, 'Bot');

-- Dumping structure for table aihra.reasoncatalog
CREATE TABLE IF NOT EXISTS `reasoncatalog` (
  `reasonID` varchar(50) NOT NULL,
  `description` enum('Incorrect response','Unclear response','Outdated info','Policy changed') DEFAULT NULL,
  PRIMARY KEY (`reasonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.reasoncatalog: ~0 rows (approximately)

-- Dumping structure for table aihra.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticketID` varchar(50) NOT NULL,
  `queryID` varchar(50) DEFAULT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `priority` varchar(10) DEFAULT NULL,
  `status` enum('Pending','Resolved','Expired') DEFAULT NULL,
  `initialResponseTime` datetime DEFAULT NULL,
  `resolutionTime` datetime DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `hrResponse` text,
  PRIMARY KEY (`ticketID`),
  KEY `queryID` (`queryID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`queryID`) REFERENCES `queries` (`queryID`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.tickets: ~0 rows (approximately)

-- Dumping structure for table aihra.users
CREATE TABLE IF NOT EXISTS `users` (
  `employeeNum` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `role` enum('Employee','HR','Admin') DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `age` datetime DEFAULT NULL,
  `profile_picture` blob,
  `profilePicture` blob,
  `status` enum('Active','Deactivated') DEFAULT NULL,
  PRIMARY KEY (`employeeNum`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table aihra.users: ~5 rows (approximately)
INSERT INTO `users` (`employeeNum`, `email`, `password`, `firstName`, `lastName`, `middleName`, `role`, `sex`, `age`, `profile_picture`, `profilePicture`, `status`) VALUES
	('0', 'system11@example.com', 'nopass', 'System', 'Bot', NULL, 'Employee', NULL, NULL, NULL, NULL, NULL),
	('999999', 'system1@example.com', 'nopass', 'System', 'Bot', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	('ADM001', 'admin@aihra.com', 'password123', 'System', 'Admin', NULL, 'Admin', 'Male', '1990-01-01 00:00:00', NULL, NULL, 'Active'),
	('EMP001', 'employee@aihra.com', 'password123', 'Juan', 'Dela Cruz', 'Santos', 'Employee', 'Male', '1995-03-15 00:00:00', NULL, NULL, 'Active'),
	('HR001', 'hr@aihra.com', 'password123', 'Maria', 'Reyes', 'Lopez', 'HR', 'Female', '1992-08-22 00:00:00', NULL, NULL, 'Active'),
	('SYSTEM', 'system@example.com', 'nopass', 'System', 'bot', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
