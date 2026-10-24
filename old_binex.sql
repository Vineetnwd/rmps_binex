-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 10:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `binex`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_head`
--

CREATE TABLE `account_head` (
  `id` int(11) NOT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_head`
--

INSERT INTO `account_head` (`id`, `account_type`, `account_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'ACTIVITY', 'singh', 'ACTIVE', '2024-11-29 07:05:25', 1, '2024-11-29 07:05:37', 1, 1),
(2, NULL, NULL, 'AUTO', '2024-11-29 07:05:38', 1, '2024-11-29 07:05:38', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_txn`
--

CREATE TABLE `account_txn` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `txn_date` date NOT NULL,
  `txn_type` varchar(25) DEFAULT NULL,
  `txn_by` varchar(255) DEFAULT NULL,
  `txn_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `txn_mode` varchar(255) NOT NULL DEFAULT 'CASH',
  `txn_remarks` varchar(255) DEFAULT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cancel_at` timestamp NULL DEFAULT current_timestamp(),
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_remarks` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Contain Account expense Details';

-- --------------------------------------------------------

--
-- Table structure for table `admin_txn`
--

CREATE TABLE `admin_txn` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `txn_date` date NOT NULL,
  `txn_type` varchar(25) DEFAULT NULL,
  `txn_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `txn_mode` varchar(255) NOT NULL DEFAULT 'CASH',
  `txn_remarks` varchar(255) DEFAULT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cancel_at` timestamp NULL DEFAULT current_timestamp(),
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_remarks` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Contain Account expense Details';

-- --------------------------------------------------------

--
-- Table structure for table `admit_card`
--

CREATE TABLE `admit_card` (
  `id` int(11) NOT NULL,
  `student_class` varchar(25) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `sitting_name` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `exam_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `marks_nb` int(11) DEFAULT 5,
  `marks_se` int(11) DEFAULT 5,
  `marks_written` int(11) DEFAULT 80,
  `marks_viva` int(11) DEFAULT 10,
  `marks_total` int(11) DEFAULT 100,
  `marks_passing` int(11) DEFAULT 30,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admit_card`
--

INSERT INTO `admit_card` (`id`, `student_class`, `exam_date`, `subject_id`, `sitting_name`, `start_time`, `end_time`, `status`, `exam_id`, `created_by`, `created_at`, `updated_at`, `updated_by`, `marks_nb`, `marks_se`, `marks_written`, `marks_viva`, `marks_total`, `marks_passing`, `branch_id`) VALUES
(1, 'NUR', '2024-12-11', 1, '1st', '10:00:00', '11:00:00', 'ACTIVE', 1, 1, '2024-11-29 06:51:32', '2024-11-29 06:53:33', NULL, 5, 5, 50, 10, 50, 16, 1),
(2, 'NUR', '2024-12-12', 2, '2nd', '12:00:00', '13:00:00', 'ACTIVE', 1, 1, '2024-11-29 06:51:32', '2024-11-29 06:53:33', NULL, 5, 5, 50, 10, 50, 16, 1),
(3, 'NUR', NULL, 1, NULL, NULL, NULL, 'ACTIVE', 5, 2, '2024-11-29 08:25:39', '2024-11-29 08:25:39', NULL, 5, 5, 100, 10, 100, 30, 2),
(4, 'NUR', NULL, 2, NULL, NULL, NULL, 'ACTIVE', 5, 2, '2024-11-29 08:25:39', '2024-11-29 08:25:40', NULL, 5, 5, 100, 10, 100, 30, 2),
(5, 'LKG', NULL, 1, NULL, NULL, NULL, 'ACTIVE', 5, 2, '2024-11-29 08:26:47', '2024-11-29 08:26:47', NULL, 5, 5, 100, 10, 100, 30, 2),
(6, 'LKG', NULL, 2, NULL, NULL, NULL, 'ACTIVE', 5, 2, '2024-11-29 08:26:47', '2024-11-29 08:26:47', NULL, 5, 5, 100, 10, 100, 30, 2);

-- --------------------------------------------------------

--
-- Table structure for table `allow_user`
--

CREATE TABLE `allow_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_name` varchar(255) NOT NULL,
  `can_view` char(5) NOT NULL DEFAULT 'YES',
  `can_add` char(5) NOT NULL DEFAULT 'NO',
  `can_edit` char(5) NOT NULL DEFAULT 'NO',
  `can_delete` char(5) NOT NULL DEFAULT 'NO',
  `in_menu` char(5) NOT NULL DEFAULT 'NO',
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `book_cat`
--

CREATE TABLE `book_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_cat`
--

INSERT INTO `book_cat` (`id`, `cat_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'books', 'ACTIVE', '2024-11-29 06:59:04', 1, '2024-11-29 06:59:16', 1, 1),
(2, 'academic', 'ACTIVE', '2024-11-29 06:59:18', 1, '2024-11-29 06:59:26', 1, 1),
(3, NULL, 'AUTO', '2024-11-29 06:59:28', 1, '2024-11-29 06:59:28', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `book_list`
--

CREATE TABLE `book_list` (
  `id` int(11) NOT NULL,
  `accession_no` int(20) NOT NULL,
  `book_name` varchar(255) DEFAULT NULL,
  `isbn_no` varchar(255) NOT NULL,
  `edition` varchar(25) DEFAULT NULL,
  `publish_year` varchar(25) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `publisher_name` varchar(255) DEFAULT NULL,
  `pub_id` int(11) DEFAULT NULL,
  `book_no` varchar(255) DEFAULT NULL,
  `book_price` varchar(25) DEFAULT NULL,
  `category` varchar(25) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `book_list`
--

INSERT INTO `book_list` (`id`, `accession_no`, `book_name`, `isbn_no`, `edition`, `publish_year`, `author_name`, `publisher_name`, `pub_id`, `book_no`, `book_price`, `category`, `cat_id`, `remarks`, `updated_at`, `updated_by`, `created_by`, `created_at`, `status`, `branch_id`) VALUES
(1, 205, 'science', '120', '7', '2024', 'singh', NULL, 1, '05', '580', NULL, 2, NULL, '2024-11-29 09:03:14', 1, 1, '2024-11-29 09:03:14', 'AVAILABLE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `book_pub`
--

CREATE TABLE `book_pub` (
  `id` int(11) NOT NULL,
  `pub_name` varchar(255) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_pub`
--

INSERT INTO `book_pub` (`id`, `pub_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'me', 'ACTIVE', '2024-11-29 07:00:06', 1, '2024-11-29 07:00:14', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `book_txn`
--

CREATE TABLE `book_txn` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `valid_till` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `book_fee` float(12,2) DEFAULT NULL,
  `book_fine` float(12,2) DEFAULT NULL,
  `fine_date` date DEFAULT NULL,
  `issue_by` int(11) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_txn`
--

INSERT INTO `book_txn` (`id`, `student_id`, `book_id`, `issue_date`, `valid_till`, `return_date`, `book_fee`, `book_fine`, `fine_date`, `issue_by`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`, `remarks`) VALUES
(1, 3, 1, '2024-11-29', '2024-12-14', '2024-11-29', NULL, 100.00, '2024-11-29', 1, 'RETURN', '2024-11-29 09:01:35', 1, '2024-11-29 09:03:14', 1, 1, 'book Damage');

-- --------------------------------------------------------

--
-- Table structure for table `branch_list`
--

CREATE TABLE `branch_list` (
  `id` int(11) NOT NULL,
  `full_name` varchar(128) DEFAULT NULL,
  `inst_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `school_code` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `aff_no` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT 1,
  `inst_address1` varchar(128) DEFAULT NULL,
  `inst_address2` varchar(128) DEFAULT NULL,
  `inst_contact` varchar(10) DEFAULT NULL,
  `inst_email` varchar(128) DEFAULT NULL,
  `inst_logo` varchar(128) DEFAULT NULL,
  `inst_url` varchar(128) DEFAULT NULL,
  `sender_id` varchar(6) DEFAULT NULL,
  `adm_suffix` varchar(10) DEFAULT NULL,
  `class_list` varchar(255) DEFAULT 'NUR,LKG,UKG',
  `section_list` varchar(128) DEFAULT 'A,B,C'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_list`
--

INSERT INTO `branch_list` (`id`, `full_name`, `inst_name`, `school_code`, `aff_no`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`, `inst_address1`, `inst_address2`, `inst_contact`, `inst_email`, `inst_logo`, `inst_url`, `sender_id`, `adm_suffix`, `class_list`, `section_list`) VALUES
(1, 'Branch 1', 'Branch 1', 'BR1', 'Numquam tenetur qui ', 'ACTIVE', '2024-11-28 08:58:41', 2, 2, '2024-11-29 05:50:41', NULL, 'Branch 1', 'Iste neque commodo e', '9431426601', 'branch1@offerplant.com', NULL, 'https://www.mogomabuky.ca', NULL, NULL, 'NUR,LKG,UKG', 'A,B'),
(2, 'Branch 2', 'Branch 2', 'BR2', 'Obcaecati qui rerum ', 'ACTIVE', '2024-11-28 09:03:35', 2, 2, '2024-11-29 05:50:45', 3, 'Branch 2', 'Duis ad dolor volupt', '9431426602', 'branch2@offerplant.com', NULL, 'https://www.fuxy.info', NULL, NULL, 'NUR,LKG,UKG', 'A,B');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `student_class` varchar(25) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'student',
  `msg_type` varchar(25) NOT NULL DEFAULT 'text',
  `reply_to` int(11) NOT NULL,
  `message` varchar(4000) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `closing_report`
--

CREATE TABLE `closing_report` (
  `id` int(11) NOT NULL,
  `closing_date` date DEFAULT NULL,
  `opening_balance` float(12,2) DEFAULT NULL,
  `inc_cash` float(12,2) DEFAULT 0.00,
  `inc_bank` float(12,2) DEFAULT 0.00,
  `exp_bank` float(12,2) DEFAULT 0.00,
  `exp_cash` float(12,2) NOT NULL DEFAULT 0.00,
  `closing_balance` float(12,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(11) DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distribute_item`
--

CREATE TABLE `distribute_item` (
  `id` int(11) NOT NULL,
  `inv_no` varchar(255) DEFAULT NULL,
  `inv_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `payment_mode` varchar(55) DEFAULT NULL,
  `status` varchar(55) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distribute_item`
--

INSERT INTO `distribute_item` (`id`, `inv_no`, `inv_id`, `student_id`, `item_id`, `date`, `amount`, `rate`, `qty`, `payment_mode`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, '1220', 1, 3, 1, '2024-11-29', 1600.00, 800.00, 2, NULL, 'CANCELLED', '2024-11-29 06:40:52', 1, '2024-11-29 08:11:57', 1, 1),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 06:46:56', 1, '2024-11-29 06:46:56', NULL, 0),
(4, '1123', 3, 3, 1, '2024-11-29', 11720.00, 293.00, 40, NULL, 'ACTIVE', '2024-11-29 08:06:14', 1, '2024-11-29 08:12:39', 1, 1),
(5, '1', 4, 5, 2, '2024-11-29', 3400.00, 100.00, 34, NULL, 'CANCELLED', '2024-11-29 08:06:48', 2, '2024-11-29 08:09:30', 2, 2),
(6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:07:38', 2, '2024-11-29 08:07:38', NULL, 2),
(7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:12:41', 1, '2024-11-29 08:12:41', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `id` int(10) NOT NULL,
  `code` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `name` varchar(31) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `state_code` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`id`, `code`, `name`, `state_code`, `created_at`, `status`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'ANNIC', 'Nicobar', 'AN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(2, 'ANNOR', 'North and Middle Andaman', 'AN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(3, 'ANSOU', 'South Andaman', 'AN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(4, 'APANA', 'Anantapur', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(5, 'APCHI', 'Chittoor', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(6, 'APEAS', 'East Godavari', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(7, 'APGUN', 'Guntur', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(8, 'APKAD', 'YSR District  Kadapa Cuddapah', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(9, 'APKRI', 'Krishna', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(10, 'APKUR', 'Kurnool', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(11, 'APNEL', 'Sri Potti Sriramulu Nellore', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(12, 'APPRA', 'Prakasam', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(13, 'APSRI', 'Srikakulam', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(14, 'APVIS', 'Visakhapatnam', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(15, 'APVIZ', 'Vizianagaram', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(16, 'APWES', 'West Godavari', 'AP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(17, 'ARANJ', 'Anjaw', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(18, 'ARCHA', 'Changlang', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(19, 'ARDIB', 'Dibang Valley', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(20, 'AREAK', 'East Kameng', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(21, 'AREAS', 'East Siang', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(22, 'ARKUR', 'Kurung Kumey', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(23, 'ARLDV', 'Lower Dibang Valley', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(24, 'ARLOH', 'Lohit', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(25, 'ARLON', 'Longding', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(26, 'ARLOS', 'Lower Subansiri', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(27, 'ARPAP', 'Papum Pare', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(28, 'ARTAW', 'Tawang', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(29, 'ARTIR', 'Tirap', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(30, 'ARUPS', 'Upper Siang', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(31, 'ARUSU', 'Upper Subansiri', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(32, 'ARWEK', 'West Kameng', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(33, 'ARWES', 'West Siang', 'AR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(34, 'ASBAK', 'Baksa', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(35, 'ASBAR', 'Barpeta', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(36, 'ASBON', 'Bongaigaon', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(37, 'ASCAC', 'Cachar', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(38, 'ASCHI', 'Chirang', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(39, 'ASDAR', 'Darrang', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(40, 'ASDHE', 'Dhemaji', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(41, 'ASDHU', 'Dhubri', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(42, 'ASDIB', 'Dibrugarh', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(43, 'ASGOA', 'Goalpara', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(44, 'ASGOL', 'Golaghat', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(45, 'ASHAI', 'Hailakandi', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(46, 'ASJOR', 'Jorhat', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(47, 'ASKAA', 'Karbi Anglong', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(48, 'ASKAM', 'Kamrup', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(49, 'ASKAN', 'Kamrup Metropolitan', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(50, 'ASKAR', 'Karimganj', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(51, 'ASKOK', 'Kokrajhar', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(52, 'ASLAK', 'Lakhimpur', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(53, 'ASMOR', 'Morigaon', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(54, 'ASNAG', 'Nagaon', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(55, 'ASNAL', 'Nalbari', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(56, 'ASNOR', 'Dima Hasao (North Cachar Hills)', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(57, 'ASSIV', 'Sivasagar', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(58, 'ASSON', 'Sonitpur', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(59, 'ASTIN', 'Tinsukia', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(60, 'ASUDA', 'Udalguri', 'AS', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(61, 'BRARA', 'Araria', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(62, 'BRARW', 'Arwal', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(63, 'BRAUR', 'Aurangabad', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(64, 'BRBAN', 'Banka', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(65, 'BRBEG', 'Begusarai', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(66, 'BRBHA', 'Bhagalpur', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(67, 'BRBHO', 'Bhojpur', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(68, 'BRBUX', 'Buxar', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(69, 'BRDAR', 'Darbhanga', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(70, 'BREAC', 'East Champaran', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(71, 'BRGAY', 'Gaya', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(72, 'BRGOP', 'Gopalganj', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(73, 'BRJAM', 'Jamui', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(74, 'BRJEH', 'Jehanabad', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(75, 'BRKAI', 'Kaimur (Bhabua)', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(76, 'BRKAT', 'Katihar', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(77, 'BRKHA', 'Khagaria', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(78, 'BRKIS', 'Kishanganj', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(79, 'BRLAK', 'Lakhisarai', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(80, 'BRMAB', 'Madhubani', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(81, 'BRMAP', 'Madhepura', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(82, 'BRMUN', 'Munger', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(83, 'BRMUZ', 'Muzaffarpur', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(84, 'BRNAL', 'Nalanda', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(85, 'BRNAW', 'Nawada', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(86, 'BRPAT', 'Patna', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(87, 'BRPUR', 'Purnea', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(88, 'BRROH', 'Rohtas', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(89, 'BRSAH', 'Saharsa', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(90, 'BRSAM', 'Samastipur', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(91, 'BRSAR', 'Saran', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(92, 'BRSHE', 'Sheohar', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(93, 'BRSHP', 'Sheikhpura', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(94, 'BRSIT', 'Sitamarhi', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(95, 'BRSIW', 'Siwan', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(96, 'BRSUP', 'Supaul', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(97, 'BRVAI', 'Vaishali', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(98, 'BRWES', 'West Champaran', 'BR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(99, 'CHCHA', 'Chandigarh', 'CH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(100, 'CTBAB', 'Baloda Bazar', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(101, 'CTBAL', 'Balod', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(102, 'CTBAP', 'Balrampur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(103, 'CTBAS', 'Bastar', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(104, 'CTBEM', 'Bemetara', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(105, 'CTBIJ', 'Bijapur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(106, 'CTBIL', 'Bilaspur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(107, 'CTDHA', 'Dhamtari', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(108, 'CTDUR', 'Durg', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(109, 'CTGAR', 'Gariaband', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(110, 'CTJAN', 'Janjgir-Champa', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(111, 'CTJAS', 'Jashpur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(112, 'CTKAB', 'Kabirdham-Kawardha', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(113, 'CTKOB', 'Korba', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(114, 'CTKOE', 'Korea', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(115, 'CTKON', 'Kondagaon', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(116, 'CTMAH', 'Mahasamund', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(117, 'CTMUN', 'Mungeli', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(118, 'CTNAR', 'Narayanpur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(119, 'CTNOR', 'North Bastar-Kanker', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(120, 'CTRAG', 'Raigarh', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(121, 'CTRAI', 'Raipur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(122, 'CTRAJ', 'Rajnandgaon', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(123, 'CTSOU', 'Dantewada (South Bastar)', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(124, 'CTSUG', 'Surguja', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(125, 'CTSUJ', 'Surajpur', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(126, 'CTSUK', 'Sukma', 'CT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(127, 'DNDNH', 'Dadra & Nagar Haveli', 'DN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(128, 'DDDAM', 'Daman', 'DD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(129, 'DDDIU', 'Diu', 'DD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(130, 'DLCEN', 'Central Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(131, 'DLEAS', 'East Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(132, 'DLNED', 'North East Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(133, 'DLNEW', 'New Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(134, 'DLNRD', 'North Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(135, 'DLNWD', 'North West Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(136, 'DLSED', 'South East Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(137, 'DLSHA', 'Shahdara', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(138, 'DLSOD', 'South Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(139, 'DLSWD', 'South West Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(140, 'DLWED', 'West Delhi', 'DL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(141, 'GANRG', 'North Goa', 'GA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(142, 'GASUG', 'South Goa', 'GA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(143, 'GJAHM', 'Ahmedabad', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(144, 'GJAMR', 'Amreli', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(145, 'GJANA', 'Anand', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(146, 'GJARA', 'Aravalli', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(147, 'GJBAN', 'Banaskantha', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(148, 'GJBHA', 'Bhavnagar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(149, 'GJBHR', 'Bharuch', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(150, 'GJBOT', 'Botad', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(151, 'GJCHH', 'Chhota Udepur', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(152, 'GJDAH', 'Dahod', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(153, 'GJDAN', 'Dangs', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(154, 'GJDEV', 'Devbhoomi Dwarka', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(155, 'GJGAN', 'Gandhinagar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(156, 'GJGIR', 'Gir Somnath', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(157, 'GJJAM', 'Jamnagar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(158, 'GJJUN', 'Junagadh', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(159, 'GJKAC', 'Kachchh', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(160, 'GJKHE', 'Kheda', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(161, 'GJMAH', 'Mahisagar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(162, 'GJMEH', 'Mehsana', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(163, 'GJMOR', 'Morbi', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(164, 'GJNAR', 'Narmada', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(165, 'GJNAV', 'Navsari', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(166, 'GJPAN', 'Panchmahals', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(167, 'GJPAT', 'Patan', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(168, 'GJPOR', 'Porbandar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(169, 'GJRAJ', 'Rajkot', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(170, 'GJSAB', 'Sabarkantha', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(171, 'GJSUN', 'Surendranagar', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(172, 'GJSUT', 'Surat', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(173, 'GJTAP', 'Tapi', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(174, 'GJVAD', 'Vadodara', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(175, 'GJVAL', 'Valsad', 'GJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(176, 'HRAMB', 'Ambala', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(177, 'HRBHI', 'Bhiwani', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(178, 'HRFAR', 'Faridabad', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(179, 'HRFAT', 'Fatehabad', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(180, 'HRGUR', 'Gurgaon', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(181, 'HRHIS', 'Hisar', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(182, 'HRJHA', 'Jhajjar', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(183, 'HRJIN', 'Jind', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(184, 'HRKAI', 'Kaithal', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(185, 'HRKAR', 'Karnal', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(186, 'HRKUR', 'Kurukshetra', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(187, 'HRMAH', 'Mahendragarh', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(188, 'HRMEW', 'Mewat', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(189, 'HRPAK', 'Panchkula', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(190, 'HRPAL', 'Palwal', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(191, 'HRPAP', 'Panipat', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(192, 'HRREW', 'Rewari', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(193, 'HRROH', 'Rohtak', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(194, 'HRSIR', 'Sirsa', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(195, 'HRSON', 'Sonipat', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(196, 'HRYAM', 'Yamunanagar', 'HR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(197, 'HPBIL', 'Bilaspur', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(198, 'HPCHA', 'Chamba', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(199, 'HPHAM', 'Hamirpur', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(200, 'HPKAN', 'Kangra', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(201, 'HPKIN', 'Kinnaur', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(202, 'HPKUL', 'Kullu', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(203, 'HPLAH', 'Lahaul & Spiti', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(204, 'HPMAN', 'Mandi', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(205, 'HPSHI', 'Shimla', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(206, 'HPSIR', 'Sirmaur', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(207, 'HPSOL', 'Solan', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(208, 'HPUNA', 'Una', 'HP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(209, 'JKANA', 'Anantnag', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(210, 'JKBAN', 'Bandipore', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(211, 'JKBAR', 'Baramulla', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(212, 'JKBUD', 'Budgam', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(213, 'JKDOD', 'Doda', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(214, 'JKGAN', 'Ganderbal', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(215, 'JKJAM', 'Jammu', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(216, 'JKKAR', 'Kargil', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(217, 'JKKAT', 'Kathua', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(218, 'JKKIS', 'Kishtwar', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(219, 'JKKUL', 'Kulgam', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(220, 'JKKUP', 'Kupwara', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(221, 'JKLEH', 'Leh', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(222, 'JKPOO', 'Poonch', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(223, 'JKPUL', 'Pulwama', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(224, 'JKRAJ', 'Rajouri', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(225, 'JKRAM', 'Ramban', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(226, 'JKREA', 'Reasi', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(227, 'JKSAM', 'Samba', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(228, 'JKSHO', 'Shopian', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(229, 'JKSRI', 'Srinagar', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(230, 'JKUDH', 'Udhampur', 'JK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(231, 'JHBOK', 'Bokaro', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(232, 'JHCHA', 'Chatra', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(233, 'JHDEO', 'Deoghar', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(234, 'JHDHA', 'Dhanbad', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(235, 'JHDUM', 'Dumka', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(236, 'JHEAS', 'East Singhbhum', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(237, 'JHGAR', 'Garhwa', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(238, 'JHGIR', 'Giridih', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(239, 'JHGOD', 'Godda', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(240, 'JHGUM', 'Gumla', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(241, 'JHHAZ', 'Hazaribag', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(242, 'JHJAM', 'Jamtara', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(243, 'JHKHU', 'Khunti', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(244, 'JHKOD', 'Koderma', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(245, 'JHLAT', 'Latehar', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(246, 'JHLOH', 'Lohardaga', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(247, 'JHPAK', 'Pakur', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(248, 'JHPAL', 'Palamu', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(249, 'JHRAM', 'Ramgarh', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(250, 'JHRAN', 'Ranchi', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(251, 'JHSAH', 'Sahibganj', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(252, 'JHSER', 'Seraikela-Kharsawan', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(253, 'JHSIM', 'Simdega', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(254, 'JHWES', 'West Singhbhum', 'JH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(255, 'KABAG', 'Bagalkot', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(256, 'KABAL', 'Ballari (Bellary)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(257, 'KABEL', 'Belagavi (Belgaum)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(258, 'KABID', 'Bidar', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(259, 'KABNR', 'Bengaluru (Bangalore) Rural', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(260, 'KABNU', 'Bengaluru (Bangalore) Urban', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(261, 'KACHA', 'Chamrajanagar', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(262, 'KACHB', 'Chikballapur', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(263, 'KACHI', 'Chitradurga', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(264, 'KACHM', 'Chikkamagaluru (Chikmagalur)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(265, 'KADAK', 'Dakshina Kannada', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(266, 'KADAV', 'Davangere', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(267, 'KADHA', 'Dharwad', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(268, 'KAGAD', 'Gadag', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(269, 'KAHAS', 'Hassan', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(270, 'KAHAV', 'Haveri', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(271, 'KAKAL', 'Kalaburagi (Gulbarga)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(272, 'KAKOD', 'Kodagu', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(273, 'KAKOL', 'Kolar', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(274, 'KAKOP', 'Koppal', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(275, 'KAMAN', 'Mandya', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(276, 'KAMYS', 'Mysuru (Mysore)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(277, 'KARAI', 'Raichur', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(278, 'KARAM', 'Ramanagara', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(279, 'KASHI', 'Shivamogga (Shimoga)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(280, 'KATUM', 'Tumakuru (Tumkur)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(281, 'KAUDU', 'Udupi', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(282, 'KAUTT', 'Uttara Kannada', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(283, 'KAVIJ', 'Vijayapura (Bijapur)', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(284, 'KAYAD', 'Yadgir', 'KA', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(285, 'KLALA', 'Alappuzha', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(286, 'KLERN', 'Ernakulam', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(287, 'KLIDU', 'Idukki', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(288, 'KLKAN', 'Kannur', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(289, 'KLKAS', 'Kasaragod', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(290, 'KLKOM', 'Kollam', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(291, 'KLKOT', 'Kottayam', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(292, 'KLKOZ', 'Kozhikode', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(293, 'KLMAL', 'Malappuram', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(294, 'KLPAL', 'Palakkad', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(295, 'KLPAT', 'Pathanamthitta', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(296, 'KLTHI', 'Thiruvananthapuram', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(297, 'KLTHR', 'Thrissur', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(298, 'KLWAY', 'Wayanad', 'KL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(299, 'LDLAK', 'Lakshadweep', 'LD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(300, 'MPAGA', 'Agarmalwa', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(301, 'MPALI', 'Alirajpur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(302, 'MPANU', 'Anuppur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(303, 'MPASH', 'Ashoknagar', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(304, 'MPBAL', 'Balaghat', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(305, 'MPBAR', 'Barwani', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(306, 'MPBET', 'Betul', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(307, 'MPBHI', 'Bhind', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(308, 'MPBHO', 'Bhopal', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(309, 'MPBUR', 'Burhanpur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(310, 'MPCAP', 'Chhatarpur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(311, 'MPCHW', 'Chhindwara', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(312, 'MPDAM', 'Damoh', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(313, 'MPDAT', 'Datia', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(314, 'MPDEW', 'Dewas', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(315, 'MPDHA', 'Dhar', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(316, 'MPDIN', 'Dindori', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(317, 'MPGUN', 'Guna', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(318, 'MPGWA', 'Gwalior', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(319, 'MPHAR', 'Harda', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(320, 'MPHOS', 'Hoshangabad', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(321, 'MPIND', 'Indore', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(322, 'MPJAB', 'Jabalpur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(323, 'MPJHA', 'Jhabua', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(324, 'MPKAT', 'Katni', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(325, 'MPKHG', 'Khargone', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(326, 'MPKHW', 'Khandwa', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(327, 'MPMAD', 'Mandla', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(328, 'MPMAS', 'Mandsaur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(329, 'MPMOR', 'Morena', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(330, 'MPNAR', 'Narsinghpur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(331, 'MPNEE', 'Neemuch', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(332, 'MPPAN', 'Panna', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(333, 'MPRAI', 'Raisen', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(334, 'MPRAJ', 'Rajgarh', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(335, 'MPRAT', 'Ratlam', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(336, 'MPREW', 'Rewa', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(337, 'MPSAG', 'Sagar', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(338, 'MPSAT', 'Satna', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(339, 'MPSEH', 'Sehore', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(340, 'MPSEO', 'Seoni', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(341, 'MPSHD', 'Shahdol', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(342, 'MPSHE', 'Sheopur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(343, 'MPSHI', 'Shivpuri', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(344, 'MPSHJ', 'Shajapur', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(345, 'MPSID', 'Sidhi', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(346, 'MPSIN', 'Singrauli', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(347, 'MPTIK', 'Tikamgarh', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(348, 'MPUJJ', 'Ujjain', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(349, 'MPUMA', 'Umaria', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(350, 'MPVID', 'Vidisha', 'MP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(351, 'MHAHM', 'Ahmednagar', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(352, 'MHAKO', 'Akola', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(353, 'MHAMR', 'Amravati', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(354, 'MHAUR', 'Aurangabad', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(355, 'MHBEE', 'Beed', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(356, 'MHBHA', 'Bhandara', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(357, 'MHBUL', 'Buldhana', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(358, 'MHCHA', 'Chandrapur', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(359, 'MHDHU', 'Dhule', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(360, 'MHGAD', 'Gadchiroli', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(361, 'MHGON', 'Gondia', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(362, 'MHHIN', 'Hingoli', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(363, 'MHJAL', 'Jalna', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(364, 'MHJLG', 'Jalgaon', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(365, 'MHKOL', 'Kolhapur', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(366, 'MHLAT', 'Latur', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(367, 'MHMSU', 'Mumbai Suburban', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(368, 'MHMUC', 'Mumbai City', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(369, 'MHNAG', 'Nagpur', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(370, 'MHNAN', 'Nandurbar', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(371, 'MHNAS', 'Nashik', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(372, 'MHNND', 'Nanded', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(373, 'MHOSM', 'Osmanabad', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(374, 'MHPAL', 'Palghar', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(375, 'MHPAR', 'Parbhani', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(376, 'MHPUN', 'Pune', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(377, 'MHRAI', 'Raigad', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(378, 'MHRAT', 'Ratnagiri', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(379, 'MHSAN', 'Sangli', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(380, 'MHSAT', 'Satara', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(381, 'MHSIN', 'Sindhudurg', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(382, 'MHSOL', 'Solapur', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(383, 'MHTHA', 'Thane', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(384, 'MHWAR', 'Wardha', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(385, 'MHWAS', 'Washim', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(386, 'MHYAV', 'Yavatmal', 'MH', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(387, 'MNBIS', 'Bishnupur', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(388, 'MNCHA', 'Chandel', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(389, 'MNCHU', 'Churachandpur', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(390, 'MNIME', 'Imphal East', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(391, 'MNIMW', 'Imphal West', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(392, 'MNSEN', 'Senapati', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(393, 'MNTAM', 'Tamenglong', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(394, 'MNTHO', 'Thoubal', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(395, 'MNUKH', 'Ukhrul', 'MN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(396, 'MLEGH', 'East Garo Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(397, 'MLEJH', 'East Jaintia Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(398, 'MLEKH', 'East Khasi Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(399, 'MLNGH', 'North Garo Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(400, 'MLRIB', 'Ri Bhoi', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(401, 'MLSGH', 'South Garo Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(402, 'MLSWG', 'South West Garo Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(403, 'MLSWK', 'South West Khasi Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(404, 'MLWGH', 'West Garo Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(405, 'MLWJH', 'West Jaintia Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(406, 'MLWKH', 'West Khasi Hills', 'ML', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(407, 'MZAIZ', 'Aizawl', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(408, 'MZCHA', 'Champhai', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(409, 'MZKOL', 'Kolasib', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(410, 'MZLAW', 'Lawngtlai', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(411, 'MZLUN', 'Lunglei', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(412, 'MZMAM', 'Mamit', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(413, 'MZSAI', 'Saiha', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(414, 'MZSER', 'Serchhip', 'MZ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(415, 'NLDIM', 'Dimapur', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(416, 'NLKIP', 'Kiphire', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(417, 'NLKOH', 'Kohima', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(418, 'NLLON', 'Longleng', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(419, 'NLMOK', 'Mokokchung', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(420, 'NLMON', 'Mon', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(421, 'NLPER', 'Peren', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(422, 'NLPHE', 'Phek', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(423, 'NLTUE', 'Tuensang', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(424, 'NLWOK', 'Wokha', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(425, 'NLZUN', 'Zunheboto', 'NL', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(426, 'ODANG', 'Angul', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(427, 'ODBAR', 'Bargarh', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(428, 'ODBHA', 'Bhadrak', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(429, 'ODBLG', 'Balangir', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(430, 'ODBLS', 'Balasore', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(431, 'ODBOU', 'Boudh', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(432, 'ODCUT', 'Cuttack', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(433, 'ODDEO', 'Deogarh', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(434, 'ODDHE', 'Dhenkanal', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(435, 'ODGAJ', 'Gajapati', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(436, 'ODGAN', 'Ganjam', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(437, 'ODJAG', 'Jagatsinghapur', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(438, 'ODJAJ', 'Jajpur', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(439, 'ODJHA', 'Jharsuguda', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(440, 'ODKAL', 'Kalahandi', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(441, 'ODKAN', 'Kandhamal', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(442, 'ODKEN', 'Kendujhar', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(443, 'ODKHO', 'Khordha', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(444, 'ODKNP', 'Kendrapara', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(445, 'ODKOR', 'Koraput', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(446, 'ODMAL', 'Malkangiri', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(447, 'ODMAY', 'Mayurbhanj', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(448, 'ODNAB', 'Nabarangpur', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(449, 'ODNAY', 'Nayagarh', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(450, 'ODNUA', 'Nuapada', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(451, 'ODPUR', 'Puri', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(452, 'ODRAY', 'Rayagada', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(453, 'ODSAM', 'Sonepur', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(454, 'ODSLP', 'Sambalpur', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(455, 'ODSUN', 'Sundargarh', 'OD', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(456, 'PYKAR', 'Karaikal', 'PY', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(457, 'PYMAH', 'Mahe', 'PY', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(458, 'PYPON', 'Pondicherry', 'PY', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(459, 'PYYAN', 'Yanam', 'PY', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(460, 'PBAMR', 'Amritsar', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(461, 'PBBAR', 'Barnala', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(462, 'PBBAT', 'Bathinda', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(463, 'PBFAR', 'Faridkot', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(464, 'PBFAT', 'Fatehgarh Sahib', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(465, 'PBFAZ', 'Fazilka', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(466, 'PBFER', 'Ferozepur', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(467, 'PBGUR', 'Gurdaspur', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(468, 'PBHOS', 'Hoshiarpur', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(469, 'PBJAL', 'Jalandhar', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(470, 'PBKAP', 'Kapurthala', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(471, 'PBLUD', 'Ludhiana', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(472, 'PBMAN', 'Mansa', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(473, 'PBMOG', 'Moga', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(474, 'PBMUK', 'Muktsar', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(475, 'PBNAW', 'Nawanshahr', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(476, 'PBPAT', 'Patiala', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(477, 'PBPTK', 'Pathankot', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(478, 'PBRUP', 'Rupnagar', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(479, 'PBSAN', 'Sangrur', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(480, 'PBSAS', 'SAS Nagar', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(481, 'PBTAR', 'Tarn Taran', 'PB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(482, 'RJAJM', 'Ajmer', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL);
INSERT INTO `district` (`id`, `code`, `name`, `state_code`, `created_at`, `status`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(483, 'RJALW', 'Alwar', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(484, 'RJBAN', 'Banswara', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(485, 'RJBAR', 'Barmer', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(486, 'RJBHA', 'Bharatpur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(487, 'RJBHI', 'Bhilwara', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(488, 'RJBIK', 'Bikaner', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(489, 'RJBRN', 'Baran', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(490, 'RJBUN', 'Bundi', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(491, 'RJCHI', 'Chittorgarh', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(492, 'RJCHU', 'Churu', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(493, 'RJDAU', 'Dausa', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(494, 'RJDHO', 'Dholpur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(495, 'RJDUN', 'Dungarpur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(496, 'RJHAN', 'Hanumangarh', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(497, 'RJJAL', 'Jalore', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(498, 'RJJHA', 'Jhalawar', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(499, 'RJJHU', 'Jhunjhunu', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(500, 'RJJIP', 'Jaipur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(501, 'RJJIS', 'Jaisalmer', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(502, 'RJJOD', 'Jodhpur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(503, 'RJKAR', 'Karauli', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(504, 'RJKOT', 'Kota', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(505, 'RJNAG', 'Nagaur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(506, 'RJPAL', 'Pali', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(507, 'RJPRA', 'Pratapgarh', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(508, 'RJRAJ', 'Rajsamand', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(509, 'RJSAW', 'Sawai Madhopur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(510, 'RJSIK', 'Sikar', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(511, 'RJSIR', 'Sirohi', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(512, 'RJSRI', 'Sri Ganganagar', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(513, 'RJTON', 'Tonk', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(514, 'RJUDA', 'Udaipur', 'RJ', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(515, 'SKEAS', 'East Sikkim', 'SK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(516, 'SKNOS', 'North Sikkim', 'SK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(517, 'SKSOS', 'South Sikkim', 'SK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(518, 'SKWES', 'West Sikkim', 'SK', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(519, 'TNARI', 'Ariyalur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(520, 'TNCHE', 'Chennai', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(521, 'TNCOI', 'Coimbatore', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(522, 'TNCUD', 'Cuddalore', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(523, 'TNDHA', 'Dharmapuri', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(524, 'TNDIN', 'Dindigul', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(525, 'TNERO', 'Erode', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(526, 'TNKAR', 'Karur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(527, 'TNKNK', 'Kanyakumari', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(528, 'TNKNP', 'Kanchipuram', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(529, 'TNKRI', 'Krishnagiri', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(530, 'TNMAD', 'Madurai', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(531, 'TNNAG', 'Nagapattinam', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(532, 'TNNAM', 'Namakkal', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(533, 'TNNIL', 'Nilgiris', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(534, 'TNPER', 'Perambalur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(535, 'TNPUD', 'Pudukkottai', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(536, 'TNRAM', 'Ramanathapuram', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(537, 'TNSAL', 'Salem', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(538, 'TNSIV', 'Sivaganga', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(539, 'TNTHA', 'Thanjavur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(540, 'TNTHE', 'Theni', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(541, 'TNTHO', 'Thoothukudi', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(542, 'TNTIM', 'Tiruvannamalai', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(543, 'TNTIR', 'Tiruppur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(544, 'TNTIV', 'Tiruvarur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(545, 'TNTRL', 'Tirunelveli', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(546, 'TNTRP', 'Tiruchirappalli', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(547, 'TNTVL', 'Tiruvallur', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(548, 'TNVEL', 'Vellore', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(549, 'TNVIL', 'Viluppuram', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(550, 'TNVIR', 'Virudhunagar', 'TN', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(551, 'TGADI', 'Adilabad', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(552, 'TGBAD', 'Badradri', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(553, 'TGHYD', 'Hyderabad', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(554, 'TGJAG', 'Jagitial', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(555, 'TGJAN', 'Jangoan', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(556, 'TGJAY', 'Jayashankar', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(557, 'TGJOG', 'Jogulamba', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(558, 'TGKAM', 'Kamareddy', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(559, 'TGKAR', 'Karimnagar', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(560, 'TGKHA', 'Khammam', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(561, 'TGKOM', 'Komaram Bheem', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(562, 'TGMAB', 'Mahabubanad', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(563, 'TGMAH', 'Mahabubnagar', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(564, 'TGMAN', 'Mancherial', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(565, 'TGMDM', 'Medchal - Malkajgiri', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(566, 'TGMED', 'Medak', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(567, 'TGNAG', 'Nagarkurnool', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(568, 'TGNAL', 'Nalgonda', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(569, 'TGNIR', 'Nirmal', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(570, 'TGNIZ', 'Nizamabad', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(571, 'TGPED', 'Peddapalli', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(572, 'TGRAJ', 'Rajanna', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(573, 'TGRAN', 'Ranga Reddy', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(574, 'TGSAN', 'Sangareddy', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(575, 'TGSID', 'Siddipet', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(576, 'TGSUR', 'Suryapet', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(577, 'TGVIK', 'Vikarabad', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(578, 'TGWAN', 'Wanaparthy', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(579, 'TGWRR', 'Warangal Rural', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(580, 'TGWRU', 'Warangal Urban', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(581, 'TGYAD', 'Yadadri', 'TG', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(582, 'TRDHA', 'Dhalai', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(583, 'TRGOM', 'Gomati', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(584, 'TRKHO', 'Khowai', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(585, 'TRNOR', 'North Tripura', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(586, 'TRSEP', 'Sepahijala', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(587, 'TRSOU', 'South Tripura', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(588, 'TRUNA', 'Unakoti', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(589, 'TRWES', 'West Tripura', 'TR', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(590, 'UPAGR', 'Agra', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(591, 'UPALI', 'Aligarh', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(592, 'UPALL', 'Allahabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(593, 'UPAMB', 'Ambedkar Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(594, 'UPAME', 'Amethi', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(595, 'UPAMR', 'Amroha', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(596, 'UPAUR', 'Auraiya', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(597, 'UPAZA', 'Azamgarh', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(598, 'UPBAB', 'Barabanki', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(599, 'UPBAG', 'Baghpat', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(600, 'UPBAH', 'Bahraich', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(601, 'UPBAL', 'Balrampur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(602, 'UPBAN', 'Banda', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(603, 'UPBAR', 'Bareilly', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(604, 'UPBAS', 'Basti', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(605, 'UPBIJ', 'Bijnor', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(606, 'UPBLL', 'Ballia', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(607, 'UPBUD', 'Budaun', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(608, 'UPBUL', 'Bulandshahar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(609, 'UPCHA', 'Chandauli', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(610, 'UPCHI', 'Chitrakoot', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(611, 'UPDEO', 'Deoria', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(612, 'UPETH', 'Etah', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(613, 'UPETW', 'Etawah', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(614, 'UPFAI', 'Faizabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(615, 'UPFAR', 'Farrukhabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(616, 'UPFAT', 'Fatehpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(617, 'UPFIR', 'Firozabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(618, 'UPGAU', 'Gautam Buddha Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(619, 'UPGON', 'Gonda', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(620, 'UPGOR', 'Gorakhpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(621, 'UPGZB', 'Ghaziabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(622, 'UPGZP', 'Ghazipur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(623, 'UPHAM', 'Hamirpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(624, 'UPHAP', 'Hapur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(625, 'UPHAR', 'Hardoi', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(626, 'UPHAT', 'Hathras', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(627, 'UPJAL', 'Jalaun', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(628, 'UPJAU', 'Jaunpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(629, 'UPJHA', 'Jhansi', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(630, 'UPKAU', 'Kaushambi', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(631, 'UPKMN', 'Kanshiramnagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(632, 'UPKND', 'Kanpur Dehat', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(633, 'UPKNJ', 'Kannauj', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(634, 'UPKNN', 'Kanpur Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(635, 'UPKUS', 'Kushinagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(636, 'UPLAK', 'Lakhimpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(637, 'UPLAL', 'Lalitpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(638, 'UPLUC', 'Lucknow', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(639, 'UPMAH', 'Mahoba', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(640, 'UPMAI', 'Mainpuri', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(641, 'UPMAT', 'Mathura', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(642, 'UPMAU', 'Mau', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(643, 'UPMEE', 'Meerut', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(644, 'UPMHJ', 'Maharajganj', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(645, 'UPMIR', 'Mirzapur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(646, 'UPMOR', 'Moradabad', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(647, 'UPMUZ', 'Muzaffarnagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(648, 'UPPIL', 'Pilibhit', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(649, 'UPPRA', 'Pratapgarh', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(650, 'UPRAE', 'RaeBareli', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(651, 'UPRAM', 'Rampur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(652, 'UPSAH', 'Saharanpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(653, 'UPSAM', 'Sambhal', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(654, 'UPSHJ', 'Shahjahanpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(655, 'UPSHM', 'Shamali', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(656, 'UPSHR', 'Shravasti', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(657, 'UPSID', 'Siddharth Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(658, 'UPSIT', 'Sitapur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(659, 'UPSKN', 'Sant Kabir Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(660, 'UPSON', 'Sonbhadra', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(661, 'UPSRN', 'Sant Ravidas Nagar', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(662, 'UPSUL', 'Sultanpur', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(663, 'UPUNN', 'Unnao', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(664, 'UPVAR', 'Varanasi', 'UP', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(665, 'UTALM', 'Almora', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(666, 'UTBAG', 'Bageshwar', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(667, 'UTCHA', 'Champawat', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(668, 'UTCHM', 'Chamoli', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(669, 'UTDEH', 'Dehradun', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(670, 'UTHAR', 'Haridwar', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(671, 'UTNAI', 'Nainital', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(672, 'UTPAU', 'Pauri Garhwal', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(673, 'UTPIT', 'Pithoragarh', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(674, 'UTRUD', 'Rudraprayag', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(675, 'UTTEH', 'Tehri Garhwal', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(676, 'UTUDH', 'Udham Singh Nagar', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(677, 'UTUTT', 'Uttarkashi', 'UT', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(678, 'WBBAN', 'Bankura', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(679, 'WBBAR', 'Bardhaman', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(680, 'WBBIR', 'Birbhum', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(681, 'WBCOO', 'Cooch Behar', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(682, 'WBDAR', 'Darjeeling', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(683, 'WBEAS', 'East Medinipur', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(684, 'WBHOO', 'Hooghly', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(685, 'WBHOW', 'Howrah', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(686, 'WBJAL', 'Jalpaiguri', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(687, 'WBKOL', 'Kolkata', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(688, 'WBMAL', 'Malda', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(689, 'WBMUR', 'Murshidabad', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(690, 'WBNAD', 'Nadia', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(691, 'WBNRD', 'North Dinajpur', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(692, 'WBNRP', 'North 24 Parganas', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(693, 'WBPUR', 'Purulia', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(694, 'WBSUD', 'South Dinajpur', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(695, 'WBSUP', 'South 24 Parganas', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL),
(696, 'WBWEM', 'West Medinipur', 'WB', '2018-10-14 16:44:25', 'ACTIVE', 0, NULL, '2021-09-19 16:22:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(4) NOT NULL,
  `e_adhar` varchar(12) DEFAULT NULL,
  `e_name` varchar(20) DEFAULT NULL,
  `e_sex` varchar(1) DEFAULT NULL,
  `e_dob` varchar(10) DEFAULT NULL,
  `e_category` varchar(3) DEFAULT NULL,
  `e_designation` varchar(14) DEFAULT NULL,
  `e_qualification` varchar(20) DEFAULT NULL,
  `e_professional` varchar(11) DEFAULT NULL,
  `e_subject` varchar(21) DEFAULT NULL,
  `e_additional` varchar(21) DEFAULT NULL,
  `e_math_sc` varchar(10) DEFAULT NULL,
  `e_english` varchar(10) DEFAULT NULL,
  `e_social_st` varchar(6) DEFAULT NULL,
  `e_mobile` varchar(22) DEFAULT NULL,
  `e_email` varchar(30) DEFAULT NULL,
  `e_address` varchar(118) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry`
--

CREATE TABLE `enquiry` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `app_no` varchar(25) DEFAULT NULL,
  `student_class` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `student_sex` varchar(10) NOT NULL,
  `student_address` varchar(255) NOT NULL,
  `student_mobile` varchar(10) NOT NULL,
  `form_fee` float(12,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'AUTO',
  `remarks` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `student_father` varchar(255) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `source` varchar(50) DEFAULT NULL,
  `student_whatsapp` varchar(10) DEFAULT NULL,
  `enquiry_date` date DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiry`
--

INSERT INTO `enquiry` (`id`, `student_name`, `app_no`, `student_class`, `date_of_birth`, `student_sex`, `student_address`, `student_mobile`, `form_fee`, `status`, `remarks`, `created_at`, `created_by`, `student_father`, `updated_by`, `updated_at`, `source`, `student_whatsapp`, `enquiry_date`, `branch_id`) VALUES
(1, 'Raven Cardenas', 'E24110001', 'LKG', '0000-00-00', 'MALE', 'Laborum laboris non ', '8809117872', 50.00, 'ACTIVE', 'Voluptatum ad dolore', '2024-11-29 05:51:13', 1, 'Sed qui cillum qui v', 1, '2024-11-29 05:55:46', 'CALL/MSG', '8809117872', '2024-11-29', 1),
(2, '', NULL, '', '0000-00-00', '', '', '', 0.00, 'AUTO', '', '2024-11-29 05:52:29', 1, '', NULL, '2024-11-29 05:52:29', NULL, NULL, NULL, 0),
(3, 'Vance Graves', 'E24110003', 'UKG', '0000-00-00', 'MALE', 'Qui sunt nihil aper', '8231789502', 81.00, 'ACTIVE', 'Officia quae totam d', '2024-11-29 06:23:53', 2, 'Omnis sint occaecat ', 2, '2024-11-29 06:26:10', 'POSTER', '1661941529', '2024-11-29', 2),
(4, 'Judith Bean', 'E24110004', 'LKG', '0000-00-00', 'OTHER', 'Obcaecati architecto', '8329284619', 200.00, 'ACTIVE', 'Ullam error dolore d', '2024-11-29 06:25:43', 2, 'Optio et autem do d', 2, '2024-11-29 06:25:52', 'FRIENDS', '7648427982', '2000-10-24', 2);

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE `event_log` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `event_on` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `event_date` date NOT NULL,
  `event_desc` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'UNSEEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `id` int(11) NOT NULL,
  `exam_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_admission` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `total` int(11) NOT NULL,
  `percentile` float(5,1) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `attendance` varchar(25) DEFAULT NULL,
  `sanskrit_nb` int(11) DEFAULT 0,
  `sanskrit_se` int(11) DEFAULT 0,
  `sanskrit_mo` int(11) DEFAULT 0,
  `sanskrit_written` int(11) DEFAULT 0,
  `sanskrit_viva` int(11) DEFAULT 0,
  `supw` varchar(25) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `hindi_nb` int(11) DEFAULT 0,
  `hindi_se` int(11) DEFAULT 0,
  `hindi_written` int(11) DEFAULT 0,
  `hindi_viva` int(11) DEFAULT 0,
  `english_nb` int(11) DEFAULT 0,
  `english_se` int(11) DEFAULT 0,
  `english_written` int(11) DEFAULT 0,
  `english_viva` int(11) DEFAULT 0,
  `mathematics_nb` int(11) DEFAULT 0,
  `mathematics_se` int(11) DEFAULT 0,
  `mathematics_written` int(11) DEFAULT 0,
  `mathematics_viva` int(11) DEFAULT 0,
  `branch_id` int(11) DEFAULT NULL,
  `jonas_dennis` varchar(25) DEFAULT NULL,
  `maths_written` int(11) DEFAULT 0,
  `science_written` int(11) DEFAULT 0,
  `george_horn` varchar(25) DEFAULT NULL,
  `ergh` varchar(25) DEFAULT NULL,
  `maths_viva` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`id`, `exam_id`, `student_id`, `student_admission`, `created_by`, `created_at`, `status`, `total`, `percentile`, `remarks`, `attendance`, `sanskrit_nb`, `sanskrit_se`, `sanskrit_mo`, `sanskrit_written`, `sanskrit_viva`, `supw`, `updated_at`, `updated_by`, `hindi_nb`, `hindi_se`, `hindi_written`, `hindi_viva`, `english_nb`, `english_se`, `english_written`, `english_viva`, `mathematics_nb`, `mathematics_se`, `mathematics_written`, `mathematics_viva`, `branch_id`, `jonas_dennis`, `maths_written`, `science_written`, `george_horn`, `ergh`, `maths_viva`) VALUES
(1, '1', 3, 'Excepteur dolore qui', 1, '2024-11-29 08:51:01', '', 0, 0.0, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-11-29 08:51:01', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, 30, 32, NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `exam_list`
--

CREATE TABLE `exam_list` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `instructions` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_list`
--

INSERT INTO `exam_list` (`id`, `exam_name`, `start_date`, `end_date`, `instructions`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'annual', '2024-12-10', '2024-12-20', NULL, 'ACTIVE', '2024-11-29 06:49:51', 1, 1, '2024-11-29 06:50:11', 1),
(2, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 06:50:15', 1, NULL, '2024-11-29 06:50:15', 0),
(3, 'Exma 2', '2024-11-29', '2024-11-30', NULL, 'ACTIVE', '2024-11-29 08:23:40', 2, 2, '2024-11-29 08:23:53', 2),
(4, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:23:40', 1, NULL, '2024-11-29 08:23:40', 1),
(5, 'annual', '2016-08-27', '2005-10-16', NULL, 'ACTIVE', '2024-11-29 08:23:54', 2, 2, '2024-11-29 08:25:04', 2),
(6, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:25:06', 2, NULL, '2024-11-29 08:25:06', 2);

-- --------------------------------------------------------

--
-- Stand-in structure for view `exp_details`
-- (See below for the actual view)
--
CREATE TABLE `exp_details` (
`account_type` varchar(255)
,`id` int(11)
,`branch_id` int(11)
,`account_id` int(11)
,`txn_date` date
,`txn_type` varchar(25)
,`txn_amount` float(12,2)
,`txn_mode` varchar(255)
,`txn_remarks` varchar(255)
,`status` varchar(25)
,`created_by` int(11)
,`created_at` timestamp
,`updated_by` int(11)
,`updated_at` timestamp
,`cancel_at` timestamp
,`cancel_by` int(11)
,`cancel_remarks` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `fee_details`
--

CREATE TABLE `fee_details` (
  `id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `student_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `hostel_fee` float(12,2) DEFAULT NULL,
  `admission_fee` float(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `tuition_fee` float(12,2) DEFAULT 0.00,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stationary_fee` float(12,2) DEFAULT 0.00,
  `school_development_fee` float(12,2) DEFAULT 0.00,
  `exme_fee` float(12,2) DEFAULT 0.00,
  `registration_for_admission` float(12,2) DEFAULT 0.00,
  `branch_id` int(11) DEFAULT NULL,
  `tution_fee` float(12,2) DEFAULT 0.00,
  `transport_fee` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_details`
--

INSERT INTO `fee_details` (`id`, `created_by`, `student_class`, `hostel_fee`, `admission_fee`, `created_at`, `status`, `tuition_fee`, `updated_by`, `updated_at`, `stationary_fee`, `school_development_fee`, `exme_fee`, `registration_for_admission`, `branch_id`, `tution_fee`, `transport_fee`) VALUES
(1, 1, 'NUR', 500.00, 900.00, '2024-11-29 06:04:22', 'ACTIVE', 0.00, 1, '2024-11-29 06:05:40', 0.00, 0.00, 0.00, 0.00, 1, 500.00, 0.00),
(2, 1, 'LKG', 500.00, 1000.00, '2024-11-29 06:04:22', 'ACTIVE', 0.00, 1, '2024-11-29 06:05:24', 0.00, 0.00, 0.00, 0.00, 1, 700.00, 0.00),
(3, 1, 'UKG', 1000.00, 1200.00, '2024-11-29 06:04:22', 'ACTIVE', 0.00, 1, '2024-11-29 06:05:55', 0.00, 0.00, 0.00, 0.00, 1, 900.00, 0.00),
(4, 2, 'NUR', NULL, 1001.00, '2024-11-29 06:32:57', 'ACTIVE', 0.00, 2, '2024-11-29 06:43:52', 0.00, 0.00, 0.00, 0.00, 2, 401.00, 0.00),
(5, 2, 'LKG', NULL, 1002.00, '2024-11-29 06:32:57', 'ACTIVE', 0.00, 2, '2024-11-29 06:44:01', 0.00, 0.00, 0.00, 0.00, 2, 402.00, 0.00),
(6, 2, 'UKG', NULL, 1003.00, '2024-11-29 06:32:57', 'ACTIVE', 0.00, 2, '2024-11-29 06:43:39', 0.00, 0.00, 0.00, 0.00, 2, 403.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `fee_head`
--

CREATE TABLE `fee_head` (
  `id` int(11) NOT NULL,
  `fee_order` int(11) NOT NULL,
  `fee_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fee_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fee_month` varchar(2000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_class` varchar(2000) NOT NULL,
  `fee_amount` float(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `finance_type` varchar(255) NOT NULL,
  `admission_type` varchar(255) NOT NULL,
  `student_type` varchar(255) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_head`
--

INSERT INTO `fee_head` (`id`, `fee_order`, `fee_name`, `fee_type`, `fee_month`, `student_class`, `fee_amount`, `status`, `created_at`, `created_by`, `finance_type`, `admission_type`, `student_type`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 1, 'Admission Fee', 'CLASS', 'Admission_month', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:01:33', 1, 'NORMAL,BPL,FREE,WARD', 'NEW', 'GENERAL,TRANSPORT,HOSTELER', 1, '2024-11-29 06:02:08', 1),
(2, 2, 'Tution Fee', 'CLASS', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:02:11', 1, 'NORMAL', 'NEW,OLD', 'GENERAL,TRANSPORT,HOSTELER', 1, '2024-11-29 06:02:34', 1),
(3, 0, 'Transport Fee', 'STUDENT', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:02:36', 1, 'NORMAL', 'NEW,OLD', 'TRANSPORT', 1, '2024-11-29 06:05:05', 1),
(4, 0, 'Hostel Fee', 'CLASS', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:03:01', 1, 'NORMAL', 'NEW,OLD', 'HOSTELER', 1, '2024-11-29 06:04:03', 1),
(5, 0, '', '', '', '', 0.00, 'AUTO', '2024-11-29 06:04:04', 1, '', '', '', NULL, '2024-11-29 06:04:04', 0),
(6, 0, 'Tution Fee', 'CLASS', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:33:02', 2, 'NORMAL', 'NEW,OLD', 'GENERAL,TRANSPORT,HOSTELER', 2, '2024-11-29 06:33:25', 2),
(7, 0, 'Admission Fee', 'CLASS', 'Admission_month', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:33:26', 2, 'NORMAL', 'NEW,OLD', 'GENERAL,TRANSPORT,HOSTELER', 2, '2024-11-29 06:34:10', 2),
(8, 0, 'Transport Fee', 'STUDENT', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG', 0.00, 'ACTIVE', '2024-11-29 06:34:12', 2, 'NORMAL,BPL,FREE', 'NEW,OLD', 'TRANSPORT', 2, '2024-11-29 06:35:32', 2),
(9, 0, '', '', '', '', 0.00, 'AUTO', '2024-11-29 06:35:33', 2, '', '', '', NULL, '2024-11-29 06:35:33', 3);

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `holiday_date` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item`
--

CREATE TABLE `inventory_item` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `opening_stock` int(11) DEFAULT NULL,
  `current_stock` int(11) DEFAULT NULL,
  `unit` varchar(55) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `mrp` decimal(12,2) DEFAULT NULL,
  `gst` varchar(100) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `min_alert_limit` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_item`
--

INSERT INTO `inventory_item` (`id`, `cat_id`, `name`, `opening_stock`, `current_stock`, `unit`, `rate`, `mrp`, `gst`, `barcode`, `min_alert_limit`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 1, 'Science ', 50, 1220, 'pc', 800.00, 1000.00, NULL, NULL, 5, 'IN STOCK', '2024-11-29 06:43:38', 1, '2024-11-29 08:20:45', 1, 1),
(2, 2, 'Item 2', 10, 10, 'PC', 100.00, 10.00, NULL, NULL, 3, 'IN STOCK', '2024-11-29 08:06:05', 2, '2024-11-29 08:09:30', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `inv_no` varchar(255) DEFAULT NULL,
  `txn_date` date DEFAULT NULL,
  `prev_dues` float(12,2) DEFAULT NULL,
  `total` float(12,2) DEFAULT NULL,
  `payment` float(12,2) DEFAULT NULL,
  `dues` float(12,2) DEFAULT NULL,
  `payment_mode` varchar(55) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `student_id`, `inv_no`, `txn_date`, `prev_dues`, `total`, `payment`, `dues`, `payment_mode`, `remarks`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 3, '1220', '2024-11-29', 0.00, 1600.00, 1500.00, 100.00, 'CASH', NULL, 'CANCELLED', '2024-11-29 06:40:52', 1, 1, '2024-11-29 08:11:57', 1),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 06:47:52', 1, NULL, '2024-11-29 06:47:52', 0),
(3, 3, '1123', '2024-11-29', 100.00, 11720.00, 11800.00, 20.00, 'CASH', 'test', 'CLOSED', '2024-11-29 08:05:59', 1, 1, '2024-11-29 08:13:03', 1),
(4, 5, '1', '2024-11-29', 0.00, 3400.00, 2000.00, 1400.00, 'CASH', 'Test', 'CANCELLED', '2024-11-29 08:06:48', 2, 2, '2024-11-29 08:09:30', 2);

-- --------------------------------------------------------

--
-- Table structure for table `item_cat`
--

CREATE TABLE `item_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_cat`
--

INSERT INTO `item_cat` (`id`, `cat_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'books', 'ACTIVE', '2024-11-29 06:43:26', 1, '2024-11-29 06:43:37', 1, 1),
(2, 'dfg', 'ACTIVE', '2024-11-29 08:05:41', 2, '2024-11-29 08:06:04', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `notice_date` date NOT NULL,
  `notice_title` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `notice_details` varchar(4000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `notice_attachment` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_payment`
--

CREATE TABLE `online_payment` (
  `id` int(11) NOT NULL,
  `demand_id` varchar(255) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `student_admission` varchar(25) DEFAULT NULL,
  `payment_month` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) NOT NULL DEFAULT 'ONLINE',
  `payment_amount` float(12,2) DEFAULT NULL,
  `total_amount` float(12,2) NOT NULL,
  `txn_fee` float(12,2) NOT NULL DEFAULT 0.00,
  `txn_tax` float(12,2) NOT NULL DEFAULT 0.00,
  `remarks` varchar(2000) DEFAULT NULL,
  `status` varchar(25) DEFAULT 'ACTIVE',
  `txn_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) NOT NULL,
  `txn_date` date DEFAULT NULL,
  `receipt_id` varchar(25) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `op_sms`
--

CREATE TABLE `op_sms` (
  `id` int(11) NOT NULL,
  `mobile` text DEFAULT NULL,
  `text` varchar(1000) DEFAULT NULL,
  `sender_id` varchar(6) DEFAULT NULL,
  `request_id` varchar(255) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `delivery_time` timestamp NULL DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `op_sms`
--

INSERT INTO `op_sms` (`id`, `mobile`, `text`, `sender_id`, `request_id`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `delivery_time`, `branch_id`) VALUES
(1, '8809117872', 'Dear+Raven+Cardenas%2C+%0AYour+Application+for+Admission+in+class+LKG+submitted+successfully+with+application+no+is+E24110001+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 05:51:28', 1, NULL, NULL, NULL, 1),
(2, '8809117872', 'Dear+Raven+Cardenas%2C+%0AYour+Application+for+Admission+in+class+LKG+submitted+successfully+with+application+no+is+E24110001+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 05:54:47', 1, NULL, NULL, NULL, 1),
(3, '8809117872', 'Dear+Raven+Cardenas%2C+%0AYour+Application+for+Admission+in+class+LKG+submitted+successfully+with+application+no+is+E24110001+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 05:55:15', 1, NULL, NULL, NULL, 1),
(4, '8809117872', 'Dear+Raven+Cardenas%2C+%0AYour+Application+for+Admission+in+class+LKG+submitted+successfully+with+application+no+is+E24110001+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 05:55:46', 1, NULL, NULL, NULL, 1),
(5, '8231789502', 'Dear+Vance+Graves%2C+%0AYour+Application+for+Admission+in+class+UKG+submitted+successfully+with+application+no+is+E24110003+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 06:23:57', 2, NULL, NULL, NULL, 1),
(6, '8329284619', 'Dear+Judith+Bean%2C+%0AYour+Application+for+Admission+in+class+LKG+submitted+successfully+with+application+no+is+E24110004+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 06:25:52', 2, NULL, NULL, NULL, 1),
(7, '8231789502', 'Dear+Vance+Graves%2C+%0AYour+Application+for+Admission+in+class+UKG+submitted+successfully+with+application+no+is+E24110003+%0ARegards+%0ABineX+OFFAPP', NULL, NULL, NULL, '2024-11-29 06:26:10', 2, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `payment_book`
-- (See below for the actual view)
--
CREATE TABLE `payment_book` (
`student_name` varchar(255)
,`student_class` varchar(15)
,`student_section` varchar(255)
,`student_roll` int(11)
,`student_type` varchar(255)
,`id` int(11)
,`receipt_no` varchar(255)
,`branch_id` int(11)
,`student_id` int(11)
,`student_admission` varchar(25)
,`created_at` timestamp
,`urid` varchar(255)
,`paid_date` date
,`paid_month` varchar(255)
,`previous_dues` float(12,2)
,`discount` float(12,2)
,`transport_fee` float(12,2)
,`hostel_fee` float(12,2)
,`other_fee` float(12,2)
,`total` float(10,2)
,`paid_amount` float(10,2)
,`current_dues` float(10,2)
,`payment_mode` varchar(255)
,`remarks` varchar(255)
,`status` varchar(255)
,`cancel_at` datetime
,`cancel_by` int(11)
,`cancel_remarks` varchar(255)
,`created_by` int(11)
,`admission_fee` float(12,2)
,`tuition_fee` float(12,2)
,`updated_by` int(11)
,`updated_at` timestamp
,`stationary_fee` float(12,2)
,`school_development_fee` float(12,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item`
--

CREATE TABLE `purchase_item` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `vendor_cat` varchar(255) DEFAULT NULL,
  `quantity` int(20) DEFAULT NULL,
  `doi` date DEFAULT NULL,
  `rate` int(50) DEFAULT NULL,
  `mrp` int(50) DEFAULT NULL,
  `gst` int(10) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_item`
--

INSERT INTO `purchase_item` (`id`, `name`, `vendor`, `vendor_cat`, `quantity`, `doi`, `rate`, `mrp`, `gst`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, '1', '1', NULL, 1200, '2024-09-13', 680, 1000, NULL, 'IN STOCK', '2024-11-29 06:45:22', 1, '2024-11-29 06:45:47', 1, 1),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 06:45:48', 1, '2024-11-29 06:45:48', NULL, 0),
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:05:22', 2, '2024-11-29 08:05:22', NULL, 2),
(4, '1', '1', NULL, 20, '2024-11-29', 700, 1000, NULL, 'IN STOCK', '2024-11-29 08:20:33', 1, '2024-11-29 08:20:45', 1, 1),
(5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', '2024-11-29 08:20:47', 1, '2024-11-29 08:20:47', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `receipt_no` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_admission` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `urid` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `paid_date` date NOT NULL,
  `paid_month` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PREVIOUS DUES',
  `previous_dues` float(12,2) NOT NULL,
  `discount` float(12,2) NOT NULL,
  `transport_fee` float(12,2) NOT NULL,
  `hostel_fee` float(12,2) NOT NULL,
  `other_fee` float(12,2) NOT NULL,
  `total` float(10,2) NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `current_dues` float(10,2) NOT NULL,
  `payment_mode` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `remarks` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PAID',
  `cancel_at` datetime NOT NULL,
  `cancel_by` int(11) NOT NULL,
  `cancel_remarks` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `admission_fee` float(12,2) NOT NULL,
  `tuition_fee` float(12,2) DEFAULT 0.00,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stationary_fee` float(12,2) DEFAULT 0.00,
  `school_development_fee` float(12,2) DEFAULT 0.00,
  `exme_fee` float(12,2) DEFAULT 0.00,
  `registration_for_admission` float(12,2) DEFAULT 0.00,
  `branch_id` int(11) DEFAULT NULL,
  `idola_castillo` float(12,2) DEFAULT 0.00,
  `tution_fee` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`id`, `receipt_no`, `student_id`, `student_admission`, `created_at`, `urid`, `paid_date`, `paid_month`, `previous_dues`, `discount`, `transport_fee`, `hostel_fee`, `other_fee`, `total`, `paid_amount`, `current_dues`, `payment_mode`, `remarks`, `status`, `cancel_at`, `cancel_by`, `cancel_remarks`, `created_by`, `admission_fee`, `tuition_fee`, `updated_by`, `updated_at`, `stationary_fee`, `school_development_fee`, `exme_fee`, `registration_for_admission`, `branch_id`, `idola_castillo`, `tution_fee`) VALUES
(1, '', 4, 'Pariatur Enim esse ', '2024-11-29 06:14:40', '', '2024-11-29', 'admission_month,april,may,june', 56.00, 0.00, 0.00, 0.00, 0.00, 3956.00, 3500.00, 456.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 1, 1200.00, 0.00, NULL, '2024-11-29 06:14:40', 0.00, 0.00, 0.00, 0.00, 1, 0.00, 2700.00),
(2, '', 3, 'Excepteur dolore qui', '2024-11-29 06:18:04', '', '2024-11-29', 'other_month', 32.00, 0.00, 0.00, 0.00, 0.00, 32.00, 32.00, 0.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 1, 0.00, 0.00, NULL, '2024-11-29 06:18:04', 0.00, 0.00, 0.00, 0.00, 1, 0.00, 0.00),
(3, '', 3, 'Excepteur dolore qui', '2024-11-29 06:18:26', '', '2024-11-29', 'admission_month,april,may,june,july,august,september,october,november', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 1, 0.00, 0.00, NULL, '2024-11-29 06:18:26', 0.00, 0.00, 0.00, 0.00, 1, 0.00, 0.00),
(4, '', 5, 'Stu 1', '2024-11-29 06:45:58', '', '2024-11-29', 'admission_month,april,may,june,july,august,september,october,november', 0.00, 0.00, 0.00, 0.00, 0.00, 4900.00, 4000.00, 900.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 2, 900.00, 0.00, NULL, '2024-11-29 07:02:18', 0.00, 0.00, 0.00, 0.00, 2, 0.00, 4000.00),
(5, '', 5, 'Stu 1', '2024-11-29 06:50:51', '', '2024-11-29', 'other_month', 900.00, 0.00, 0.00, 0.00, 0.00, 900.00, 100.00, 800.00, 'Cash', 'Previous Dues', 'PAID', '0000-00-00 00:00:00', 0, '', 2, 0.00, 0.00, NULL, '2024-11-29 07:02:22', 0.00, 0.00, 0.00, 0.00, 2, 0.00, 0.00),
(6, '', 5, 'Stu 1', '2024-11-29 07:02:55', '', '2024-11-29', 'other_month', 800.00, 0.00, 0.00, 0.00, 0.00, 800.00, 200.00, 600.00, 'Cash', 'dfg', 'PAID', '0000-00-00 00:00:00', 0, '', 2, 0.00, 0.00, NULL, '2024-11-29 07:02:55', 0.00, 0.00, 0.00, 0.00, 2, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `sms_report`
--

CREATE TABLE `sms_report` (
  `id` int(11) NOT NULL,
  `requestId` varchar(100) DEFAULT NULL,
  `userName` varchar(100) DEFAULT NULL,
  `deliveredDateTime` datetime DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `senderId` varchar(100) DEFAULT NULL,
  `mobileNumber` bigint(20) DEFAULT NULL,
  `routeId` varchar(100) DEFAULT NULL,
  `providerReqId` varchar(100) DEFAULT NULL,
  `userId` int(50) DEFAULT NULL,
  `smppDelvieryTime` varchar(100) DEFAULT NULL,
  `statusCode` int(20) DEFAULT NULL,
  `sms_text` varchar(2000) DEFAULT NULL,
  `response_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=big5 COLLATE=big5_chinese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_template`
--

CREATE TABLE `sms_template` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(6) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_id` varchar(30) NOT NULL,
  `sms` varchar(5000) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT 'English',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'AUTO',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `code` varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`id`, `name`, `code`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'Andaman and Nicobar ', 'AN', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(2, 'Andhra Pradesh ', 'AP', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(3, 'Arunachal Pradesh ', 'AR', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(4, 'Assam ', 'AS', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(5, 'Bihar ', 'BR', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(6, 'Chandigarh ', 'CH', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(7, 'Chhattisgarh ', 'CT', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(8, 'Dadra and Nagar Haveli ', 'DN', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(9, 'Daman and Diu ', 'DD', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(10, 'Delhi ', 'DL', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(11, 'Goa ', 'GA', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(12, 'Gujarat ', 'GJ', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(13, 'Haryana ', 'HR', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(14, 'Himachal Pradesh ', 'HP', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(15, 'Jammu and Kashmir ', 'JK', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(16, 'Jharkhand ', 'JH', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(17, 'Karnataka ', 'KA', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(18, 'Kerala ', 'KL', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(19, 'Lakshadweep ', 'LD', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(20, 'Madhya Pradesh ', 'MP', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(21, 'Maharashtra ', 'MH', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(22, 'Manipur ', 'MN', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(23, 'Meghalaya ', 'ML', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(24, 'Mizoram ', 'MZ', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(25, 'Nagaland ', 'NL', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(26, 'Odisha ', 'OD', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(27, 'Puducherry ', 'PY', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(28, 'Punjab ', 'PB', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(29, 'Rajasthan ', 'RJ', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(30, 'Sikkim ', 'SK', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(31, 'Tamil Nadu ', 'TN', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(32, 'Telangana ', 'TG', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(33, 'Tripura ', 'TR', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(34, 'Uttar Pradesh ', 'UP', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(35, 'Uttarakhand ', 'UT', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL),
(36, 'West Bengal ', 'WB', 'ACTIVE', '2020-05-17 06:57:42', 0, NULL, '2021-09-19 16:22:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `student_admission` varchar(25) DEFAULT NULL,
  `school_admission` varchar(128) DEFAULT NULL,
  `student_session` varchar(255) NOT NULL,
  `student_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_mother` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `student_father` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `student_sex` char(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `student_mobile` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_photo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'no_image.jpg',
  `student_bloodgroup` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_of_admission` date NOT NULL,
  `student_class` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_section` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_roll` int(11) NOT NULL,
  `student_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `transport_area` varchar(255) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `mother_mobile` varchar(10) NOT NULL,
  `student_whatsapp` varchar(10) NOT NULL,
  `cbse_reg_no` varchar(255) NOT NULL,
  `vth_subject` varchar(255) NOT NULL,
  `family_income` varchar(25) NOT NULL,
  `admission_type` varchar(255) NOT NULL DEFAULT 'NEW',
  `finance_type` varchar(255) NOT NULL DEFAULT 'NORMAL',
  `father_qualification` varchar(255) NOT NULL,
  `mother_qualification` varchar(255) NOT NULL,
  `father_mobile` varchar(10) NOT NULL,
  `base_dues` float(12,2) NOT NULL DEFAULT 0.00,
  `father_occupation` varchar(255) NOT NULL,
  `mother_occupation` varchar(255) NOT NULL,
  `aadhar_no` varchar(12) NOT NULL,
  `pin_code` varchar(6) NOT NULL,
  `student_category` varchar(255) NOT NULL,
  `student_religion` varchar(255) NOT NULL,
  `student_caste` varchar(255) NOT NULL,
  `status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AUTO',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `state_code` varchar(10) DEFAULT NULL,
  `district_code` varchar(10) DEFAULT NULL,
  `student_address1` varchar(255) NOT NULL,
  `student_address2` varchar(255) NOT NULL,
  `bus_stop` varchar(255) NOT NULL,
  `transport_fee` float(12,2) NOT NULL,
  `item_dues` float(12,2) NOT NULL DEFAULT 0.00,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `student_admission`, `school_admission`, `student_session`, `student_name`, `student_mother`, `student_father`, `date_of_birth`, `student_sex`, `student_mobile`, `student_email`, `student_photo`, `student_bloodgroup`, `date_of_admission`, `student_class`, `student_section`, `student_roll`, `student_type`, `area_id`, `transport_area`, `trip_id`, `mother_mobile`, `student_whatsapp`, `cbse_reg_no`, `vth_subject`, `family_income`, `admission_type`, `finance_type`, `father_qualification`, `mother_qualification`, `father_mobile`, `base_dues`, `father_occupation`, `mother_occupation`, `aadhar_no`, `pin_code`, `student_category`, `student_religion`, `student_caste`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `state_code`, `district_code`, `student_address1`, `student_address2`, `bus_stop`, `transport_fee`, `item_dues`, `branch_id`) VALUES
(1, NULL, NULL, '', '', NULL, NULL, NULL, NULL, '', '', 'no_image.jpg', '', '0000-00-00', '', '', 0, '', NULL, '', 0, '', '', '', '', '', 'NEW', 'NORMAL', '', '', '', 0.00, '', '', '', '', '', '', '', 'AUTO', '2024-11-29 05:48:09', 130, NULL, '2024-11-29 05:48:09', NULL, NULL, '', '', '', 0.00, 0.00, 3),
(2, 'Molestiae tempor dol', 'Esse facilis quae do', 'Test Session', 'Emery Rowe', 'Irure ut eum exceptu', 'Est illum impedit', '2005-09-15', 'OTHER', '52', 'litewozeb@mailinator.com', 'no_image.jpg', 'AB-', '1990-10-22', 'UKG', 'B', 51, 'GENERAL', NULL, '', 0, '6014928984', '5348782042', 'Reprehenderit et sed', 'Et reprehenderit re', '984', 'OLD', 'WARD', 'Primary Education', 'Bachelor', '7412197884', 0.00, 'House Wife', 'Govt. Job', 'Nam quos sun', 'Et ita', 'OBC', 'HINDU', '', 'INACTIVE', '2024-11-29 05:51:09', 1, 1, '2024-11-29 05:56:21', 'HR', 'HRAMB', '61 South Oak Road', 'Sed ea deserunt est', '', 0.00, 0.00, 1),
(3, 'Excepteur dolore qui', 'Harum labore ex sunt', 'Test Session', 'Sebastian Fowler', 'Quis culpa quisquam', 'Dolorum porro a temp', '2021-04-15', 'FEMALE', '27', 'pugofa@mailinator.com', 'no_image.jpg', 'O+', '1978-12-22', 'NUR', 'B', 75, 'TRANSPORT', 1, '', 1, '5429456097', '3457353591', 'Omnis consequatur ne', 'Sit voluptatem Tem', '193', 'OLD', 'NORMAL', 'Primary Education', 'Doctorate or higher', '2899601290', 32.00, 'Student', 'Self Employed', 'Aut inventor', 'Labore', 'SC', 'MUSLIM', '', 'ACTIVE', '2024-11-29 05:56:27', 1, 1, '2024-11-29 08:13:03', 'BR', 'BRARA', '385 East New Freeway', 'Qui eu ducimus dolo', '', 0.00, 40.00, 1),
(4, 'Pariatur Enim esse ', 'Eu velit molestiae d', 'Test Session', 'Plato Randall', 'Quis facilis porro m', 'Duis voluptates non ', '2011-08-28', 'OTHER', '2', 'vekyhubequ@mailinator.com', 'no_image.jpg', 'B+', '2016-11-29', 'UKG', 'B', 50, 'GENERAL', NULL, '', 0, '3163267505', '8107601019', 'Libero delectus min', 'Quam odit rerum quia', '192', 'NEW', 'NORMAL', 'Doctorate or higher', 'Bachelor', '2061871139', 56.00, 'Govt. Job', 'Govt. Job', 'Aliqua Eos i', 'Eum ne', 'OBC', 'HINDU', '', 'ACTIVE', '2024-11-29 05:57:16', 1, 1, '2024-11-29 06:11:57', 'JH', 'JHBOK', '797 Rocky Clarendon Freeway', 'Adipisicing quae lab', '', 0.00, 0.00, 1),
(5, 'Stu 1', 'Dicta id laborum P', 'Test Session', 'Kirsten Hodge', 'Pariatur Est alias ', 'Fugiat do minim expl', '1997-02-13', 'MALE', '43', 'wazofaq@offerplant.com', '241129120038_WE7JV.png', 'A+', '1985-03-29', 'NUR', 'A', 18, 'GENERAL', NULL, '', 0, '8412141701', '8259140307', 'Possimus harum nequ', 'Voluptatem Error re', '75', 'OLD', 'NORMAL', '', '', '3559135445', 0.00, '', '', 'Doloremque p', 'Dolore', '', 'CHRISTIAN', '', 'ACTIVE', '2024-11-29 06:27:15', 2, 2, '2024-11-29 08:07:49', NULL, NULL, '20 West Nobel Drive', 'Ea eos dolores sit ', '', 0.00, 1400.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_fee`
--

CREATE TABLE `student_fee` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_admission` varchar(25) DEFAULT NULL,
  `current_dues` float(12,2) DEFAULT 0.00,
  `session_year` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `annual_fee` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `other_month` varchar(2000) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `admission_month` varchar(255) DEFAULT NULL,
  `january` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `february` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `march` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `april` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `may` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `june` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `july` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `august` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `september` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `october` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `november` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `december` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `transport_fee` float(12,2) DEFAULT 0.00,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exme_fee` float(12,2) DEFAULT 0.00,
  `registration_for_admission` float(12,2) DEFAULT 0.00,
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `branch_id` int(11) DEFAULT NULL,
  `idola_castillo` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_fee`
--

INSERT INTO `student_fee` (`id`, `student_id`, `student_admission`, `current_dues`, `session_year`, `annual_fee`, `other_month`, `admission_month`, `january`, `february`, `march`, `april`, `may`, `june`, `july`, `august`, `september`, `october`, `november`, `december`, `created_by`, `created_at`, `transport_fee`, `updated_by`, `updated_at`, `exme_fee`, `registration_for_admission`, `status`, `branch_id`, `idola_castillo`) VALUES
(1, 2, 'Molestiae tempor dol', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-11-29 11:26:21', 0.00, NULL, '2024-11-29 05:56:21', 0.00, 0.00, 'ACTIVE', 1, 0.00),
(2, 3, 'Excepteur dolore qui', 0.00, NULL, NULL, '2', '3', NULL, NULL, NULL, '3', '3', '3', '3', '3', '3', '3', '3', NULL, 1, '2024-11-29 11:26:37', 0.00, 1, '2024-11-29 06:18:26', 0.00, 0.00, 'ACTIVE', 1, 0.00),
(3, 4, 'Pariatur Enim esse ', 456.00, NULL, NULL, NULL, '1', NULL, NULL, NULL, '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-11-29 11:27:28', 0.00, 1, '2024-11-29 06:14:40', 0.00, 0.00, 'ACTIVE', 1, 0.00),
(4, 5, 'Stu 1', 600.00, NULL, NULL, '5,6', '4', NULL, NULL, NULL, '4', '4', '4', '4', '4', '4', '4', '4', NULL, 2, '2024-11-29 12:00:46', 0.00, 2, '2024-11-29 07:02:55', 0.00, 0.00, 'ACTIVE', 2, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `study_material`
--

CREATE TABLE `study_material` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `student_class` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(4000) NOT NULL,
  `yt_link` varchar(255) NOT NULL,
  `gm_link` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `subject_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `subject_order` int(11) NOT NULL,
  `student_class` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `subject_column` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `is_extra` varchar(11) NOT NULL DEFAULT 'NO',
  `status` varchar(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `subject_name`, `subject_type`, `subject_order`, `student_class`, `subject_column`, `is_extra`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'maths', 'Scholastic', 1, 'NUR,LKG,UKG', 'maths', 'NO', 'ACTIVE', 1, '2024-11-29 08:49:48', 1, '2024-11-29 08:49:48', 1),
(2, 'science', 'Scholastic', 2, 'NUR,LKG,UKG', 'science', 'NO', 'ACTIVE', 1, '2024-11-29 06:51:08', 1, '2024-11-29 06:51:08', 1),
(3, '', '', 0, '', '', 'NO', 'AUTO', 1, '2024-11-29 06:51:10', NULL, '2024-11-29 06:51:10', 0),
(4, 'George Horn', 'Non Scholastic', 89, 'NUR,LKG', 'george_horn', 'YES', 'ACTIVE', 2, '2024-11-29 08:27:36', 2, '2024-11-29 08:27:36', 2),
(5, '', '', 0, '', '', 'NO', 'AUTO', 2, '2024-11-29 08:27:38', NULL, '2024-11-29 08:27:38', 2),
(6, 'ergh', 'Non Scholastic', 3, 'NUR,LKG,UKG', 'ergh', 'NO', 'ACTIVE', 1, '2024-11-29 08:28:21', 1, '2024-11-29 08:28:21', 1),
(7, '', '', 0, '', '', 'NO', 'AUTO', 1, '2024-11-29 08:28:23', NULL, '2024-11-29 08:28:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cc`
--

CREATE TABLE `tbl_cc` (
  `id` int(4) UNSIGNED ZEROFILL NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_admission` varchar(255) NOT NULL,
  `student_class` varchar(255) NOT NULL,
  `session_year` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tc`
--

CREATE TABLE `tbl_tc` (
  `id` int(4) UNSIGNED ZEROFILL NOT NULL,
  `student_admission` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_father` varchar(255) NOT NULL,
  `student_mother` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `sc_st` varchar(255) NOT NULL,
  `d_first` varchar(255) NOT NULL,
  `d_birth` date NOT NULL,
  `dob_text` varchar(255) NOT NULL,
  `last_class` varchar(255) NOT NULL,
  `last_result` varchar(255) NOT NULL,
  `higher_class` varchar(255) NOT NULL,
  `subject_studies` varchar(255) NOT NULL,
  `promotion_higher_class` varchar(255) NOT NULL,
  `dues_paid` varchar(255) NOT NULL,
  `consession` varchar(255) NOT NULL,
  `working_day` varchar(255) NOT NULL,
  `total_present` varchar(255) NOT NULL,
  `ncc` varchar(255) NOT NULL,
  `game` varchar(255) NOT NULL,
  `conduct` varchar(255) NOT NULL,
  `doa_certificate` varchar(255) NOT NULL,
  `doi_certificate` varchar(255) NOT NULL,
  `reason_leaving` varchar(255) NOT NULL,
  `other_remarks` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_area`
--

CREATE TABLE `transport_area` (
  `id` int(11) NOT NULL,
  `area_name` varchar(2000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `area_fee` float(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_area`
--

INSERT INTO `transport_area` (`id`, `area_name`, `area_fee`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'Rampur', 100.00, 'ACTIVE', 2, '2024-11-29 08:01:00', 2, '2024-11-29 08:01:12', 2),
(2, 'cpr', 123.00, 'ACTIVE', 1, '2024-11-29 08:01:06', 1, '2024-11-29 08:01:20', 1),
(3, 'art', 0.00, 'ACTIVE', 2, '2024-11-29 08:01:13', 2, '2024-11-29 08:01:25', 2),
(4, '', 0.00, 'AUTO', 1, '2024-11-29 08:01:21', NULL, '2024-11-29 08:01:21', 1),
(5, '', 0.00, 'AUTO', 2, '2024-11-29 08:01:26', NULL, '2024-11-29 08:01:26', 2);

-- --------------------------------------------------------

--
-- Table structure for table `trip_details`
--

CREATE TABLE `trip_details` (
  `id` int(11) NOT NULL,
  `trip_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `vehicle_no` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `driver_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `area_list` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `gps_key` varchar(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_details`
--

INSERT INTO `trip_details` (`id`, `trip_name`, `departure_time`, `arrival_time`, `vehicle_no`, `driver_name`, `area_list`, `gps_key`, `created_at`, `status`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'trip ', '08:00:00', '09:00:00', 'BR01FP1057', 'deno', '', '', '2024-11-29 06:28:27', 'ACTIVE', 1, 1, '2024-11-29 06:36:16', 1),
(2, 'Ebony Lowe', '03:49:00', '18:09:00', 'Nesciunt ad possimu', 'Tatum Blevins', '', 'Qui quia enim sit q', '2024-11-29 06:36:19', 'ACTIVE', 1, 1, '2024-11-29 07:50:43', 1),
(3, '', '00:00:00', '00:00:00', '', '', '', '', '2024-11-29 07:51:44', 'AUTO', 1, NULL, '2024-11-29 07:51:45', 1),
(4, 'asds', '12:46:00', '19:26:00', 'Sit sed sint non lab', 'TaShya Russell', '', 'Culpa architecto arc', '2024-11-29 08:01:47', 'ACTIVE', 2, 2, '2024-11-29 08:04:10', 2),
(5, 'Shay Bartlett', '09:13:00', '07:57:00', 'Architecto voluptate', 'Clayton Shepherd', '', 'Sit ut sit pariatur', '2024-11-29 08:03:56', 'ACTIVE', 2, 2, '2024-11-29 08:04:00', 2),
(6, '', '00:00:00', '00:00:00', '', '', '', '', '2024-11-29 08:04:01', 'AUTO', 2, NULL, '2024-11-29 08:04:01', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_mobile` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_name` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_pass` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `doc` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'ACTIVE',
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` varchar(128) DEFAULT '1',
  `allowed_branch` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_type`, `full_name`, `user_mobile`, `user_name`, `user_pass`, `user_email`, `doc`, `status`, `token`, `last_login`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`, `allowed_branch`) VALUES
(1, 'ADMIN', 'Branch 1', '7992353365', 'branch1', '5f4699b7a4a800988dabe770f2749ace', 'st.georgeschool.b@gmail.com', '2021-10-13', 'ACTIVE', 'pa8lp0a1aqgqc1qo744ceb3eq0', '2024-11-29 05:50:59', 2, '2021-09-19 07:42:02', 1, '2024-11-29 05:50:59', '1', '1'),
(2, 'ADMIN', 'Branch 2', '8210487789', 'branch2', '41802a56984b22c73932f4c0111f2122', 'mesumitsumit@gmail.com', NULL, 'ACTIVE', '7m6gicn093g51bdt9um96hiigr', '2024-11-29 06:23:46', 22, '2024-08-20 11:47:17', 2, '2024-11-29 06:23:46', '2', '2');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `mobile` varchar(12) DEFAULT NULL,
  `gst` varchar(50) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `cat_name` varchar(25) NOT NULL,
  `opening_balance` decimal(12,2) DEFAULT NULL,
  `closing_balance` decimal(12,2) DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `status` varchar(60) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id`, `name`, `address`, `mobile`, `gst`, `category`, `cat_name`, `opening_balance`, `closing_balance`, `doj`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'Giselle Schroeder', 'Illo minus ullam est', '8795156589', NULL, '1', '', 400.00, NULL, '2024-01-29', 'ACTIVE', '2024-11-29 06:41:16', 1, '2024-11-29 06:43:07', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_cat`
--

CREATE TABLE `vendor_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `status` varchar(55) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_cat`
--

INSERT INTO `vendor_cat` (`id`, `cat_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'academic', 'ACTIVE', '2024-11-29 06:41:16', 1, '2024-11-29 06:42:23', 1, 1),
(2, NULL, 'AUTO', '2024-11-29 06:42:25', 1, '2024-11-29 06:42:25', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wa_sms`
--

CREATE TABLE `wa_sms` (
  `id` int(11) NOT NULL,
  `mobile` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `title` varchar(25) NOT NULL,
  `msg` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `exp_details`
--
DROP TABLE IF EXISTS `exp_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `exp_details`  AS SELECT `account_head`.`account_type` AS `account_type`, `account_txn`.`id` AS `id`, `account_txn`.`branch_id` AS `branch_id`, `account_txn`.`account_id` AS `account_id`, `account_txn`.`txn_date` AS `txn_date`, `account_txn`.`txn_type` AS `txn_type`, `account_txn`.`txn_amount` AS `txn_amount`, `account_txn`.`txn_mode` AS `txn_mode`, `account_txn`.`txn_remarks` AS `txn_remarks`, `account_txn`.`status` AS `status`, `account_txn`.`created_by` AS `created_by`, `account_txn`.`created_at` AS `created_at`, `account_txn`.`updated_by` AS `updated_by`, `account_txn`.`updated_at` AS `updated_at`, `account_txn`.`cancel_at` AS `cancel_at`, `account_txn`.`cancel_by` AS `cancel_by`, `account_txn`.`cancel_remarks` AS `cancel_remarks` FROM (`account_txn` join `account_head`) WHERE `account_txn`.`account_id` = `account_head`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `payment_book`
--
DROP TABLE IF EXISTS `payment_book`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `payment_book`  AS SELECT `student`.`student_name` AS `student_name`, `student`.`student_class` AS `student_class`, `student`.`student_section` AS `student_section`, `student`.`student_roll` AS `student_roll`, `student`.`student_type` AS `student_type`, `receipt`.`id` AS `id`, `receipt`.`receipt_no` AS `receipt_no`, `receipt`.`branch_id` AS `branch_id`, `receipt`.`student_id` AS `student_id`, `receipt`.`student_admission` AS `student_admission`, `receipt`.`created_at` AS `created_at`, `receipt`.`urid` AS `urid`, `receipt`.`paid_date` AS `paid_date`, `receipt`.`paid_month` AS `paid_month`, `receipt`.`previous_dues` AS `previous_dues`, `receipt`.`discount` AS `discount`, `receipt`.`transport_fee` AS `transport_fee`, `receipt`.`hostel_fee` AS `hostel_fee`, `receipt`.`other_fee` AS `other_fee`, `receipt`.`total` AS `total`, `receipt`.`paid_amount` AS `paid_amount`, `receipt`.`current_dues` AS `current_dues`, `receipt`.`payment_mode` AS `payment_mode`, `receipt`.`remarks` AS `remarks`, `receipt`.`status` AS `status`, `receipt`.`cancel_at` AS `cancel_at`, `receipt`.`cancel_by` AS `cancel_by`, `receipt`.`cancel_remarks` AS `cancel_remarks`, `receipt`.`created_by` AS `created_by`, `receipt`.`admission_fee` AS `admission_fee`, `receipt`.`tuition_fee` AS `tuition_fee`, `receipt`.`updated_by` AS `updated_by`, `receipt`.`updated_at` AS `updated_at`, `receipt`.`stationary_fee` AS `stationary_fee`, `receipt`.`school_development_fee` AS `school_development_fee` FROM (`student` join `receipt`) WHERE `receipt`.`student_id` = `student`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_head`
--
ALTER TABLE `account_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_txn`
--
ALTER TABLE `account_txn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_txn`
--
ALTER TABLE `admin_txn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admit_card`
--
ALTER TABLE `admit_card`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_class` (`student_class`,`subject_id`,`exam_id`);

--
-- Indexes for table `allow_user`
--
ALTER TABLE `allow_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_cat`
--
ALTER TABLE `book_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_list`
--
ALTER TABLE `book_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_name` (`book_name`,`category`);

--
-- Indexes for table `book_pub`
--
ALTER TABLE `book_pub`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_txn`
--
ALTER TABLE `book_txn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch_list`
--
ALTER TABLE `branch_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `closing_report`
--
ALTER TABLE `closing_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `closing_date` (`closing_date`);

--
-- Indexes for table `distribute_item`
--
ALTER TABLE `distribute_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiry`
--
ALTER TABLE `enquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_log`
--
ALTER TABLE `event_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_id` (`exam_id`,`student_admission`);

--
-- Indexes for table `exam_list`
--
ALTER TABLE `exam_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_name_2` (`exam_name`,`branch_id`);

--
-- Indexes for table `fee_details`
--
ALTER TABLE `fee_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_head`
--
ALTER TABLE `fee_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `area_name` (`holiday_name`);

--
-- Indexes for table `inventory_item`
--
ALTER TABLE `inventory_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_cat`
--
ALTER TABLE `item_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_payment`
--
ALTER TABLE `online_payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `demand_id` (`demand_id`);

--
-- Indexes for table `op_sms`
--
ALTER TABLE `op_sms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_report`
--
ALTER TABLE `sms_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requestId` (`requestId`);

--
-- Indexes for table `sms_template`
--
ALTER TABLE `sms_template`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_id` (`template_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_admission` (`student_admission`,`branch_id`);

--
-- Indexes for table `student_fee`
--
ALTER TABLE `student_fee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_admission` (`student_admission`);

--
-- Indexes for table `study_material`
--
ALTER TABLE `study_material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_column` (`subject_column`,`branch_id`);

--
-- Indexes for table `tbl_cc`
--
ALTER TABLE `tbl_cc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tc`
--
ALTER TABLE `tbl_tc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_admission` (`student_admission`);

--
-- Indexes for table `transport_area`
--
ALTER TABLE `transport_area`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `area_name_2` (`area_name`,`branch_id`);

--
-- Indexes for table `trip_details`
--
ALTER TABLE `trip_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trip_name` (`trip_name`,`branch_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_cat`
--
ALTER TABLE `vendor_cat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_sms`
--
ALTER TABLE `wa_sms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`,`title`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_head`
--
ALTER TABLE `account_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `account_txn`
--
ALTER TABLE `account_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_txn`
--
ALTER TABLE `admin_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admit_card`
--
ALTER TABLE `admit_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `allow_user`
--
ALTER TABLE `allow_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_cat`
--
ALTER TABLE `book_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `book_list`
--
ALTER TABLE `book_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_pub`
--
ALTER TABLE `book_pub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_txn`
--
ALTER TABLE `book_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branch_list`
--
ALTER TABLE `branch_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `closing_report`
--
ALTER TABLE `closing_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distribute_item`
--
ALTER TABLE `distribute_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=697;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry`
--
ALTER TABLE `enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_log`
--
ALTER TABLE `event_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_list`
--
ALTER TABLE `exam_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_details`
--
ALTER TABLE `fee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_head`
--
ALTER TABLE `fee_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_item`
--
ALTER TABLE `inventory_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item_cat`
--
ALTER TABLE `item_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_payment`
--
ALTER TABLE `online_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `op_sms`
--
ALTER TABLE `op_sms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_item`
--
ALTER TABLE `purchase_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sms_report`
--
ALTER TABLE `sms_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_template`
--
ALTER TABLE `sms_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_fee`
--
ALTER TABLE `student_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `study_material`
--
ALTER TABLE `study_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_cc`
--
ALTER TABLE `tbl_cc`
  MODIFY `id` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tc`
--
ALTER TABLE `tbl_tc`
  MODIFY `id` int(4) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_area`
--
ALTER TABLE `transport_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `trip_details`
--
ALTER TABLE `trip_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_cat`
--
ALTER TABLE `vendor_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wa_sms`
--
ALTER TABLE `wa_sms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
