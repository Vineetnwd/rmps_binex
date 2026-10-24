-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 02:30 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account_head`
--

INSERT INTO `account_head` (`id`, `account_type`, `account_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `branch_id`) VALUES
(1, 'ACTIVITY', 'SHYAM', 'ACTIVE', '2024-12-11 13:24:44', 6, '2024-12-11 13:25:03', 6, 1),
(2, 'ENERGY', 'RAMU', 'ACTIVE', '2024-12-11 13:24:44', 6, '2024-12-11 13:24:55', 6, 1),
(3, NULL, NULL, 'AUTO', '2024-12-11 13:25:05', 6, '2024-12-11 13:25:05', NULL, 1),
(4, NULL, NULL, 'AUTO', '2024-12-11 13:29:37', 7, '2024-12-11 13:29:37', NULL, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contain Account expense Details';

--
-- Dumping data for table `account_txn`
--

INSERT INTO `account_txn` (`id`, `account_id`, `txn_date`, `txn_type`, `txn_by`, `txn_amount`, `txn_mode`, `txn_remarks`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `cancel_at`, `cancel_by`, `cancel_remarks`, `branch_id`) VALUES
(1, 1, '2024-12-11', NULL, NULL, 1000.00, 'CASH', 'Bill Paid', 'ACTIVE', 7, '2024-12-11 13:29:35', NULL, '2024-12-11 13:29:35', '2024-12-11 13:29:35', NULL, NULL, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contain Account expense Details';

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `branch_list`
--

CREATE TABLE `branch_list` (
  `id` int(11) NOT NULL,
  `full_name` varchar(128) DEFAULT NULL,
  `inst_name` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `school_code` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `aff_no` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch_list`
--

INSERT INTO `branch_list` (`id`, `full_name`, `inst_name`, `school_code`, `aff_no`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`, `inst_address1`, `inst_address2`, `inst_contact`, `inst_email`, `inst_logo`, `inst_url`, `sender_id`, `adm_suffix`, `class_list`, `section_list`) VALUES
(1, 'DPS MKP', 'DPS Chapra', 'DPS02', NULL, 'ACTIVE', '2024-12-11 10:59:19', 1, 1, '2024-12-11 11:01:13', 0, 'Saran', NULL, '8809117872', NULL, NULL, NULL, NULL, NULL, 'NUR,LKG,UKG', 'A,B,C'),
(2, 'DPS', 'Delhi Public School', 'DPS01', NULL, 'ACTIVE', '2024-12-11 10:59:19', 1, 1, '2024-12-11 11:00:02', 0, 'Chapra', NULL, '8809117872', NULL, NULL, NULL, NULL, NULL, 'NUR,LKG,UKG', 'A,B,C'),
(3, NULL, NULL, NULL, NULL, 'AUTO', '2024-12-11 11:01:15', 1, NULL, '2024-12-11 11:01:15', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NUR,LKG,UKG', 'A,B,C');

-- --------------------------------------------------------

--
-- Table structure for table `career`
--

CREATE TABLE `career` (
  `id` int(11) NOT NULL,
  `hiring_title` varchar(500) CHARACTER SET latin1 NOT NULL,
  `job_post` varchar(255) CHARACTER SET latin1 NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `salary` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experiense` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `remark` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `id` int(10) NOT NULL,
  `code` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(31) CHARACTER SET utf8 DEFAULT NULL,
  `state_code` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id` int(11) NOT NULL,
  `emp_code` varchar(255) DEFAULT NULL,
  `adhar` varchar(12) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `category` varchar(55) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `professional` varchar(255) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(12) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `dl` varchar(255) DEFAULT NULL,
  `blood_group` varchar(4) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT NULL,
  `dl_proof` varchar(255) DEFAULT NULL,
  `aadhar_profile` varchar(255) DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `emp_type` varchar(255) DEFAULT NULL,
  `allow_subject` varchar(255) DEFAULT NULL,
  `allow_class` varchar(255) DEFAULT NULL,
  `allow_day` varchar(255) DEFAULT NULL,
  `class_teacher` varchar(25) DEFAULT NULL,
  `live_link` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `employee_att`
--

CREATE TABLE `employee_att` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `att_month` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_1` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_2` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_3` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_4` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_5` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_6` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_7` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_8` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_9` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_10` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_11` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_12` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_13` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_14` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_15` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_16` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_17` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_18` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_19` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_20` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_21` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_22` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_23` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_24` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_25` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_26` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_27` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_28` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_29` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_30` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_31` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(55) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_cat`
--

CREATE TABLE `emp_cat` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(255) DEFAULT NULL,
  `status` varchar(55) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE `event_log` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `event_on` varchar(255) CHARACTER SET latin1 NOT NULL,
  `event_date` date NOT NULL,
  `event_desc` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'UNSEEN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `exam_list`
--

CREATE TABLE `exam_list` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `instructions` text CHARACTER SET latin1 DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `exam_list`
--

INSERT INTO `exam_list` (`id`, `exam_name`, `start_date`, `end_date`, `instructions`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, NULL, NULL, NULL, NULL, 'AUTO', '2024-12-11 12:14:22', 1, NULL, '2024-12-11 12:14:22', 0);

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
  `student_class` varchar(255) CHARACTER SET latin1 NOT NULL,
  `hostel_fee` float(12,2) DEFAULT NULL,
  `admission_fee` float(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `tuition_fee` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fee_details`
--

INSERT INTO `fee_details` (`id`, `created_by`, `student_class`, `hostel_fee`, `admission_fee`, `created_at`, `status`, `updated_by`, `updated_at`, `branch_id`, `tuition_fee`) VALUES
(1, 1, 'NUR', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(2, 1, 'LKG', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(3, 1, 'UKG', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(4, 1, 'I', NULL, 1000.00, '2024-12-11 12:11:38', 'ACTIVE', 1, '2024-12-11 12:20:11', 2, 500.00),
(5, 1, 'II', NULL, 1000.00, '2024-12-11 12:11:38', 'ACTIVE', 1, '2024-12-11 12:20:11', 2, 600.00),
(6, 1, 'III', NULL, 1000.00, '2024-12-11 12:11:38', 'ACTIVE', 1, '2024-12-11 12:20:11', 2, 700.00),
(7, 1, 'IV', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(8, 1, 'V', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(9, 1, 'VI', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(10, 1, 'VII', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(11, 1, 'VIII', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(12, 1, 'IX', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(13, 1, 'X', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(14, 1, 'XI', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(15, 1, 'XII', NULL, NULL, '2024-12-11 12:11:38', 'ACTIVE', NULL, '2024-12-11 12:20:11', 2, 0.00),
(16, 6, 'NUR', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(17, 6, 'LKG', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(18, 6, 'UKG', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(19, 6, 'I', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(20, 6, 'II', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(21, 6, 'III', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(22, 6, 'IV', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(23, 6, 'V', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(24, 6, 'VI', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(25, 6, 'VII', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(26, 6, 'VIII', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(27, 6, 'IX', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(28, 6, 'X', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00),
(29, 6, 'XI', NULL, 5000.00, '2024-12-11 12:35:29', 'ACTIVE', 6, '2024-12-11 12:35:55', 1, 2200.00),
(30, 6, 'XII', NULL, NULL, '2024-12-11 12:35:29', 'ACTIVE', NULL, '2024-12-11 12:35:29', 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `fee_head`
--

CREATE TABLE `fee_head` (
  `id` int(11) NOT NULL,
  `fee_order` int(11) NOT NULL,
  `fee_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `fee_type` varchar(255) CHARACTER SET latin1 NOT NULL,
  `fee_month` varchar(2000) CHARACTER SET latin1 NOT NULL,
  `student_class` varchar(2000) NOT NULL,
  `fee_amount` float(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `finance_type` varchar(255) NOT NULL,
  `admission_type` varchar(255) NOT NULL,
  `student_type` varchar(255) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fee_head`
--

INSERT INTO `fee_head` (`id`, `fee_order`, `fee_name`, `fee_type`, `fee_month`, `student_class`, `fee_amount`, `status`, `created_at`, `created_by`, `finance_type`, `admission_type`, `student_type`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 0, 'Tuition Fee', 'CLASS', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:07:16', 1, 'NORMAL', 'NEW,OLD', 'GENERAL,TRANSPORT,HOSTELER', 1, '2024-12-11 12:18:52', 2),
(2, 0, 'Admission Fee', 'CLASS', 'Admission_month', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:09:56', 1, 'NORMAL', 'NEW', 'GENERAL,TRANSPORT,HOSTELER', 1, '2024-12-11 12:18:55', 2),
(3, 0, 'Transport Fee', 'STUDENT', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:10:11', 1, 'NORMAL', 'NEW,OLD', 'TRANSPORT', 1, '2024-12-11 12:18:58', 2),
(4, 0, '', '', '', '', 0.00, 'AUTO', '2024-12-11 12:11:35', 1, '', '', '', NULL, '2024-12-11 12:11:35', 0),
(5, 0, 'Tuition Fee', 'CLASS', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:34:42', 6, 'NORMAL', 'NEW,OLD', 'GENERAL,TRANSPORT,HOSTELER', 6, '2024-12-11 13:17:24', 1),
(6, 0, 'Admission Fee', 'CLASS', 'Admission_month', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:34:58', 6, 'NORMAL', 'NEW', 'GENERAL,TRANSPORT,HOSTELER', 6, '2024-12-11 12:59:34', 1),
(7, 0, 'Transport Fee', 'STUDENT', 'April,May,June,July,August,September,October,November,December,January,February,March', 'NUR,LKG,UKG,I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII', 0.00, 'ACTIVE', '2024-12-11 12:35:10', 6, 'NORMAL', 'NEW,OLD', 'TRANSPORT', 6, '2024-12-11 12:35:25', 1),
(8, 0, '', '', '', '', 0.00, 'AUTO', '2024-12-11 12:35:26', 6, '', '', '', NULL, '2024-12-11 12:35:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `status` varchar(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `image_title` varchar(128) DEFAULT NULL,
  `image_url` varchar(128) DEFAULT NULL,
  `image_status` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `holiday_date` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `leave_details`
--

CREATE TABLE `leave_details` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `leave_cause` varchar(255) DEFAULT NULL,
  `leave_type` varchar(20) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(60) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `notice_date` date NOT NULL,
  `notice_title` varchar(500) CHARACTER SET latin1 NOT NULL,
  `notice_details` varchar(4000) CHARACTER SET latin1 NOT NULL,
  `notice_attachment` varchar(255) CHARACTER SET latin1 NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Stand-in structure for view `payment_book`
-- (See below for the actual view)
--
CREATE TABLE `payment_book` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `receipt_no` varchar(255) CHARACTER SET latin1 NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_admission` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `urid` varchar(255) CHARACTER SET latin1 NOT NULL,
  `paid_date` date NOT NULL,
  `paid_month` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'PREVIOUS DUES',
  `previous_dues` float(12,2) NOT NULL,
  `discount` float(12,2) NOT NULL,
  `other_fee` float(12,2) NOT NULL,
  `total` float(10,2) NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `current_dues` float(10,2) NOT NULL,
  `payment_mode` varchar(255) CHARACTER SET latin1 NOT NULL,
  `remarks` varchar(255) CHARACTER SET latin1 NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'PAID',
  `cancel_at` datetime NOT NULL,
  `cancel_by` int(11) NOT NULL,
  `cancel_remarks` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `tuition_fee` float(12,2) DEFAULT 0.00,
  `transport_fee` float(12,2) DEFAULT 0.00,
  `hostel_fee` float(12,2) DEFAULT 0.00,
  `admission_fee` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`id`, `receipt_no`, `student_id`, `student_admission`, `created_at`, `urid`, `paid_date`, `paid_month`, `previous_dues`, `discount`, `other_fee`, `total`, `paid_amount`, `current_dues`, `payment_mode`, `remarks`, `status`, `cancel_at`, `cancel_by`, `cancel_remarks`, `created_by`, `updated_by`, `updated_at`, `branch_id`, `tuition_fee`, `transport_fee`, `hostel_fee`, `admission_fee`) VALUES
(1, '', 1, 'Dignissimos tenetur ', '2024-12-11 12:21:21', '', '2024-12-11', 'admission_month,april,may,june,july', 0.00, 300.00, 0.00, 3500.00, 300.00, 3200.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 3, NULL, '2024-12-11 12:21:21', 2, 2800.00, 0.00, 0.00, 1000.00),
(2, '', 1, 'Dignissimos tenetur ', '2024-12-11 12:22:00', '', '2024-12-11', 'august,september,october,november,december', 3200.00, 0.00, 0.00, 6700.00, 3000.00, 3700.00, 'Bank', '', 'CANCEL', '2024-12-11 05:59:16', 3, 'done by mistake', 3, 3, '2024-12-11 12:29:16', 2, 3500.00, 0.00, 0.00, 0.00),
(3, '', 1, 'Dignissimos tenetur ', '2024-12-11 12:29:54', '', '2024-12-11', 'august', 3200.00, 0.00, 0.00, 3900.00, 3400.00, 500.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 3, NULL, '2024-12-11 12:29:54', 2, 700.00, 0.00, 0.00, 0.00),
(4, '', 1, 'Dignissimos tenetur ', '2024-12-11 12:30:41', '', '2024-12-11', 'september', 500.00, 0.00, 0.00, 1200.00, 1000.00, 200.00, 'Bank', '', 'PAID', '0000-00-00 00:00:00', 0, '', 3, NULL, '2024-12-11 12:30:41', 2, 700.00, 0.00, 0.00, 0.00),
(5, '', 2, 'Mollitia expedita se', '2024-12-11 13:19:15', '', '2024-12-11', 'april,may', 0.00, 0.00, 0.00, 8400.00, 5000.00, 3400.00, 'Cash', '', 'PAID', '0000-00-00 00:00:00', 0, '', 7, NULL, '2024-12-11 13:19:15', 1, 4400.00, 4000.00, 0.00, 0.00),
(6, '', 2, 'Mollitia expedita se', '2024-12-11 13:20:44', '', '2024-12-11', 'other_month', 3400.00, 400.00, 0.00, 3000.00, 3000.00, 0.00, 'Bank', '', 'PAID', '0000-00-00 00:00:00', 0, '', 7, NULL, '2024-12-11 13:20:44', 1, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `salary`
--

CREATE TABLE `salary` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `month` varchar(55) DEFAULT NULL,
  `basic_salary` decimal(12,2) DEFAULT NULL,
  `total_present` int(11) DEFAULT NULL,
  `total_absent` int(11) DEFAULT NULL,
  `total_leave` int(11) DEFAULT NULL,
  `payable_amount` decimal(12,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status` varchar(100) DEFAULT NULL,
  `payment_mode` varchar(55) DEFAULT NULL,
  `paid_amount` decimal(12,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` varchar(55) DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=big5;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `name` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `code` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `student_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `student_mother` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `student_father` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `student_sex` char(10) CHARACTER SET latin1 DEFAULT NULL,
  `student_mobile` varchar(20) CHARACTER SET latin1 NOT NULL,
  `student_email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `student_photo` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'no_image.jpg',
  `student_bloodgroup` varchar(255) CHARACTER SET latin1 NOT NULL,
  `date_of_admission` date NOT NULL,
  `student_class` varchar(15) CHARACTER SET latin1 NOT NULL,
  `student_section` varchar(255) CHARACTER SET latin1 NOT NULL,
  `student_roll` int(11) NOT NULL,
  `student_type` varchar(255) CHARACTER SET latin1 NOT NULL,
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
  `status` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT 'AUTO',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `student_admission`, `school_admission`, `student_session`, `student_name`, `student_mother`, `student_father`, `date_of_birth`, `student_sex`, `student_mobile`, `student_email`, `student_photo`, `student_bloodgroup`, `date_of_admission`, `student_class`, `student_section`, `student_roll`, `student_type`, `area_id`, `transport_area`, `trip_id`, `mother_mobile`, `student_whatsapp`, `cbse_reg_no`, `vth_subject`, `family_income`, `admission_type`, `finance_type`, `father_qualification`, `mother_qualification`, `father_mobile`, `base_dues`, `father_occupation`, `mother_occupation`, `aadhar_no`, `pin_code`, `student_category`, `student_religion`, `student_caste`, `status`, `created_at`, `created_by`, `updated_by`, `updated_at`, `state_code`, `district_code`, `student_address1`, `student_address2`, `bus_stop`, `transport_fee`, `item_dues`, `branch_id`) VALUES
(1, 'Dignissimos tenetur ', 'Consequatur natus de', 'Test Session', 'Kaseem Rowe', 'Dolore assumenda imp', 'Voluptas itaque magn', '1981-12-03', 'OTHER', '8809117872', 'wube@mailinator.com', '241211053151_XIS7N.png', 'O+', '1991-06-08', 'III', 'A', 33, 'GENERAL', NULL, '', 0, '4634125207', '3718344204', 'Beatae magni pariatu', 'Illum est anim cill', '975', 'NEW', 'NORMAL', 'No Formal Education', 'Bachelor', '1141300962', 0.00, 'House Wife', '', 'Veniam verit', 'Ad est', 'General', 'MUSLIM', '', 'ACTIVE', '2024-12-11 12:01:36', 1, 1, '2024-12-11 12:18:06', NULL, NULL, '22 Fabien Road', 'Omnis culpa error p', '', 0.00, 0.00, 2),
(2, 'Mollitia expedita se', 'Est eius qui laborum', 'Test Session', 'Ori Rosario', 'Et facilis distincti', 'Irure velit quia cum', '2013-08-29', 'MALE', '8809117872', 'zusupe@mailinator.com', '241211060412_QXRVL.png', 'A+', '1978-08-20', 'XI', 'B', 95, 'TRANSPORT', 1, '', 0, '3476246439', '2930137942', 'Aut irure corporis o', 'Debitis ratione dese', '596', 'OLD', 'NORMAL', '', '', '2724146583', 0.00, '', '', 'Dolores qui ', 'Minim ', '', 'SHIKH', '', 'ACTIVE', '2024-12-11 12:34:03', 6, 6, '2024-12-11 12:36:22', NULL, NULL, '48 North Clarendon Avenue', 'Sapiente iure rerum ', '', 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_att`
--

CREATE TABLE `student_att` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `att_month` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_1` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_2` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_3` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_4` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_5` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_6` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_7` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_8` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_9` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_10` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_11` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_12` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_13` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_14` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_15` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_16` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_17` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_18` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_19` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_20` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_21` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_22` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_23` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_24` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_25` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_26` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_27` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_28` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_29` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_30` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_31` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(55) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_fee`
--

CREATE TABLE `student_fee` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_admission` varchar(25) DEFAULT NULL,
  `current_dues` float(12,2) DEFAULT 0.00,
  `session_year` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `annual_fee` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_month` varchar(2000) CHARACTER SET latin1 DEFAULT NULL,
  `admission_month` varchar(255) DEFAULT NULL,
  `january` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `february` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `march` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `april` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `may` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `june` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `july` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `august` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `september` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `october` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `november` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `december` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(25) NOT NULL DEFAULT 'ACTIVE',
  `branch_id` int(11) DEFAULT NULL,
  `transport_fee` float(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_fee`
--

INSERT INTO `student_fee` (`id`, `student_id`, `student_admission`, `current_dues`, `session_year`, `annual_fee`, `other_month`, `admission_month`, `january`, `february`, `march`, `april`, `may`, `june`, `july`, `august`, `september`, `october`, `november`, `december`, `created_by`, `created_at`, `updated_by`, `updated_at`, `status`, `branch_id`, `transport_fee`) VALUES
(1, 1, 'Dignissimos tenetur ', 200.00, NULL, NULL, NULL, '1', NULL, NULL, NULL, '1', '1', '1', '1', '3', '4', NULL, NULL, NULL, 1, '2024-12-11 17:32:11', 3, '2024-12-11 12:30:41', 'ACTIVE', 2, 0.00),
(2, 2, 'Mollitia expedita se', 0.00, NULL, NULL, '6', NULL, NULL, NULL, NULL, '5', '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, '2024-12-11 18:04:37', 7, '2024-12-11 13:20:44', 'ACTIVE', 1, 0.00);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `subject_type` varchar(255) CHARACTER SET latin1 NOT NULL,
  `subject_order` int(11) NOT NULL,
  `student_class` varchar(255) CHARACTER SET latin1 NOT NULL,
  `subject_column` varchar(255) CHARACTER SET latin1 NOT NULL,
  `is_extra` varchar(11) NOT NULL DEFAULT 'NO',
  `status` varchar(11) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `subject_name`, `subject_type`, `subject_order`, `student_class`, `subject_column`, `is_extra`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, '', '', 0, '', '', 'NO', 'AUTO', 1, '2024-12-11 12:12:19', NULL, '2024-12-11 12:12:19', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transport_area`
--

CREATE TABLE `transport_area` (
  `id` int(11) NOT NULL,
  `area_name` varchar(2000) CHARACTER SET latin1 NOT NULL,
  `area_fee` float(12,2) NOT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transport_area`
--

INSERT INTO `transport_area` (`id`, `area_name`, `area_fee`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`) VALUES
(1, 'Transport 1', 2000.00, 'ACTIVE', 6, '2024-12-11 12:36:01', 6, '2024-12-11 13:18:25', 1),
(2, '', 0.00, 'AUTO', 6, '2024-12-11 12:36:12', NULL, '2024-12-11 12:36:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `trip_details`
--

CREATE TABLE `trip_details` (
  `id` int(11) NOT NULL,
  `trip_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `vehicle_no` varchar(255) CHARACTER SET latin1 NOT NULL,
  `driver_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `area_list` varchar(255) CHARACTER SET latin1 NOT NULL,
  `gps_key` varchar(1000) CHARACTER SET latin1 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `user_mobile` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `user_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `user_pass` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `user_email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `doc` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'ACTIVE',
  `token` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` varchar(128) DEFAULT '1',
  `allowed_branch` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_type`, `full_name`, `user_mobile`, `user_name`, `user_pass`, `user_email`, `doc`, `status`, `token`, `last_login`, `created_by`, `created_at`, `updated_by`, `updated_at`, `branch_id`, `allowed_branch`) VALUES
(1, 'DEV', 'OFFERPLANT', '9431426600', 'offerplant', '4501ed01cb1149a9b7676e503d9a902d', 'ASK@OFFERPLANT.COM', '2024-12-11', 'LOGOUT', '1fv69ck6qomqv71msnkt2qoq65', '2024-12-11 12:33:35', NULL, '2024-12-11 11:07:34', 1, '2024-12-11 12:33:35', '', NULL),
(2, 'ADMIN', 'admin', '8809117872', 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, NULL, 'LOGOUT', 'pppg8gh0lhfdne0j1q37ik23sp', '2024-12-11 12:18:15', 1, '2024-12-11 15:40:40', 2, '2024-12-11 12:18:15', '2', '2'),
(3, 'ACCOUNT', 'account', '8809117872', 'account', 'e268443e43d93dab7ebef303bbe9642f', NULL, NULL, 'ACTIVE', '5eurb5pb4idf82afqq75n1l81r', '2024-12-11 13:30:03', 1, '2024-12-11 16:34:01', 3, '2024-12-11 13:30:03', '2', '2'),
(4, 'TEACHER', 'teacher', '8809117872', 'teacher', '8d788385431273d11e8b43bb78f3aa41', NULL, NULL, 'ACTIVE', NULL, '2024-12-11 11:15:20', 1, '2024-12-11 16:43:28', 1, '2024-12-11 11:15:20', '2', '2'),
(5, 'DBA', 'dba', '8809117872', 'dba', 'd693c4871a99d7acf43c4b1112da0c6e', NULL, NULL, 'ACTIVE', NULL, '2024-12-11 11:15:35', 1, '2024-12-11 16:45:22', 1, '2024-12-11 11:15:35', '2', '2'),
(6, 'ADMIN', 'admin2', '8809117872', 'admin2', 'c84258e9c39059a89ab77d846ddab909', NULL, NULL, 'LOGOUT', '8lk1bsnv6lc0tce3tlusth4101', '2024-12-11 13:25:08', 1, '2024-12-11 16:45:36', 6, '2024-12-11 13:25:08', '1', '1'),
(7, 'ACCOUNT', 'account', '8809117872', 'account2', '93189e2c4c7b1a2c7b16a24d5daa98a9', NULL, NULL, 'LOGOUT', 'cdh9fcm9dg69ft6qo4er6j85el', '2024-12-11 13:29:50', 1, '2024-12-11 18:02:14', 7, '2024-12-11 13:29:50', '1', '1'),
(8, 'DBA', 'dba2', '8809117872', 'dba2', '1368e7f5b47edc684ef805daa15145b4', NULL, NULL, 'ACTIVE', NULL, '2024-12-11 12:33:07', 1, '2024-12-11 18:02:49', 1, '2024-12-11 12:33:07', '1', '1'),
(9, 'TEACHER', 'teacher2', '8809117872', 'teacher2', 'ccffb0bb993eeb79059b31e1611ec353', NULL, NULL, 'ACTIVE', NULL, '2024-12-11 12:33:22', 1, '2024-12-11 18:03:08', 1, '2024-12-11 12:33:22', '1', '1'),
(10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AUTO', NULL, '2024-12-11 12:33:24', 1, '2024-12-11 18:03:24', NULL, '2024-12-11 12:33:24', '', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wa_sms`
--

CREATE TABLE `wa_sms` (
  `id` int(11) NOT NULL,
  `mobile` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `msg` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT 'PENDING',
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
-- Indexes for table `career`
--
ALTER TABLE `career`
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`);

--
-- Indexes for table `employee_att`
--
ALTER TABLE `employee_att`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_cat`
--
ALTER TABLE `emp_cat`
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_class` (`student_class`,`branch_id`);

--
-- Indexes for table `fee_head`
--
ALTER TABLE `fee_head`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_name` (`fee_name`,`branch_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
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
-- Indexes for table `leave_details`
--
ALTER TABLE `leave_details`
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
-- Indexes for table `salary`
--
ALTER TABLE `salary`
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
-- Indexes for table `student_att`
--
ALTER TABLE `student_att`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `account_txn`
--
ALTER TABLE `account_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_txn`
--
ALTER TABLE `admin_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admit_card`
--
ALTER TABLE `admit_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allow_user`
--
ALTER TABLE `allow_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_cat`
--
ALTER TABLE `book_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_list`
--
ALTER TABLE `book_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_pub`
--
ALTER TABLE `book_pub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `book_txn`
--
ALTER TABLE `book_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_list`
--
ALTER TABLE `branch_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `career`
--
ALTER TABLE `career`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=697;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_att`
--
ALTER TABLE `employee_att`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_cat`
--
ALTER TABLE `emp_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry`
--
ALTER TABLE `enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_log`
--
ALTER TABLE `event_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_list`
--
ALTER TABLE `exam_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_details`
--
ALTER TABLE `fee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `fee_head`
--
ALTER TABLE `fee_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_item`
--
ALTER TABLE `inventory_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_cat`
--
ALTER TABLE `item_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_details`
--
ALTER TABLE `leave_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_item`
--
ALTER TABLE `purchase_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_att`
--
ALTER TABLE `student_att`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_fee`
--
ALTER TABLE `student_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `study_material`
--
ALTER TABLE `study_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trip_details`
--
ALTER TABLE `trip_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_cat`
--
ALTER TABLE `vendor_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wa_sms`
--
ALTER TABLE `wa_sms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
