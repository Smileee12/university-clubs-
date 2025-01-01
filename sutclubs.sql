-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: sutclubs
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `club_members`
--

DROP TABLE IF EXISTS `club_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `club_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `club_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `club_members`
--

LOCK TABLES `club_members` WRITE;
/*!40000 ALTER TABLE `club_members` DISABLE KEYS */;
INSERT INTO `club_members` VALUES (1,'Coding Club','John Doe','S1001','john.doe@example.com','123-456-7890','Member','2024-12-28 19:24:33','Pendding'),(2,'Robotics Club','Jane Smith','S1002','jane.smith@example.com','123-456-7891','admin','2024-12-28 19:24:33','approved'),(3,'Hackathon Club','Alice Johnson','S1003','alice.johnson@example.com','123-456-7892','Member','2024-12-28 19:24:33','approved'),(4,'Tech Enthusiasts Club','Bob Brown','S1004','bob.brown@example.com','123-456-7893','Member','2024-12-28 19:24:33','Pending'),(7,'Hackathon Club','samira','3','student@gmail.com','120323','pr-logistics','2024-12-28 20:24:44','Pending');
/*!40000 ALTER TABLE `club_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1234567129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
INSERT INTO `clubs` VALUES (2,'Robotics Club','A club dedicated to robotics, automation, and AI development.','webProject2 - Copy.webp','2024-12-28 19:24:33'),(3,'Hackathon Club','A club for organizing and participating in coding competitions and hackathons.','clubs.webp','2024-12-28 19:24:33'),(4,'Tech Enthusiasts Club','A club for students passionate about all things tech-related.','webProject1.webp','2024-12-28 19:24:33');
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_registrations`
--

DROP TABLE IF EXISTS `event_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_registrations`
--

LOCK TABLES `event_registrations` WRITE;
/*!40000 ALTER TABLE `event_registrations` DISABLE KEYS */;
INSERT INTO `event_registrations` VALUES (4,2,12,'2024-12-28 22:24:18','samira','student@gmail.com'),(5,2,1,'2024-12-28 22:47:24','John Doe','admin@gmail.com');
/*!40000 ALTER TABLE `event_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `date_time` datetime DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `max_registrations` int DEFAULT '0',
  `current_registrations` int DEFAULT '0',
  `club_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_club_events` (`club_id`),
  CONSTRAINT `fk_club_events` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (2,'Robotics Workshop','Learn the basics of robotics and automation.','2024-12-28 21:24:00','uploads/event1.jpg',100,32,2),(3,'Hackathon 2024','Join the annual hackathon for building innovative tech solutions.','2024-12-28 21:24:00','uploads/event3.jpg',100,50,3),(4,'Tech Talk Series','Guest talks and panel discussions on emerging technology trends.','2024-12-28 21:24:00','uploads/event2.jpg',100,40,4);
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_type` enum('student','admin') NOT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`full_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John Doe','admin@gmail.com','$2y$10$DH2wYRa0i20BdyQdt.HOjuZ4j66v6m8C7wHtG8u3ztauRQD.kKY5m','2024-12-28 20:43:32','admin','john_doe'),(2,'Jane Smith','student@example.com','$2y$10$mrZ6.knPA.DuThLa8EykcuWBWEpSvLJ5k4kJKAcr9c/16liZt72Dy','2024-12-28 20:43:32','student','jane_smith'),(3,'Alice Johnson','alice.johnson@example.com','$2y$10$m1nlplYZPQewvFvKhN/3BekmAXvpfc6poUgK9B2Sxo6vYj4UdAZai','2024-12-28 20:43:32','student','alice_johnson'),(4,'Bob Brown','bob.brown@example.com','$2y$10$QdkjEiPVIPfffQjsaE6nFuRcaL54scRE0QdZ2qnn6bTpgoU/D2iWa','2024-12-28 20:43:32','student','bob_brown'),(10,' Samira','admin1@gmail.com','$2y$10$LQ5nvSpfEV6uClE.u2pxwuFPLL4rRydBr1YifQZiG6KbW5PB3TzuW','2024-12-28 20:43:32','admin','john_doe'),(12,'samira','student@gmail.com','$2y$10$JAwxytJ3cWQZHmRglgiMRezzCRbFcsPuy6OH86SfnDNV4xJ2Bam2q','2024-12-28 20:43:32','student','samiraaa');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-28 22:51:05
