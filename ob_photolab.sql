-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2017 at 04:17 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ob_photolab`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertise_links`
--

CREATE TABLE `advertise_links` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `url` text NOT NULL,
  `platform` varchar(10) NOT NULL COMMENT 'Android,iOS',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_master`
--

CREATE TABLE `catalog_master` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` text,
  `is_free` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=free,1=paid',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text,
  `attribute5` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `device_master`
--

CREATE TABLE `device_master` (
  `device_id` int(11) NOT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `user_id` varchar(11) DEFAULT NULL,
  `device_reg_id` text NOT NULL,
  `device_platform` enum('android','ios','chrome','other') NOT NULL,
  `device_model_name` text COMMENT 'like: nexus 4, nexus 5',
  `device_vendor_name` text COMMENT 'like: LG',
  `device_os_version` text COMMENT 'like: for Android: 4.4,5.0',
  `device_udid` text NOT NULL COMMENT 'udid for device',
  `device_resolution` text COMMENT 'like: width*height',
  `device_carrier` text COMMENT 'like: vodafone',
  `device_country_code` varchar(10) DEFAULT NULL COMMENT 'like: +1 for us',
  `device_language` varchar(50) DEFAULT NULL COMMENT 'like: en for english',
  `device_local_code` varchar(10) DEFAULT NULL COMMENT 'like: 411001',
  `device_default_time_zone` varchar(25) DEFAULT NULL COMMENT 'like: GMT+09:30',
  `device_library_version` varchar(10) DEFAULT NULL COMMENT 'like: 1 (it is ob lib version)',
  `device_application_version` varchar(10) DEFAULT NULL COMMENT 'Device app version',
  `device_type` varchar(25) DEFAULT NULL COMMENT 'like: phone, tablet',
  `device_registration_date` varchar(30) DEFAULT NULL COMMENT 'time of device when it registred',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1',
  `is_count` int(11) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `image` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `image_details`
--

CREATE TABLE `image_details` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `directory_name` varchar(200) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'png,jpg,jpeg and gif',
  `size` float NOT NULL COMMENT 'bytes',
  `height` int(5) NOT NULL,
  `width` int(5) NOT NULL,
  `pixel` int(5) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order_logger`
--

CREATE TABLE `order_logger` (
  `id` int(11) NOT NULL,
  `request_data` longtext,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `order_master`
--

CREATE TABLE `order_master` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `order_number` varchar(254) DEFAULT NULL,
  `user_id` varchar(254) DEFAULT NULL,
  `tot_order_amount` double(10,2) DEFAULT NULL,
  `currency_code` varchar(45) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `purchase_time` varchar(50) NOT NULL,
  `auto_renewing` varchar(10) DEFAULT NULL,
  `device_platform` varchar(25) NOT NULL,
  `order_status` tinyint(1) DEFAULT '0',
  `neft_transaction_id` varchar(254) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin_permission', NULL, NULL, '2017-08-02 10:37:53', '2017-08-02 10:37:53'),
(2, 'user_permission', NULL, NULL, '2017-08-02 10:38:12', '2017-08-02 10:38:12');

--
-- Triggers `permissions`
--
DELIMITER $$
CREATE TRIGGER `permissions_BEFORE_INSERT` BEFORE INSERT ON `permissions` FOR EACH ROW BEGIN
SET NEW.created_at = CURRENT_TIMESTAMP;
SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `restore_device`
--

CREATE TABLE `restore_device` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `order_number` varchar(250) NOT NULL,
  `device_udid` text NOT NULL,
  `restore` int(11) NOT NULL DEFAULT '1',
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL, '2017-08-02 10:40:52', '2017-08-02 10:40:52'),
(2, 'user', NULL, NULL, '2017-08-02 10:41:07', '2017-08-02 10:41:07');

--
-- Triggers `roles`
--
DELIMITER $$
CREATE TRIGGER `roles_BEFORE_INSERT` BEFORE INSERT ON `roles` FOR EACH ROW BEGIN
SET NEW.created_at = CURRENT_TIMESTAMP;
SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sent_notification_detail`
--

CREATE TABLE `sent_notification_detail` (
  `ntf_detail_id` int(11) NOT NULL,
  `ntf_id` int(11) NOT NULL,
  `device_platform` text NOT NULL,
  `ntf_sent` int(11) NOT NULL,
  `ntf_success` int(11) NOT NULL,
  `ntf_failure` int(11) NOT NULL,
  `ntf_canonical` int(11) NOT NULL,
  `ntf_received` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `sent_notification_detail`
--
DELIMITER $$
CREATE TRIGGER `sent_notification_detail_BEFORE_INSERT` BEFORE INSERT ON `sent_notification_detail` FOR EACH ROW BEGIN
SET NEW.create_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sent_notification_detail_BEFORE_UPDATE` BEFORE UPDATE ON `sent_notification_detail` FOR EACH ROW BEGIN
SET NEW.update_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sent_notification_logs`
--

CREATE TABLE `sent_notification_logs` (
  `log_id` int(11) NOT NULL,
  `ntf_id` int(11) NOT NULL,
  `request_header` longtext NOT NULL,
  `request_body` longtext NOT NULL,
  `response_header` longtext NOT NULL,
  `response_body` longtext NOT NULL,
  `response_header_code` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `sent_notification_logs`
--
DELIMITER $$
CREATE TRIGGER `sent_notification_logs_BEFORE_INSERT` BEFORE INSERT ON `sent_notification_logs` FOR EACH ROW BEGIN
SET NEW.create_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sent_notification_master`
--

CREATE TABLE `sent_notification_master` (
  `ntf_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `ntf_title` text NOT NULL,
  `ntf_message` text NOT NULL,
  `url` text,
  `ntf_icon_path` text NOT NULL,
  `ntf_type` enum('alert','update','url') NOT NULL COMMENT 'like: update, alert',
  `ntf_filter` text NOT NULL COMMENT 'notification filter detail like sent only to  OS:4.1',
  `ntf_total_device` int(11) NOT NULL COMMENT 'count of total device selected for notification.',
  `ntf_status` int(11) NOT NULL COMMENT 'response code from server like 200,401',
  `was_scheduled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: was not scheduled  1: was scheduled',
  `is_active` tinyint(1) DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE `sub_category` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `is_catalog` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category_advertise_links`
--

CREATE TABLE `sub_category_advertise_links` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `advertise_link_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category_catalog`
--

CREATE TABLE `sub_category_catalog` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_detail`
--

CREATE TABLE `user_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `email_id` varchar(254) DEFAULT NULL,
  `phone_number_1` varchar(15) DEFAULT NULL,
  `profile_img` text,
  `about_me` text,
  `phone_number` varchar(15) DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(6) DEFAULT NULL,
  `contry` varchar(255) DEFAULT NULL,
  `latitude` double(12,8) DEFAULT NULL,
  `longitude` double(12,8) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_detail`
--

INSERT INTO `user_detail` (`id`, `user_id`, `first_name`, `last_name`, `email_id`, `phone_number_1`, `profile_img`, `about_me`, `phone_number`, `address_line_1`, `city`, `state`, `zip_code`, `contry`, `latitude`, `longitude`, `create_time`, `update_time`, `attribute1`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 1, 'Admin', 'Admin', 'admin@gmail.com', '9876549877', '', 'i\'m Admin.', '6549874569', 'Surat', 'surat', 'gujarat', '395010', 'India', NULL, NULL, '2017-07-04 06:05:36', '2017-07-26 05:12:46', NULL, NULL, NULL, NULL),
(2, 2, 'guest', 'user', 'guest@gmail.com', '9898989898', '595c7e38976de_profile_img_1499233848.jpg', 'i\'m user.', '6549874569', 'Rander Road', 'surat', 'gujarat', '395010', 'India', NULL, NULL, '2017-07-05 11:51:22', '2017-07-06 07:48:43', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `social_uid` varchar(255) DEFAULT NULL,
  `signup_type` int(11) DEFAULT NULL COMMENT '1 = email,\n2 = facebook,\n3 = twitter',
  `profile_setup` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`id`, `user_name`, `email_id`, `password`, `social_uid`, `signup_type`, `profile_setup`, `is_active`, `create_time`, `update_time`, `attribute1`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$5iivhMskiA3tDehoG7Y/9.H6FJU7XksN2YLrflFhdgi1gr84/nJQq', NULL, NULL, 0, 1, '2017-08-02 10:45:46', '2017-07-22 12:01:55', NULL, NULL, NULL, NULL),
(2, 'user', 'guest@gmail.com', '$2y$10$12lfLNBhV39QMXyK0RHMsuoz/YG7QpkITB1SdyNVC0ZMFHtJvhEiO', NULL, NULL, 0, 1, '2017-08-02 10:46:14', '2017-07-06 07:49:04', NULL, NULL, NULL, NULL);

--
-- Triggers `user_master`
--
DELIMITER $$
CREATE TRIGGER `user_master_BEFORE_INSERT` BEFORE INSERT ON `user_master` FOR EACH ROW BEGIN
SET NEW.create_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `user_master_BEFORE_UPDATE` BEFORE UPDATE ON `user_master` FOR EACH ROW BEGIN
SET NEW.update_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

CREATE TABLE `user_session` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `token` varchar(1000) DEFAULT NULL,
  `device_udid` text,
  `device_id` int(11) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertise_links`
--
ALTER TABLE `advertise_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catalog_master`
--
ALTER TABLE `catalog_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `device_master`
--
ALTER TABLE `device_master`
  ADD PRIMARY KEY (`device_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sub_category_id` (`sub_category_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_from_product_to_category_idx` (`catalog_id`);

--
-- Indexes for table `image_details`
--
ALTER TABLE `image_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_logger`
--
ALTER TABLE `order_logger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `from_permission_role_to_permission_idx` (`permission_id`),
  ADD KEY `from_permission_role_to_roles_idx` (`role_id`);

--
-- Indexes for table `restore_device`
--
ALTER TABLE `restore_device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `from_role_user_to_user_master_idx` (`user_id`),
  ADD KEY `from_role_user_to_roles_idx` (`role_id`);

--
-- Indexes for table `sent_notification_detail`
--
ALTER TABLE `sent_notification_detail`
  ADD PRIMARY KEY (`ntf_detail_id`),
  ADD KEY `nft_detail_to_ntf_master_idx` (`ntf_id`);

--
-- Indexes for table `sent_notification_logs`
--
ALTER TABLE `sent_notification_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `from_snl_to_master_idx` (`ntf_id`);

--
-- Indexes for table `sent_notification_master`
--
ALTER TABLE `sent_notification_master`
  ADD PRIMARY KEY (`ntf_id`),
  ADD KEY `sub_category_id` (`sub_category_id`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Indexes for table `sub_category_advertise_links`
--
ALTER TABLE `sub_category_advertise_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_advertise_link_id` (`advertise_link_id`),
  ADD KEY `fk_sub_category_id` (`sub_category_id`);

--
-- Indexes for table `sub_category_catalog`
--
ALTER TABLE `sub_category_catalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `catalog_id` (`catalog_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_detail_to_user_master_idx` (`user_id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_UNIQUE` (`user_name`),
  ADD UNIQUE KEY `email_id_UNIQUE` (`email_id`);

--
-- Indexes for table `user_session`
--
ALTER TABLE `user_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_session_to_user_master_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sub_category_advertise_links`
--
ALTER TABLE `sub_category_advertise_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_category_catalog`
--
ALTER TABLE `sub_category_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `device_master`
--
ALTER TABLE `device_master`
  ADD CONSTRAINT `device_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`);

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `catalog_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_master`
--
ALTER TABLE `order_master`
  ADD CONSTRAINT `order_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `from_permission_role_to_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `from_permission_role_to_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `restore_device`
--
ALTER TABLE `restore_device`
  ADD CONSTRAINT `restore_device_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`);

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `from_role_user_to_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `from_role_user_to_user_master` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `sent_notification_master`
--
ALTER TABLE `sent_notification_master`
  ADD CONSTRAINT `sent_notification_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`);

--
-- Constraints for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD CONSTRAINT `sub_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `sub_category_advertise_links`
--
ALTER TABLE `sub_category_advertise_links`
  ADD CONSTRAINT `fk_advertise_link_id` FOREIGN KEY (`advertise_link_id`) REFERENCES `advertise_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub_category_id` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD CONSTRAINT `from_user_detail_to_user_master` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
