-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 13, 2017 at 11:36 PM
-- Server version: 10.0.31-MariaDB-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cedarphp_cedar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE IF NOT EXISTS `admin_messages` (
  `admin_id` int(8) NOT NULL AUTO_INCREMENT,
  `admin_type` int(1) NOT NULL,
  `admin_text` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `admin_to` int(8) NOT NULL,
  `admin_by` int(8) NOT NULL,
  `admin_post` int(8) NOT NULL,
  `is_reply` int(1) NOT NULL,
  `admin_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_read` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`admin_id`),
  KEY `admin_by` (`admin_by`),
  KEY `admin_by_2` (`admin_by`),
  KEY `admin_to` (`admin_to`),
  KEY `admin_post` (`admin_post`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_titles`
--

CREATE TABLE IF NOT EXISTS `favorite_titles` (
  `fav_id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL,
  `title_id` int(8) NOT NULL,
  PRIMARY KEY (`fav_id`),
  UNIQUE KEY `user_id` (`user_id`,`title_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=702 ;

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE IF NOT EXISTS `follows` (
  `follow_id` int(8) NOT NULL AUTO_INCREMENT,
  `follow_by` int(8) NOT NULL,
  `follow_to` int(8) NOT NULL,
  PRIMARY KEY (`follow_id`),
  UNIQUE KEY `follow_by` (`follow_by`,`follow_to`),
  KEY `follow_to` (`follow_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=8477 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifs`
--

CREATE TABLE IF NOT EXISTS `notifs` (
  `notif_id` int(8) NOT NULL AUTO_INCREMENT,
  `notif_type` int(1) NOT NULL,
  `notif_by` int(8) DEFAULT NULL,
  `notif_to` int(8) NOT NULL,
  `notif_post` int(8) DEFAULT NULL,
  `merged` int(8) DEFAULT NULL,
  `notif_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notif_read` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notif_id`),
  KEY `notif_by` (`notif_by`),
  KEY `notif_to` (`notif_to`),
  KEY `notif_post` (`notif_post`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=51007 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `post_by_id` int(8) NOT NULL,
  `post_title` int(8) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `text` varchar(800) COLLATE utf8mb4_bin NOT NULL,
  `post_image` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_by_id` (`post_by_id`),
  KEY `posts_ibfk_2` (`post_title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=99992399 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `user_id` int(8) NOT NULL,
  `bio` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `country` enum('1','2','3','4','5','6','7') COLLATE utf8mb4_bin DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `fav_post` int(8) DEFAULT NULL,
  `organization` varchar(256) COLLATE utf8mb4_bin DEFAULT NULL,
  `yeah_notifs` int(1) NOT NULL DEFAULT '1',
  `last_online` datetime DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE IF NOT EXISTS `replies` (
  `reply_id` int(8) NOT NULL AUTO_INCREMENT,
  `reply_post` int(8) NOT NULL,
  `reply_by_id` int(8) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `text` varchar(800) COLLATE utf8mb4_bin NOT NULL,
  `reply_image` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`reply_id`),
  KEY `reply_post` (`reply_post`),
  KEY `reply_by_id` (`reply_by_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=99996487 ;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE IF NOT EXISTS `titles` (
  `title_id` int(8) NOT NULL,
  `title_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_desc` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `perm` int(1) DEFAULT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(8) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `user_pass` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `nickname` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `user_face` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `date_created` datetime NOT NULL,
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `user_level` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1199 ;

-- --------------------------------------------------------

--
-- Table structure for table `yeahs`
--

CREATE TABLE IF NOT EXISTS `yeahs` (
  `yeah_id` int(8) NOT NULL AUTO_INCREMENT,
  `yeah_post` int(8) NOT NULL,
  `type` enum('post','reply') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'post',
  `date_time` datetime NOT NULL,
  `yeah_by` int(8) NOT NULL,
  PRIMARY KEY (`yeah_id`),
  UNIQUE KEY `yeah_post` (`yeah_post`,`type`,`yeah_by`),
  KEY `yeah_by` (`yeah_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32577 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD CONSTRAINT `admin_messages_ibfk_1` FOREIGN KEY (`admin_to`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_messages_ibfk_2` FOREIGN KEY (`admin_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorite_titles`
--
ALTER TABLE `favorite_titles`
  ADD CONSTRAINT `favorite_titles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorite_titles_ibfk_2` FOREIGN KEY (`title_id`) REFERENCES `titles` (`title_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follow_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`follow_to`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifs`
--
ALTER TABLE `notifs`
  ADD CONSTRAINT `notifs_ibfk_1` FOREIGN KEY (`notif_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifs_ibfk_2` FOREIGN KEY (`notif_to`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`post_by_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`post_title`) REFERENCES `titles` (`title_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`reply_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`reply_by_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `yeahs`
--
ALTER TABLE `yeahs`
  ADD CONSTRAINT `yeahs_ibfk_1` FOREIGN KEY (`yeah_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
