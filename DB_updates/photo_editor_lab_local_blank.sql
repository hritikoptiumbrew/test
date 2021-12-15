-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2019 at 04:10 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `photo_editor_lab_local_blank`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertise_category_master`
--

CREATE TABLE `advertise_category_master` (
  `id` int(11) NOT NULL,
  `advertise_category` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `advertise_category_master`
--

INSERT INTO `advertise_category_master` (`id`, `advertise_category`, `is_active`, `create_time`, `update_time`, `attribute1`, `attribute2`, `attribute3`) VALUES
(1, 'Banner', 1, '2018-07-16 09:06:47', '2018-07-16 09:06:47', NULL, NULL, NULL),
(2, 'Intertial', 1, '2018-07-16 09:06:47', '2018-07-16 09:06:47', NULL, NULL, NULL),
(3, 'Rewarded Video', 1, '2018-07-16 09:07:07', '2018-07-16 09:07:07', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `advertise_links`
--

CREATE TABLE `advertise_links` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `app_logo_img` text,
  `url` text NOT NULL,
  `platform` varchar(10) NOT NULL COMMENT 'Android,iOS',
  `app_description` text,
  `is_active` tinyint(1) DEFAULT '1',
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
  `is_free` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=free,1=paid',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
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

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `is_active`, `created_at`, `updated_at`, `attribute1`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 'Frame', 1, '2017-08-06 06:53:14', '2017-08-06 13:22:49', NULL, NULL, NULL, NULL),
(2, 'Sticker', 1, '2017-08-06 06:53:14', '2017-08-06 13:23:21', NULL, NULL, NULL, NULL),
(3, 'Background', 1, '2017-08-06 06:53:14', '2019-01-12 08:35:37', NULL, NULL, NULL, NULL),
(4, 'Fonts', 1, '2019-01-18 05:20:58', '2019-01-18 05:20:58', NULL, NULL, NULL, NULL),
(5, 'Shapes', 1, '2019-02-21 03:23:06', '2019-02-21 03:23:06', NULL, NULL, NULL, NULL);

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
  `device_vendor_name` text CHARACTER SET utf8mb4 COMMENT 'like: LG',
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
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs_detail`
--

