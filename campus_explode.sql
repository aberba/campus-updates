-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2014 at 09:32 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `campus_explode`
--
CREATE DATABASE IF NOT EXISTS `campus_explode` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `campus_explode`;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET latin1 NOT NULL,
  `text` text CHARACTER SET latin1 NOT NULL,
  `full_name` varchar(60) CHARACTER SET latin1 NOT NULL,
  `display` set('Show','Hide') CHARACTER SET latin1 NOT NULL DEFAULT 'Hide',
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `text`, `full_name`, `display`, `date_posted`) VALUES
(1, 'Upgrading Campus Explode', 'The administration of campus explode wish to inform all users on the upcoming site upgrade.', 'Erica Jane', 'Hide', '2014-05-22 15:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `campus_list`
--

CREATE TABLE IF NOT EXISTS `campus_list` (
  `campus_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(555) CHARACTER SET latin1 NOT NULL,
  `campus_abbr` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(555) CHARACTER SET latin1 NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`campus_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `campus_list`
--

INSERT INTO `campus_list` (`campus_id`, `campus_name`, `campus_abbr`, `location`, `date_posted`) VALUES
(1, 'University of Ghana (Legon)', 'LEGON', 'Accra', '2014-02-07 09:20:20'),
(2, 'Kwame Nkrumah University of Science and Technology ', 'KNUST', 'Kumasi', '2014-02-07 09:40:38'),
(3, 'University of Energy and Natural Resources', 'UENR', 'Sunyani', '2014-02-07 10:50:27'),
(4, 'Catholic University College', 'CUC', 'Sunyani', '2014-02-09 21:09:02'),
(5, 'University of Development Studies', 'U.D.S.', 'Navrongo', '2014-05-10 10:31:00'),
(6, 'University of Development Studies', 'U.D.S', 'Wa', '2014-05-10 10:31:20'),
(7, 'University of Development Studies', 'U.D.S.', 'Tamale', '2014-05-10 10:31:41'),
(8, 'Sunyani Polytechinc', 'S-Poly', 'Sunyani', '2014-05-22 13:36:12');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update_id` int(10) unsigned NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_by` int(6) unsigned NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `update_id`, `comment`, `comment_by`, `date_posted`) VALUES
(2, 10, 'cool-stuff', 7, '2014-05-22 15:19:48');

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE IF NOT EXISTS `comment_replies` (
  `reply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) unsigned NOT NULL,
  `update_id` int(10) unsigned NOT NULL,
  `reply` text COLLATE utf8_unicode_ci NOT NULL,
  `reply_by` int(6) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `comment_replies`
--

INSERT INTO `comment_replies` (`reply_id`, `comment_id`, `update_id`, `reply`, `reply_by`, `date_posted`) VALUES
(1, 2, 10, 'hahaha', 7, '2014-05-22 15:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL,
  `action` varchar(555) CHARACTER SET latin1 NOT NULL,
  `ip_address` varchar(15) CHARACTER SET latin1 NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `action`, `ip_address`, `action_date`) VALUES
(12, 7, 'Last time of log deleting', '127.0.0.1', '2014-05-22 13:21:02'),
(13, 7, 'Added a new campus with name <em>Sunyani Polytechinc</em> located at <em>Sunyani</em>', '127.0.0.1', '2014-05-22 13:36:13'),
(14, 7, 'Unconfirmed an update with an ID of <em>10</em>', '169.254.246.222', '2014-05-22 15:29:39'),
(15, 7, 'Confirmed an update with an ID of <em>10</em>', '169.254.246.222', '2014-05-22 15:30:12');

-- --------------------------------------------------------

--
-- Table structure for table `private_messages`
--

CREATE TABLE IF NOT EXISTS `private_messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL,
  `subject` varchar(555) CHARACTER SET latin1 NOT NULL,
  `message` text CHARACTER SET latin1 NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `private_messages`
--

INSERT INTO `private_messages` (`message_id`, `user_id`, `subject`, `message`, `date_sent`) VALUES
(1, 7, 'what up admin', 'cool bakey what up with u', '2014-05-10 11:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `update_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update_title` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  `update_text` text COLLATE utf8_unicode_ci NOT NULL,
  `update_photo` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  `update_by` varchar(555) CHARACTER SET latin1 NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirm` set('1','0') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `stop_comments` set('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  PRIMARY KEY (`update_id`),
  FULLTEXT KEY `update_title` (`update_title`),
  FULLTEXT KEY `update_text` (`update_text`),
  FULLTEXT KEY `update_photo` (`update_photo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`update_id`, `update_title`, `update_text`, `update_photo`, `update_by`, `date_posted`, `confirm`, `stop_comments`) VALUES
(10, 'Campus Explode Official Lunching', '<p>When you <a href="hello.com">have an</a> idea on what you want your newsletter template to look like, you can start working with Photoshop. Otherwise, you can download a template from many free email template providers. There are no specific width and height of an email template, but to make sure it is compatible and readable on any email without breaks, we suggest you set the width size at not more than 650px. There are however no limitations on heights, so that parameter is up to you. If you have an idea on what you want your newsletter template to look like, you can start working with Photoshop.</p>\r\n<p> Otherwise, you can download a template from many free email template providers. There are no specific width and height of an email template, but to make sure it is compatible and readable on any email without breaks, we suggest you set the width size at not more than 650px. There are however no limitations on heights, so that parameter is up to you. If you have an idea on what you want your newsletter template to look like, you can start working with Photoshop. Otherwise, you can download a template from many free email template providers.</p>\r\n<p> There are no specific width and height of an email template, but to make sure it is compatible and readable on any email without breaks, we suggest you set the width size at not more than 650px. There are however no limitations on heights, so that parameter is up to you.</p>', 'campus-explode-lunching-image.png', 'Jonathan Kellis', '2014-05-22 12:36:21', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(6) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` set('M','F') COLLATE utf8_unicode_ci NOT NULL,
  `campus` varchar(555) CHARACTER SET latin1 NOT NULL,
  `profile_photo` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) CHARACTER SET latin1 NOT NULL,
  `dibre` set('8','5','0','') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activation` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='site users table' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `password`, `gender`, `campus`, `profile_photo`, `email`, `dibre`, `date_registered`, `activation`) VALUES
(7, 'Jane', '25faccc9e2e6c8df210b9ad672f36d11b86d652b', 'F', 'University of Ghana (Legon)', 'campus_explode_logo.png', 'campusupdates4u@gmail.com', '5', '2014-04-21 11:33:31', NULL),
(8, 'Zeus', '25faccc9e2e6c8df210b9ad672f36d11b86d652b', 'M', 'University of Ghana (Legon)', 'manage-your-computer-anywhere.jpg', 'admin@campusupdates.bytehost8.com', '8', '2014-04-21 11:37:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_uploads`
--

CREATE TABLE IF NOT EXISTS `users_uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(40) CHARACTER SET latin1 NOT NULL,
  `file_name` text CHARACTER SET latin1 NOT NULL,
  `date_uploaded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table contains updates sent by users for posting on the' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
