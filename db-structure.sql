-- phpMyAdmin SQL Dump
-- version 4.7.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2018 at 09:05 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cedar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `admin_id` int(8) NOT NULL,
  `admin_type` int(1) NOT NULL,
  `admin_text` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `admin_to` int(8) NOT NULL,
  `admin_by` int(8) NOT NULL,
  `admin_post` int(8) NOT NULL,
  `is_reply` int(1) NOT NULL,
  `admin_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_read` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cloudinary_keys`
--

CREATE TABLE `cloudinary_keys` (
  `key_id` int(8) NOT NULL,
  `api_key` bigint(32) NOT NULL,
  `preset` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `site_name` varchar(64) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_titles`
--

CREATE TABLE `favorite_titles` (
  `fav_id` int(8) NOT NULL,
  `user_id` int(8) NOT NULL,
  `title_id` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follow_id` int(8) NOT NULL,
  `follow_by` int(8) NOT NULL,
  `follow_to` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nahs`
--

CREATE TABLE `nahs` (
  `nah_id` int(8) NOT NULL,
  `nah_post` int(8) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nah_by` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `notifs`
--

CREATE TABLE `notifs` (
  `notif_id` int(8) NOT NULL,
  `notif_type` int(1) NOT NULL,
  `notif_by` int(8) DEFAULT NULL,
  `notif_to` int(8) NOT NULL,
  `notif_post` int(8) DEFAULT NULL,
  `merged` int(8) DEFAULT NULL,
  `notif_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notif_read` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(8) NOT NULL,
  `post_by_id` int(8) NOT NULL,
  `post_title` int(8) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `text` varchar(800) COLLATE utf8mb4_bin NOT NULL,
  `post_image` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `user_id` int(8) NOT NULL,
  `bio` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `name_color` varchar(7) COLLATE utf8mb4_bin DEFAULT NULL,
  `country` enum('1','2','3','4','5','6','7') COLLATE utf8mb4_bin DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `fav_post` int(8) DEFAULT NULL,
  `organization` varchar(256) COLLATE utf8mb4_bin DEFAULT NULL,
  `yeah_notifs` int(1) NOT NULL DEFAULT '1',
  `last_online` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `reply_id` int(8) NOT NULL,
  `reply_post` int(8) NOT NULL,
  `reply_by_id` int(8) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `feeling_id` int(1) NOT NULL DEFAULT '0',
  `text` varchar(800) COLLATE utf8mb4_bin NOT NULL,
  `reply_image` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE `titles` (
  `title_id` int(8) NOT NULL,
  `title_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_desc` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title_banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `perm` int(1) DEFAULT NULL,
  `type` int(1) NOT NULL,
  `user_made` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(8) NOT NULL,
  `user_name` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `user_pass` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `nickname` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `user_face` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `date_created` datetime NOT NULL,
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `user_level` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `yeahs`
--

CREATE TABLE `yeahs` (
  `yeah_id` int(8) NOT NULL,
  `yeah_post` int(8) NOT NULL,
  `type` enum('post','reply') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'post',
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `yeah_by` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_by` (`admin_by`),
  ADD KEY `admin_by_2` (`admin_by`),
  ADD KEY `admin_to` (`admin_to`),
  ADD KEY `admin_post` (`admin_post`);

--
-- Indexes for table `cloudinary_keys`
--
ALTER TABLE `cloudinary_keys`
  ADD PRIMARY KEY (`key_id`);

--
-- Indexes for table `favorite_titles`
--
ALTER TABLE `favorite_titles`
  ADD PRIMARY KEY (`fav_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`title_id`),
  ADD KEY `title_id` (`title_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `follow_by` (`follow_by`,`follow_to`),
  ADD KEY `follow_to` (`follow_to`);

--
-- Indexes for table `nahs`
--
ALTER TABLE `nahs`
  ADD PRIMARY KEY (`nah_id`),
  ADD KEY `nah_by` (`nah_by`);

--
-- Indexes for table `notifs`
--
ALTER TABLE `notifs`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `notif_by` (`notif_by`),
  ADD KEY `notif_to` (`notif_to`),
  ADD KEY `notif_post` (`notif_post`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_by_id` (`post_by_id`),
  ADD KEY `posts_ibfk_2` (`post_title`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `reply_post` (`reply_post`),
  ADD KEY `reply_by_id` (`reply_by_id`);

--
-- Indexes for table `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`title_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `yeahs`
--
ALTER TABLE `yeahs`
  ADD PRIMARY KEY (`yeah_id`),
  ADD UNIQUE KEY `yeah_post` (`yeah_post`,`type`,`yeah_by`),
  ADD KEY `yeah_by` (`yeah_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `admin_id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloudinary_keys`
--
ALTER TABLE `cloudinary_keys`
  MODIFY `key_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `favorite_titles`
--
ALTER TABLE `favorite_titles`
  MODIFY `fav_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `follow_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nahs`
--
ALTER TABLE `nahs`
  MODIFY `nah_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifs`
--
ALTER TABLE `notifs`
  MODIFY `notif_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94061871;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `reply_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72923352;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2746;

--
-- AUTO_INCREMENT for table `yeahs`
--
ALTER TABLE `yeahs`
  MODIFY `yeah_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

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
-- Constraints for table `nahs`
--
ALTER TABLE `nahs`
  ADD CONSTRAINT `nahs_ibfk_1` FOREIGN KEY (`nah_by`) REFERENCES `users` (`user_id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