CREATE TABLE `failed_jobs_detail` (
  `id` int(11) NOT NULL,
  `user_id` varchar(254) DEFAULT NULL,
  `failed_job_id` int(11) NOT NULL,
  `api_name` text,
  `api_description` text,
  `job_name` varchar(254) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `font_master`
--

CREATE TABLE `font_master` (
  `id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `font_name` text COMMENT 'Unique name of font file',
  `font_file` text,
  `ios_font_name` text,
  `android_font_name` text,
  `is_active` tinyint(1) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `image` text,
  `original_img` text,
  `display_img` text,
  `is_active` tinyint(1) DEFAULT '1',
  `image_type` int(10) DEFAULT NULL COMMENT '1=Background , 2=Frame',
  `json_data` text,
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT NULL,
  `is_portrait` tinyint(1) DEFAULT NULL,
  `search_category` text,
  `height` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `original_img_height` int(10) DEFAULT '0',
  `original_img_width` int(10) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text COMMENT 'Used to store webp image',
  `attribute2` text COMMENT 'Used to manage sequence after set sample image as webp',
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
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8 NOT NULL,
  `payload` longtext CHARACTER SET utf8 NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 'admin_permission', NULL, NULL, '2017-02-11 05:18:47', '2017-02-11 05:18:47'),
(2, 'user_permission', NULL, NULL, '2017-02-11 05:19:01', '2017-02-11 05:19:01');

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
-- Table structure for table `promocode_master`
--

CREATE TABLE `promocode_master` (
  `id` int(11) NOT NULL,
  `promo_code` text CHARACTER SET utf8mb4 NOT NULL,
  `package_name` text CHARACTER SET utf8mb4 NOT NULL,
  `device_udid` text CHARACTER SET utf8mb4 NOT NULL,
  `device_platform` tinyint(1) DEFAULT '0' COMMENT '1=android, 2=ios',
  `status` tinyint(1) DEFAULT '0' COMMENT '0=new, 1=reading',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `question_master`
--

CREATE TABLE `question_master` (
  `id` int(11) NOT NULL,
  `question_type` int(11) DEFAULT NULL,
  `question` text,
  `answer` text,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question_master`
--

INSERT INTO `question_master` (`id`, `question_type`, `question`, `answer`, `is_active`, `create_time`, `update_time`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 1, 'Research the organization', '<p style="margin-bottom: 1.5em; color: rgb(102, 102, 102); font-family: &quot;PT Sans&quot;, Verdana, Arial, Helvetica, sans-serif;">This will help you answer questions — and stand out from less-prepared candidates.</p><ul style="margin-right: 0px; margin-bottom: 1.375em; margin-left: 0px; padding: 0px 0px 0px 30px; color: rgb(102, 102, 102); font-family: &quot;PT Sans&quot;, Verdana, Arial, Helvetica, sans-serif;"><li><strong>Seek background information.</strong>&nbsp;<ul style="margin-right: 0px; margin-left: 0px; padding: 0px 0px 0px 30px;"><li>Use tools like Vault, CareerSearch or The Riley Guide for an overview of the organization and its industry profile.</li><li>Visit the organization’s website to ensure that you understand the breadth of what they do.</li><li>Review the organization\'s background and mission statement.</li><li>Assess their products, services and client-base.</li><li>Read recent press releases for insight on projected growth and stability.</li></ul></li><li><strong>Get perspective.</strong>&nbsp;Review trade or business publications. Seek perspective and a glimpse into their industry&nbsp;standing.</li><li><strong>Develop a question list.</strong>&nbsp;Prepare to ask about the organization or position based on your research.</li></ul>', 1, '2018-12-26 04:23:49', '2018-12-26 10:36:30', NULL, NULL, NULL),
(2, 1, 'Research the organization', '<p class="MsoNormal" style="margin-bottom: 18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">This will help you answer questions —\nand stand out from less-prepared candidates.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:Symbol;\nmso-fareast-font-family:Symbol;mso-bidi-font-family:Symbol;color:#666666;\nmso-fareast-language:EN-IN">·<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><b><span style="font-size:10.5pt;font-family:\n&quot;Verdana&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:\n&quot;Times New Roman&quot;;color:#666666;mso-fareast-language:EN-IN">Seek background\ninformation.</span></b><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">&nbsp;<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:&quot;Courier New&quot;;\nmso-fareast-font-family:&quot;Courier New&quot;;color:#666666;mso-fareast-language:EN-IN">o<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">Use tools like Vault, CareerSearch or\nThe Riley Guide for an overview of the organization and its industry profile.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:&quot;Courier New&quot;;\nmso-fareast-font-family:&quot;Courier New&quot;;color:#666666;mso-fareast-language:EN-IN">o<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">Visit the organization’s website to\nensure that you understand the breadth of what they do.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:&quot;Courier New&quot;;\nmso-fareast-font-family:&quot;Courier New&quot;;color:#666666;mso-fareast-language:EN-IN">o<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">Review the organization\'s background\nand mission statement.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:&quot;Courier New&quot;;\nmso-fareast-font-family:&quot;Courier New&quot;;color:#666666;mso-fareast-language:EN-IN">o<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">Assess their products, services and\nclient-base.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:&quot;Courier New&quot;;\nmso-fareast-font-family:&quot;Courier New&quot;;color:#666666;mso-fareast-language:EN-IN">o<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">Read recent press releases for\ninsight on projected growth and stability.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:Symbol;\nmso-fareast-font-family:Symbol;mso-bidi-font-family:Symbol;color:#666666;\nmso-fareast-language:EN-IN">·<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><b><span style="font-size:10.5pt;font-family:\n&quot;Verdana&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:\n&quot;Times New Roman&quot;;color:#666666;mso-fareast-language:EN-IN">Get perspective.</span></b><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;mso-fareast-font-family:\n&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;color:#666666;\nmso-fareast-language:EN-IN">&nbsp;Review trade or business publications. Seek perspective\nand a glimpse into their industry&nbsp;standing.<o:p></o:p></span></p><p class="MsoNormal" style="margin-left: 72pt; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;">\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n</p><p class="MsoNormal" style="margin-left: 0cm; text-indent: -18pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><!--[if !supportLists]--><span style="font-size:10.0pt;mso-bidi-font-size:10.5pt;font-family:Symbol;\nmso-fareast-font-family:Symbol;mso-bidi-font-family:Symbol;color:#666666;\nmso-fareast-language:EN-IN">·<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n</span></span><!--[endif]--><b><span style="font-size:10.5pt;font-family:\n&quot;Verdana&quot;,sans-serif;mso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:\n&quot;Times New Roman&quot;;color:#666666;mso-fareast-language:EN-IN">Develop a question\nlist.</span></b><span style="font-size:10.5pt;font-family:&quot;Verdana&quot;,sans-serif;\nmso-fareast-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;\ncolor:#666666;mso-fareast-language:EN-IN">&nbsp;Prepare to ask about the\norganization or position based on your research.<o:p></o:p></span></p>', 1, '2018-12-26 04:25:03', '2018-12-26 04:25:03', NULL, NULL, NULL),
(3, 2, 'Research the organization', '<p>test1</p>', 1, '2018-12-26 04:25:34', '2018-12-26 04:30:49', NULL, NULL, NULL),
(5, 1, 'test', '<p style="margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><font color="#333333" face="Georgia, serif"><span style="font-size: 17.3333px;">test</span></font></p>', 1, '2018-12-26 04:58:34', '2018-12-26 04:58:34', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `question_type_master`
--

CREATE TABLE `question_type_master` (
  `id` int(11) NOT NULL,
  `question_type` text,
  `image` text,
  `is_active` tinyint(1) DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question_type_master`
--

INSERT INTO `question_type_master` (`id`, `question_type`, `image`, `is_active`, `create_time`, `update_time`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 'Interview Prep Plan', NULL, 1, '2018-11-28 10:38:41', '2019-03-05 04:07:11', NULL, NULL, NULL),
(2, 'Most Common', NULL, 1, '2018-11-28 10:38:47', '2018-11-28 10:38:47', NULL, NULL, NULL),
(3, 'Behavioural', NULL, 1, '2018-11-28 10:38:51', '2018-11-28 10:38:51', NULL, NULL, NULL),
(4, 'Resume writing', NULL, 1, '2018-11-28 10:38:59', '2018-11-28 10:39:00', NULL, NULL, NULL),
(5, 'Technical questions', NULL, 1, '2018-11-28 10:39:04', '2018-11-28 10:39:04', NULL, NULL, NULL),
(6, 'Personality', NULL, 1, '2018-11-28 10:39:07', '2019-03-05 04:07:14', NULL, NULL, NULL),
(7, 'Group Discussion Questions/Topics', NULL, 1, '2018-11-28 10:39:12', '2019-03-05 04:07:17', NULL, NULL, NULL);

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
(1, 'admin', 'admin', NULL, '2017-02-11 05:17:10', '2017-02-11 05:17:10'),
(2, 'user', 'user', NULL, '2017-02-11 05:17:25', '2017-02-11 05:17:25');

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
  `name` varchar(255) DEFAULT NULL,
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
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category_advertise_server_id_master`
--

CREATE TABLE `sub_category_advertise_server_id_master` (
  `id` int(11) NOT NULL,
  `advertise_category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `server_id` text,
  `device_platform` tinyint(1) DEFAULT '1' COMMENT '1=Ios, 2=Android',
  `is_active` tinyint(1) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category_catalog`
--

CREATE TABLE `sub_category_catalog` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `catalog_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tag_master`
--

CREATE TABLE `tag_master` (
  `id` int(11) NOT NULL,
  `tag_name` text CHARACTER SET utf8,
  `is_active` tinyint(1) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tag_master`
--

INSERT INTO `tag_master` (`id`, `tag_name`, `is_active`, `create_time`, `update_time`, `attribute1`, `attribute2`, `attribute3`) VALUES
(1, 'Food & Drink', 1, '2018-10-06 06:00:51', '2018-10-06 08:20:45', NULL, NULL, NULL),
(2, 'Offer & Sales', 1, '2018-10-06 06:01:01', '2018-10-06 06:01:01', NULL, NULL, NULL),
(3, 'Mobile Apps', 1, '2018-10-06 06:01:10', '2018-10-06 09:50:00', NULL, NULL, NULL),
(4, 'Photography', 1, '2018-10-06 06:01:18', '2018-10-06 06:01:18', NULL, NULL, NULL);

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

--
-- Triggers `user_detail`
--
DELIMITER $$
CREATE TRIGGER `user_detail_BEFORE_INSERT` BEFORE INSERT ON `user_detail` FOR EACH ROW BEGIN
SET NEW.create_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `user_detail_BEFORE_UPDATE` BEFORE UPDATE ON `user_detail` FOR EACH ROW BEGIN
SET NEW.update_time = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_feeds_master`
--

CREATE TABLE `user_feeds_master` (
  `id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `json_id` int(11) DEFAULT NULL,
  `image` text,
  `is_active` tinyint(1) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `id` int(10) UNSIGNED NOT NULL,
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
(1, 'admin', 'admin@gmail.com', '$2y$10$b.uidRM5FHO4dyCYLHhvmOj1boWBQ78i.FwfqJpHd.5mH6lgoUble', NULL, NULL, 0, 1, '2017-08-02 12:08:30', '2017-08-02 12:21:03', NULL, NULL, NULL, NULL),
(2, 'guest', 'guest@gmail.com', '$2y$10$12lfLNBhV39QMXyK0RHMsuoz/YG7QpkITB1SdyNVC0ZMFHtJvhEiO', NULL, NULL, 0, 1, '2017-08-02 12:10:08', '2017-08-02 12:10:33', NULL, NULL, NULL, NULL);

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

-- --------------------------------------------------------

CREATE TABLE `failed_jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `failed_jobs_detail` (
  `id` int(11) NOT NULL,
  `user_id` varchar(254) DEFAULT NULL,
  `failed_job_id` int(11) NOT NULL,
  `api_name` text,
  `api_description` text,
  `job_name` varchar(254) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attribute1` text,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8 NOT NULL,
  `payload` longtext CHARACTER SET utf8 NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_video_master`
--

CREATE TABLE `youtube_video_master` (
  `id` int(11) NOT NULL,
  `youtube_video_id` text,
  `title` text,
  `channel_name` text,
  `url` text,
  `thumbnail_url` text,
  `thumbnail_width` int(11) DEFAULT NULL,
  `thumbnail_height` int(11) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attribute2` text,
  `attribute3` text,
  `attribute4` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `youtube_video_master`
--

INSERT INTO `youtube_video_master` (`id`, `youtube_video_id`, `title`, `channel_name`, `url`, `thumbnail_url`, `thumbnail_width`, `thumbnail_height`, `published_at`, `create_time`, `update_time`, `attribute2`, `attribute3`, `attribute4`) VALUES
(1, 'yBtMwyQFXwA', 'How to Interview for a Job in American English, part 1/5', 'Rachel\'s English', 'https://www.youtube.com/watch?v=yBtMwyQFXwA', 'https://i.ytimg.com/vi/yBtMwyQFXwA/hqdefault.jpg', 480, 360, '2016-01-05 20:00:01', '2018-12-26 03:49:33', '2018-12-26 04:05:37', NULL, NULL, NULL),
(2, 'OVAMb6Kui6A', 'Job Interview   Good Example copy', 'Katherine Johnson', 'https://www.youtube.com/watch?v=OVAMb6Kui6A', 'https://i.ytimg.com/vi/OVAMb6Kui6A/hqdefault.jpg', 480, 360, '2016-11-17 17:31:08', '2018-12-26 03:52:14', '2018-12-26 03:52:14', NULL, NULL, NULL),
(3, 'nhTcuUvLGOE', 'English Job Interview Tips and Tricks - How to Answer Job Interview Questions in English', 'Oxford Online English', 'https://www.youtube.com/watch?v=nhTcuUvLGOE', 'https://i.ytimg.com/vi/nhTcuUvLGOE/hqdefault.jpg', 480, 360, '2017-10-26 06:03:30', '2018-12-26 03:52:24', '2018-12-26 03:52:24', NULL, NULL, NULL),
(4, '6aO6cGTcnUg', 'How to succeed in your JOB INTERVIEW: Behavioral Questions', 'Learn English with Emma [engVid]', 'https://www.youtube.com/watch?v=6aO6cGTcnUg', 'https://i.ytimg.com/vi/6aO6cGTcnUg/hqdefault.jpg', 480, 360, '2018-02-27 06:00:08', '2018-12-26 03:52:31', '2018-12-26 03:52:32', NULL, NULL, NULL),
(5, 'ncuCMZRG1wo', 'English Conversation: Job Interview', 'EnglishStreams', 'https://www.youtube.com/watch?v=ncuCMZRG1wo', 'https://i.ytimg.com/vi/ncuCMZRG1wo/hqdefault.jpg', 480, 360, '2017-01-18 07:57:36', '2018-12-26 03:52:38', '2018-12-26 03:52:38', NULL, NULL, NULL),
(6, 'd1xb0_tT5SQ', '"What are your weaknesses?" Job Interview Question / 9 Great Answers!', 'Learn English with Jared Hendricks', 'https://www.youtube.com/watch?v=d1xb0_tT5SQ', 'https://i.ytimg.com/vi/d1xb0_tT5SQ/hqdefault.jpg', 480, 360, '2016-11-18 00:08:45', '2018-12-26 03:52:46', '2018-12-26 03:52:47', NULL, NULL, NULL),
(7, 'BkL98JHAO_w', 'Mock Job Interview Questions and Tips for a Successful Interview', 'Virginia Western Community College', 'https://www.youtube.com/watch?v=BkL98JHAO_w', 'https://i.ytimg.com/vi/BkL98JHAO_w/hqdefault.jpg', 480, 360, '2009-09-25 20:36:08', '2018-12-26 03:52:53', '2018-12-26 03:52:54', NULL, NULL, NULL),
(8, 'kayOhGRcNt4', 'Tell Me About Yourself - A Good Answer to This Interview Question', 'Linda Raynier', 'https://www.youtube.com/watch?v=kayOhGRcNt4', 'https://i.ytimg.com/vi/kayOhGRcNt4/hqdefault.jpg', 480, 360, '2016-12-14 15:12:37', '2018-12-26 03:53:01', '2018-12-26 03:53:01', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

ALTER TABLE `failed_jobs`
ADD PRIMARY KEY (`id`),
ADD KEY `id` (`id`);


ALTER TABLE `failed_jobs_detail`
ADD PRIMARY KEY (`id`);


ALTER TABLE `jobs`
ADD PRIMARY KEY (`id`),
ADD KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved_at`);


ALTER TABLE `failed_jobs`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `failed_jobs_detail`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `jobs`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Indexes for table `advertise_category_master`
--
ALTER TABLE `advertise_category_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `advertise_links`
--
ALTER TABLE `advertise_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catalog_master`
--
ALTER TABLE `catalog_master`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `failed_jobs_detail`
--
ALTER TABLE `failed_jobs_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `font_master`
--
ALTER TABLE `font_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catalog_id` (`catalog_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_from_product_to_category_idx` (`catalog_id`);
ALTER TABLE `images` ADD FULLTEXT KEY `search_category` (`search_category`);

--
-- Indexes for table `image_details`
--
ALTER TABLE `image_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved_at`);

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
-- Indexes for table `promocode_master`
--
ALTER TABLE `promocode_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_master`
--
ALTER TABLE `question_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_type` (`question_type`);
ALTER TABLE `question_master` ADD FULLTEXT KEY `question` (`question`);

--
-- Indexes for table `question_type_master`
--
ALTER TABLE `question_type_master`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `from_role_user_to_roles_idx` (`role_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `sub_category_advertise_server_id_master`
--
ALTER TABLE `sub_category_advertise_server_id_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `advertise_category_id` (`advertise_category_id`);

--
-- Indexes for table `sub_category_catalog`
--
ALTER TABLE `sub_category_catalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `catalog_id` (`catalog_id`);

--
-- Indexes for table `tag_master`
--
ALTER TABLE `tag_master`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `user_feeds_master`
--
ALTER TABLE `user_feeds_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `json_id` (`json_id`);

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
-- Indexes for table `youtube_video_master`
--
ALTER TABLE `youtube_video_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertise_category_master`
--
ALTER TABLE `advertise_category_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `advertise_links`
--
ALTER TABLE `advertise_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `catalog_master`
--
ALTER TABLE `catalog_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `device_master`
--
ALTER TABLE `device_master`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `failed_jobs_detail`
--
ALTER TABLE `failed_jobs_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `font_master`
--
ALTER TABLE `font_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `image_details`
--
ALTER TABLE `image_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `order_logger`
--
ALTER TABLE `order_logger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `order_master`
--
ALTER TABLE `order_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `promocode_master`
--
ALTER TABLE `promocode_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `question_master`
--
ALTER TABLE `question_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `question_type_master`
--
ALTER TABLE `question_type_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `restore_device`
--
ALTER TABLE `restore_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sent_notification_detail`
--
ALTER TABLE `sent_notification_detail`
  MODIFY `ntf_detail_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sent_notification_logs`
--
ALTER TABLE `sent_notification_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sent_notification_master`
--
ALTER TABLE `sent_notification_master`
  MODIFY `ntf_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_category_advertise_links`
--
ALTER TABLE `sub_category_advertise_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_category_advertise_server_id_master`
--
ALTER TABLE `sub_category_advertise_server_id_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sub_category_catalog`
--
ALTER TABLE `sub_category_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tag_master`
--
ALTER TABLE `tag_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_detail`
--
ALTER TABLE `user_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_feeds_master`
--
ALTER TABLE `user_feeds_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_session`
--
ALTER TABLE `user_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `youtube_video_master`
--
ALTER TABLE `youtube_video_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `device_master`
--
ALTER TABLE `device_master`
  ADD CONSTRAINT `device_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `font_master`
--
ALTER TABLE `font_master`
  ADD CONSTRAINT `font_master_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `catalog_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `catalog_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_master`
--
ALTER TABLE `order_master`
  ADD CONSTRAINT `order_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `from_permission_role_to_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `from_permission_role_to_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `question_master`
--
ALTER TABLE `question_master`
  ADD CONSTRAINT `question_master_ibfk_1` FOREIGN KEY (`question_type`) REFERENCES `question_type_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restore_device`
--
ALTER TABLE `restore_device`
  ADD CONSTRAINT `restore_device_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `user_id_to_blistek_user_master_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sent_notification_master`
--
ALTER TABLE `sent_notification_master`
  ADD CONSTRAINT `sent_notification_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `sub_category_advertise_links`
--
ALTER TABLE `sub_category_advertise_links`
  ADD CONSTRAINT `sub_category_advertise_links_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_category_advertise_links_ibfk_2` FOREIGN KEY (`advertise_link_id`) REFERENCES `advertise_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_category_advertise_server_id_master`
--
ALTER TABLE `sub_category_advertise_server_id_master`
  ADD CONSTRAINT `sub_category_advertise_server_id_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_category_advertise_server_id_master_ibfk_2` FOREIGN KEY (`advertise_category_id`) REFERENCES `advertise_category_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_category_catalog`
--
ALTER TABLE `sub_category_catalog`
  ADD CONSTRAINT `sub_category_catalog_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sub_category_catalog_ibfk_2` FOREIGN KEY (`catalog_id`) REFERENCES `catalog_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD CONSTRAINT `from_user_detail_to_user_master` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_feeds_master`
--
ALTER TABLE `user_feeds_master`
  ADD CONSTRAINT `user_feeds_master_ibfk_1` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
