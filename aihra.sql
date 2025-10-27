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

-- Data exporting was unselected.

-- Dumping structure for table aihra.assigned_items
CREATE TABLE IF NOT EXISTS `assigned_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `date_assigned` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.category
CREATE TABLE IF NOT EXISTS `category` (
  `categoryID` varchar(50) NOT NULL,
  `categoryName` enum('Employment','Benefits','Promotion','Employee Development') DEFAULT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.chathistory
CREATE TABLE IF NOT EXISTS `chathistory` (
  `historyID` varchar(50) NOT NULL,
  `employeeNum` varchar(50) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`historyID`),
  KEY `employeeNum` (`employeeNum`),
  CONSTRAINT `chathistory_ibfk_1` FOREIGN KEY (`employeeNum`) REFERENCES `users` (`employeeNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.chat_messages
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(20) DEFAULT NULL,
  `sender` enum('employee','hr') DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

-- Data exporting was unselected.

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
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`queryID`) REFERENCES `queries` (`queryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table aihra.hr_inbox
CREATE TABLE IF NOT EXISTS `hr_inbox` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(50) NOT NULL,
  `from_user` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category` varchar(50) DEFAULT NULL,
  `intent` varchar(50) DEFAULT NULL,
  `confidence` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_no` (`ticket_no`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.hr_replies
CREATE TABLE IF NOT EXISTS `hr_replies` (
  `replyID` char(36) NOT NULL,
  `ticket_no` varchar(50) NOT NULL,
  `hr_message` text NOT NULL,
  `replied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `replied_by` varchar(50) DEFAULT 'HR',
  PRIMARY KEY (`replyID`),
  KEY `ticket_no` (`ticket_no`),
  CONSTRAINT `hr_replies_ibfk_1` FOREIGN KEY (`ticket_no`) REFERENCES `hr_inbox` (`ticket_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table aihra.knowledge_base
CREATE TABLE IF NOT EXISTS `knowledge_base` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question` varchar(255) DEFAULT NULL,
  `answer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table aihra.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
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
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table aihra.reasoncatalog
CREATE TABLE IF NOT EXISTS `reasoncatalog` (
  `reasonID` varchar(50) NOT NULL,
  `description` enum('Incorrect response','Unclear response','Outdated info','Policy changed') DEFAULT NULL,
  PRIMARY KEY (`reasonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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
  `about` text,
  `status` enum('Active','Deactivated') DEFAULT NULL,
  PRIMARY KEY (`employeeNum`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
