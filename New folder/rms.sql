-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 03:06 AM
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
-- Database: `rms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_verifications`
--

INSERT INTO `email_verifications` (`id`, `email`, `code`, `created_at`, `updated_at`) VALUES
(5, 'user@example.com', '799260', '2025-05-21 20:44:35', '2025-05-21 20:44:48');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"56f73953-a5d1-499b-aa2d-c01a45035b83\",\"displayName\":\"App\\\\Notifications\\\\PaymentStatusUpdated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:38:\\\"App\\\\Notifications\\\\PaymentStatusUpdated\\\":2:{s:15:\\\"\\u0000*\\u0000paymentProof\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\PaymentProof\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"587e5b2f-18fa-4299-ba68-a1bbdfa23b34\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}', 0, NULL, 1747468542, 1747468542),
(2, 'default', '{\"uuid\":\"4016a9a8-42c4-4225-be25-c41c14eed356\",\"displayName\":\"App\\\\Notifications\\\\PaymentStatusUpdated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:38:\\\"App\\\\Notifications\\\\PaymentStatusUpdated\\\":2:{s:15:\\\"\\u0000*\\u0000paymentProof\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\PaymentProof\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"587e5b2f-18fa-4299-ba68-a1bbdfa23b34\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}', 0, NULL, 1747468542, 1747468542);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(10, '2025_05_16_220200_add_name_and_password_to_email_verifications_table', 1),
(12, '0001_01_01_000000_create_users_table', 2),
(13, '0001_01_01_000001_create_cache_table', 2),
(14, '0001_01_01_000002_create_jobs_table', 2),
(15, '2025_03_09_104328_add_balance_to_users_table', 2),
(16, '2025_03_09_182220_create_receipts_table', 2),
(17, '2025_03_11_081528_add_verification_code_to_users_table', 2),
(18, '2025_03_12_082615_create_email_verifications_table', 2),
(19, '2025_03_14_185100_create_parking_spots_table', 2),
(20, '2025_03_14_185231_create_reservations_table', 2),
(21, '2025_05_17_055534_remove_is_verified_from_users_table', 2),
(22, 'xxxx_xx_xx_create_notifications_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parking_spots`
--

CREATE TABLE `parking_spots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_per_hour` decimal(8,2) NOT NULL,
  `is_reserved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_spots`
--

INSERT INTO `parking_spots` (`id`, `name`, `price_per_hour`, `is_reserved`, `created_at`, `updated_at`) VALUES
(1, 'Spot 1', 20.00, 1, NULL, '2025-05-21 20:59:04'),
(2, 'Spot 2', 20.00, 0, NULL, '2025-05-21 21:55:32'),
(3, 'Spot 3', 20.00, 0, NULL, '2025-05-21 16:22:37'),
(4, 'Spot 4', 20.00, 0, NULL, '2025-05-21 15:45:18'),
(5, 'Spot 5', 20.00, 0, NULL, '2025-05-21 20:41:53'),
(6, 'Spot 6', 20.00, 0, NULL, '2025-05-17 17:00:20'),
(7, 'Spot 7', 20.00, 0, NULL, '2025-05-18 09:09:42'),
(8, 'Spot 8', 20.00, 0, NULL, '2025-05-21 15:50:54'),
(9, 'Spot 9', 20.00, 0, NULL, '2025-05-21 15:50:51'),
(10, 'Spot 10', 20.00, 0, NULL, '2025-05-18 09:20:03'),
(11, 'Spot 11', 20.00, 0, NULL, NULL),
(12, 'Spot 12', 20.00, 0, NULL, NULL),
(13, 'Spot 13', 20.00, 0, NULL, '2025-05-21 20:10:24'),
(14, 'Spot 14', 20.00, 0, NULL, NULL),
(15, 'Spot 15', 20.00, 0, NULL, NULL),
(16, 'Spot 16', 20.00, 0, NULL, NULL),
(17, 'Spot 17', 20.00, 0, NULL, NULL),
(18, 'Spot 18', 20.00, 0, NULL, NULL),
(19, 'Spot 19', 20.00, 0, NULL, '2025-05-21 20:10:46'),
(20, 'Spot 20', 20.00, 0, NULL, NULL),
(21, 'Spot 21', 20.00, 0, NULL, NULL),
(22, 'Spot 22', 20.00, 0, NULL, NULL),
(23, 'Spot 23', 20.00, 0, NULL, NULL),
(24, 'Spot 24', 20.00, 0, NULL, '2025-05-21 19:32:24'),
(25, 'Spot 25', 20.00, 0, NULL, NULL),
(26, 'Spot 26', 20.00, 0, NULL, NULL),
(27, 'Spot 27', 20.00, 0, NULL, NULL),
(28, 'Spot 28', 20.00, 0, NULL, NULL),
(29, 'Spot 29', 20.00, 0, NULL, NULL),
(30, 'Spot 30', 20.00, 0, NULL, NULL),
(31, 'Spot 31', 20.00, 0, NULL, NULL),
(32, 'Spot 32', 20.00, 0, NULL, NULL),
(33, 'Spot 33', 20.00, 0, NULL, NULL),
(34, 'Spot 34', 20.00, 0, NULL, NULL),
(35, 'Spot 35', 20.00, 0, NULL, NULL),
(36, 'Spot 36', 20.00, 0, NULL, NULL),
(37, 'Spot 37', 20.00, 0, NULL, NULL),
(38, 'Spot 38', 20.00, 0, NULL, NULL),
(39, 'Spot 39', 20.00, 0, NULL, NULL),
(40, 'Spot 40', 20.00, 0, NULL, NULL),
(41, 'Spot 41', 20.00, 0, NULL, '2025-05-21 20:11:27'),
(42, 'Spot 42', 20.00, 0, NULL, NULL),
(43, 'Spot 43', 20.00, 0, NULL, NULL),
(44, 'Spot 44', 20.00, 0, NULL, NULL),
(45, 'Spot 45', 20.00, 0, NULL, NULL),
(46, 'Spot 46', 20.00, 0, NULL, NULL),
(47, 'Spot 47', 20.00, 0, NULL, NULL),
(48, 'Spot 48', 20.00, 0, NULL, NULL),
(49, 'Spot 49', 20.00, 0, NULL, '2025-05-21 21:03:58'),
(50, 'Spot 50', 20.00, 0, NULL, NULL),
(51, 'Spot 51', 20.00, 0, NULL, NULL),
(52, 'Spot 52', 20.00, 0, NULL, NULL),
(53, 'Spot 53', 20.00, 0, NULL, NULL),
(54, 'Spot 54', 20.00, 0, NULL, NULL),
(55, 'Spot 55', 20.00, 0, NULL, NULL),
(56, 'Spot 56', 20.00, 0, NULL, NULL),
(57, 'Spot 57', 20.00, 0, NULL, NULL),
(58, 'Spot 58', 20.00, 0, NULL, NULL),
(59, 'Spot 59', 20.00, 0, NULL, NULL),
(60, 'Spot 60', 20.00, 0, NULL, NULL),
(61, 'Spot 61', 20.00, 1, NULL, '2025-05-21 20:12:57'),
(62, 'Spot 62', 20.00, 0, NULL, NULL),
(63, 'Spot 63', 20.00, 0, NULL, NULL),
(64, 'Spot 64', 20.00, 0, NULL, NULL),
(65, 'Spot 65', 20.00, 0, NULL, NULL),
(66, 'Spot 66', 20.00, 0, NULL, NULL),
(67, 'Spot 67', 20.00, 0, NULL, NULL),
(68, 'Spot 68', 20.00, 0, NULL, NULL),
(69, 'Spot 69', 20.00, 0, NULL, NULL),
(70, 'Spot 70', 20.00, 0, NULL, NULL),
(71, 'Spot 71', 20.00, 0, NULL, NULL),
(72, 'Spot 72', 20.00, 0, NULL, NULL),
(73, 'Spot 73', 20.00, 0, NULL, NULL),
(74, 'Spot 74', 20.00, 0, NULL, NULL),
(75, 'Spot 75', 20.00, 0, NULL, NULL),
(76, 'Spot 76', 20.00, 0, NULL, NULL),
(77, 'Spot 77', 20.00, 0, NULL, NULL),
(78, 'Spot 78', 20.00, 0, NULL, NULL),
(79, 'Spot 79', 20.00, 0, NULL, NULL),
(80, 'Spot 80', 20.00, 0, NULL, NULL),
(81, 'Spot 81', 20.00, 0, NULL, NULL),
(82, 'Spot 82', 20.00, 0, NULL, NULL),
(83, 'Spot 83', 20.00, 0, NULL, NULL),
(84, 'Spot 84', 20.00, 0, NULL, NULL),
(85, 'Spot 85', 20.00, 0, NULL, NULL),
(86, 'Spot 86', 20.00, 0, NULL, NULL),
(87, 'Spot 87', 20.00, 0, NULL, NULL),
(88, 'Spot 88', 20.00, 0, NULL, NULL),
(89, 'Spot 89', 20.00, 0, NULL, NULL),
(90, 'Spot 90', 20.00, 0, NULL, NULL),
(91, 'Spot 91', 20.00, 0, NULL, NULL),
(92, 'Spot 92', 20.00, 0, NULL, NULL),
(93, 'Spot 93', 20.00, 0, NULL, NULL),
(94, 'Spot 94', 20.00, 0, NULL, '2025-05-21 19:50:01'),
(95, 'Spot 95', 20.00, 0, NULL, '2025-05-21 19:50:04'),
(96, 'Spot 96', 20.00, 1, NULL, '2025-05-21 19:48:22'),
(97, 'Spot 97', 20.00, 0, NULL, '2025-05-21 20:20:49'),
(98, 'Spot 98', 20.00, 0, NULL, '2025-05-21 19:56:53'),
(99, 'Spot 99', 20.00, 0, NULL, '2025-05-21 19:51:47'),
(100, 'Spot 100', 20.00, 0, NULL, '2025-05-21 19:16:54');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_proofs`
--

INSERT INTO `payment_proofs` (`id`, `user_id`, `image`, `status`, `amount`, `created_at`, `updated_at`) VALUES
(1, 26, 'payment_proofs/qCMylk126p84nONmIe1N4gotBMmfqxuinGPRvcoH.jpg', 'approved', 4000.00, '2025-03-18 17:14:16', '2025-03-18 17:40:54'),
(2, 26, 'payment_proofs/2HTUv1c6oxSnsoVUKQWuqOpIEaQg3WuuptRG5E0W.jpg', 'approved', 10000.00, '2025-03-18 17:26:33', '2025-03-19 05:05:04'),
(3, 26, 'payment_proofs/UDCn5Z9rhXBIGBO16dp7gO4HUMmREZOVQtPNQ0GM.jpg', 'approved', 3000.00, '2025-03-18 17:44:01', '2025-03-18 17:45:15'),
(4, 26, 'payment_proofs/ZZYjTsHgI2k7nfJsgkBl8QG8RG5XBkFAIy6tHd2n.png', 'approved', 40.00, '2025-03-19 04:03:15', '2025-03-19 04:04:34'),
(5, 24, 'payment_proofs/Y06rxAeF9rhDEE1ZNk00hrEuptcKj8m6sbsWp9u6.png', 'approved', 7000.00, '2025-03-19 04:08:04', '2025-03-26 15:57:24'),
(6, 26, 'payment_proofs/ES1KPARr7BgLdU2TjMZSTXFZTMf6TeXLR01DK0pp.png', 'approved', 3444.00, '2025-03-19 05:05:55', '2025-03-26 16:03:29'),
(7, 26, 'payment_proofs/5lAaMYOrjPRTuT4ifIwD5QUXue1YypgBplCuHHK7.png', 'approved', 500.00, '2025-03-25 06:46:50', '2025-03-26 16:20:13'),
(8, 26, 'payment_proofs/FZyDxMUg30V91oeaQLLisWG9jqUlWnd5NySRoWmg.png', 'approved', 6000.00, '2025-03-25 06:49:12', '2025-03-26 16:20:23'),
(9, 26, 'payment_proofs/LoShz8isilxYyDPIy3nEi7TxW0GiDQ2J7lBMFdH8.png', 'rejected', NULL, '2025-03-25 06:50:32', '2025-05-17 04:08:57'),
(10, 26, 'payment_proofs/6Eiu1hMa2dgDzsgLAxvgg6usSss9kgV7GWLgwUEZ.png', 'rejected', NULL, '2025-03-25 06:50:55', '2025-05-17 04:09:04'),
(15, 1, 'payment_proofs/mcbWSfyMDGvIdG2GA1qD7lCYHrF4jsPZ2gTMZPdP.jpg', 'approved', 566.00, '2025-05-17 04:31:00', '2025-05-17 04:55:39'),
(16, 1, 'payment_proofs/dLPIClvF1OvjnmMjWt5huF3uumaxw6GMgl1aU5KB.jpg', 'pending', NULL, '2025-05-17 05:10:12', '2025-05-17 05:10:12'),
(17, 1, 'payment_proofs/8KHILSqBC9cL7pu5cdHKZbtX7fEYOAMhwJXT9mXI.jpg', 'rejected', NULL, '2025-05-17 05:29:07', '2025-05-17 05:57:38'),
(18, 1, 'payment_proofs/5wZfda2T32hmGxZbrib2BYmgNccBCYyH5JnrR9ID.jpg', 'pending', NULL, '2025-05-17 05:42:49', '2025-05-17 05:42:49'),
(19, 1, 'payment_proofs/TAnNjdRcbQXnpj9spPLHUWqUW0cr02uLvbflaJQ2.jpg', 'pending', NULL, '2025-05-17 05:44:55', '2025-05-17 05:44:55'),
(20, 1, 'payment_proofs/N56KejR5IAxfcolzGNNukeLuuu3BUOBijvLbOmKT.png', 'approved', 404040.00, '2025-05-17 05:45:13', '2025-05-17 05:57:32'),
(21, 1, 'payment_proofs/YYcswwpxefCZuRPRYl1fbNOxJjVvG0pRzrT3776J.jpg', 'approved', 4000.00, '2025-05-17 05:56:50', '2025-05-17 05:57:24'),
(22, 1, 'payment_proofs/xc5XCGew36gU0VowHqT8fofbiKo5C7Ek1FYek9xJ.jpg', 'approved', 33.00, '2025-05-17 11:55:46', '2025-05-17 11:57:17'),
(23, 5, 'payment_proofs/DtOaFGQ7vp0LpZrGFT7WVDHYwgFs9q4K536wWjjD.png', 'pending', NULL, '2025-05-17 17:01:38', '2025-05-17 17:01:38'),
(24, 5, 'payment_proofs/iWZklaWKBHDy61m6xsKQoy2X7P0DmJ4kqsphXpIO.jpg', 'pending', NULL, '2025-05-17 17:04:16', '2025-05-17 17:04:16'),
(25, 5, 'payment_proofs/QDvBH027mdXmGjQSqDsHuBkbxaZW2b9C5AfCWxyz.jpg', 'approved', 5000.00, '2025-05-20 18:19:13', '2025-05-20 18:28:24'),
(26, 1, 'payment_proofs/sFTFET68flf6OOeRAvtLOy5blu7RJXABoUMosJJU.png', 'approved', 5656.00, '2025-05-21 21:11:27', '2025-05-21 21:16:30'),
(27, 2, 'payment_proofs/aWxjhlbzzDrRm1yyyM4YAA2sOy0fyZRdluPeToMM.png', 'rejected', NULL, '2025-05-21 21:15:29', '2025-05-21 21:16:24');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parking_spot_id` bigint(20) UNSIGNED NOT NULL,
  `reserved_at` timestamp NULL DEFAULT NULL,
  `parked_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `total_price` decimal(8,2) DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('free','active') NOT NULL DEFAULT 'free'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `parking_spot_id`, `reserved_at`, `parked_at`, `left_at`, `total_price`, `is_paid`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 1, '2025-05-17 06:18:35', '2025-05-17 06:24:31', '2025-05-17 06:42:07', 10.00, 1, '2025-05-17 06:18:35', '2025-05-17 06:42:16', 'free'),
(2, 1, 2, '2025-05-17 06:18:40', '2025-05-17 06:24:34', '2025-05-17 06:25:06', 0.00, 1, '2025-05-17 06:18:40', '2025-05-17 06:30:48', 'free'),
(3, 1, 3, '2025-05-17 06:18:43', '2025-05-17 06:24:40', '2025-05-17 06:42:24', 10.00, 1, '2025-05-17 06:18:43', '2025-05-17 06:42:27', 'free'),
(4, 1, 2, '2025-05-17 06:41:56', '2025-05-17 06:41:59', '2025-05-17 06:44:46', 0.00, 1, '2025-05-17 06:41:56', '2025-05-17 06:44:53', 'free'),
(5, 1, 1, '2025-05-17 06:44:27', '2025-05-17 06:44:31', '2025-05-17 06:45:00', 0.00, 1, '2025-05-17 06:44:27', '2025-05-17 06:45:33', 'free'),
(6, 1, 5, '2025-05-17 06:44:36', '2025-05-17 06:44:39', '2025-05-17 06:46:34', 0.00, 1, '2025-05-17 06:44:36', '2025-05-17 06:46:39', 'free'),
(7, 1, 1, '2025-05-17 06:45:39', '2025-05-17 06:46:22', '2025-05-17 06:49:01', 0.00, 1, '2025-05-17 06:45:39', '2025-05-17 06:49:04', 'free'),
(8, 1, 3, '2025-05-17 06:45:43', '2025-05-17 06:46:27', '2025-05-17 06:52:46', 0.00, 1, '2025-05-17 06:45:43', '2025-05-17 06:53:01', 'free'),
(9, 1, 2, '2025-05-17 06:45:46', '2025-05-17 06:46:25', '2025-05-17 06:49:58', 10.00, 1, '2025-05-17 06:45:46', '2025-05-17 06:50:04', 'free'),
(10, 1, 2, '2025-05-17 06:54:42', '2025-05-17 06:55:01', '2025-05-17 06:56:54', 9.80, 1, '2025-05-17 06:54:42', '2025-05-17 06:56:59', 'free'),
(11, 1, 3, '2025-05-17 06:54:46', '2025-05-17 06:55:03', '2025-05-17 11:44:41', 10.00, 1, '2025-05-17 06:54:46', '2025-05-17 11:44:45', 'free'),
(12, 1, 6, '2025-05-17 06:54:51', '2025-05-17 06:55:08', '2025-05-17 11:44:51', 10.00, 1, '2025-05-17 06:54:51', '2025-05-17 11:44:54', 'free'),
(13, 1, 5, '2025-05-17 06:54:55', '2025-05-17 06:55:11', '2025-05-17 07:59:46', 5.34, 1, '2025-05-17 06:54:55', '2025-05-17 07:59:50', 'free'),
(14, 1, 2, '2025-05-17 06:57:20', '2025-05-17 06:57:23', '2025-05-17 10:30:03', -34.61, 1, '2025-05-17 06:57:20', '2025-05-17 10:30:08', 'free'),
(15, 1, 4, '2025-05-17 08:00:04', '2025-05-17 08:00:07', '2025-05-17 11:44:17', 10.00, 1, '2025-05-17 08:00:04', '2025-05-17 11:44:24', 'free'),
(16, 1, 1, '2025-05-17 08:00:10', '2025-05-17 08:00:13', '2025-05-17 10:37:52', -39.61, 1, '2025-05-17 08:00:10', '2025-05-17 10:38:10', 'free'),
(35, 1, 1, '2025-05-17 11:44:36', '2025-05-17 11:44:38', '2025-05-17 11:52:17', 0.00, 1, '2025-05-17 11:44:36', '2025-05-17 11:52:20', 'free'),
(36, 1, 3, '2025-05-17 11:44:59', '2025-05-17 11:45:02', '2025-05-18 09:11:00', 171.03, 1, '2025-05-17 11:44:59', '2025-05-18 09:11:06', 'free'),
(37, 1, 5, '2025-05-17 11:45:05', '2025-05-17 11:45:09', '2025-05-18 09:12:11', 68.48, 1, '2025-05-17 11:45:05', '2025-05-18 09:12:15', 'free'),
(38, 1, 4, '2025-05-17 11:45:14', '2025-05-17 11:45:18', '2025-05-18 09:11:36', 68.29, 1, '2025-05-17 11:45:14', '2025-05-18 09:11:43', 'free'),
(39, 1, 1, '2025-05-17 11:52:28', '2025-05-17 11:52:32', '2025-05-17 12:11:17', 10.00, 1, '2025-05-17 11:52:28', '2025-05-17 12:11:26', 'free'),
(40, 1, 1, '2025-05-17 12:11:37', '2025-05-17 12:11:41', '2025-05-18 08:15:47', 10.00, 1, '2025-05-17 12:11:37', '2025-05-18 08:15:55', 'free'),
(41, 5, 6, '2025-05-17 17:00:20', '2025-05-17 17:00:30', '2025-05-20 18:43:27', 2208.49, 0, '2025-05-17 17:00:20', '2025-05-20 18:43:27', 'free'),
(45, 1, 2, '2025-05-17 17:23:31', '2025-05-17 17:23:37', '2025-05-18 09:10:40', 10.06, 1, '2025-05-17 17:23:31', '2025-05-18 09:10:46', 'free'),
(47, 1, 1, '2025-05-18 08:25:40', '2025-05-18 08:25:47', '2025-05-18 09:10:20', 0.00, 1, '2025-05-18 08:25:40', '2025-05-18 09:10:26', 'free'),
(48, 1, 9, '2025-05-18 08:32:01', '2025-05-18 08:32:09', '2025-05-18 09:51:17', 27.45, 1, '2025-05-18 08:32:01', '2025-05-18 09:51:24', 'free'),
(51, 1, 2, '2025-05-18 09:12:30', '2025-05-18 09:12:40', '2025-05-18 09:14:32', 0.00, 1, '2025-05-18 09:12:30', '2025-05-18 09:14:37', 'free'),
(52, 1, 3, '2025-05-18 09:12:37', '2025-05-18 09:12:57', '2025-05-18 09:16:08', 0.00, 1, '2025-05-18 09:12:37', '2025-05-18 09:16:12', 'free'),
(53, 1, 5, '2025-05-18 09:14:53', '2025-05-18 09:15:25', '2025-05-18 09:24:09', 0.00, 1, '2025-05-18 09:14:53', '2025-05-18 09:24:13', 'free'),
(55, 1, 1, '2025-05-18 09:24:29', '2025-05-18 09:24:35', '2025-05-21 07:50:38', 2122.66, 1, '2025-05-18 09:24:29', '2025-05-21 07:50:59', 'free'),
(56, 1, 2, '2025-05-18 09:24:41', '2025-05-18 09:24:44', '2025-05-21 15:13:31', 2344.01, 1, '2025-05-18 09:24:41', '2025-05-21 15:13:38', 'free'),
(58, 1, 3, '2025-05-18 09:51:03', '2025-05-18 09:51:08', '2025-05-20 18:07:14', 1697.59, 1, '2025-05-18 09:51:03', '2025-05-20 18:07:21', 'free'),
(59, 1, 5, '2025-05-18 09:51:43', '2025-05-18 09:51:49', '2025-05-20 18:06:28', 51.78, 1, '2025-05-18 09:51:43', '2025-05-20 18:06:34', 'free'),
(61, 1, 4, '2025-05-21 06:10:53', '2025-05-21 06:10:57', '2025-05-21 06:11:04', 0.00, 1, '2025-05-21 06:10:53', '2025-05-21 06:11:08', 'free'),
(63, 1, 4, '2025-05-21 07:07:52', '2025-05-21 07:07:58', '2025-05-21 15:43:14', 30.88, 1, '2025-05-21 07:07:52', '2025-05-21 15:43:18', 'free'),
(65, 1, 1, '2025-05-21 15:14:00', '2025-05-21 15:14:11', '2025-05-21 15:17:39', 10.08, 1, '2025-05-21 15:14:00', '2025-05-21 15:17:44', 'free'),
(66, 1, 2, '2025-05-21 15:17:25', '2025-05-21 15:17:33', '2025-05-21 15:31:43', 10.17, 1, '2025-05-21 15:17:25', '2025-05-21 15:31:46', 'free'),
(67, 1, 1, '2025-05-21 15:19:55', '2025-05-21 15:19:59', '2025-05-21 15:43:24', 15.73, 1, '2025-05-21 15:19:55', '2025-05-21 15:43:29', 'free'),
(68, 1, 3, '2025-05-21 15:20:14', '2025-05-21 15:20:18', '2025-05-21 15:20:49', 10.03, 1, '2025-05-21 15:20:14', '2025-05-21 15:20:52', 'free'),
(80, 1, 1, '2025-05-21 15:53:12', '2025-05-21 15:53:14', '2025-05-21 16:07:53', 17.33, 1, '2025-05-21 15:53:12', '2025-05-21 16:07:56', 'free'),
(81, 1, 1, '2025-05-21 16:08:03', '2025-05-21 16:08:07', '2025-05-21 16:12:33', 11.06, 1, '2025-05-21 16:08:03', '2025-05-21 16:12:35', 'free'),
(82, 1, 1, '2025-05-21 16:12:42', '2025-05-21 16:12:46', '2025-05-21 16:19:18', 13.27, 1, '2025-05-21 16:12:42', '2025-05-21 16:19:21', 'free'),
(83, 1, 1, '2025-05-21 16:19:30', '2025-05-21 16:19:33', '2025-05-21 16:24:04', 12.26, 1, '2025-05-21 16:19:30', '2025-05-21 16:24:07', 'free'),
(86, 1, 1, '2025-05-21 16:36:09', '2025-05-21 16:36:17', '2025-05-21 16:36:44', 10.23, 1, '2025-05-21 16:36:09', '2025-05-21 16:36:49', 'free'),
(87, 1, 2, '2025-05-21 16:36:38', '2025-05-21 16:36:42', '2025-05-21 16:44:12', 13.75, 1, '2025-05-21 16:36:38', '2025-05-21 16:44:16', 'free'),
(89, 1, 1, '2025-05-21 16:38:11', '2025-05-21 16:38:15', '2025-05-21 16:38:36', 10.18, 1, '2025-05-21 16:38:11', '2025-05-21 16:38:40', 'free'),
(90, 1, 2, '2025-05-21 16:44:23', '2025-05-21 16:44:41', '2025-05-21 16:51:46', 13.55, 1, '2025-05-21 16:44:23', '2025-05-21 16:51:51', 'free'),
(91, 1, 1, '2025-05-21 16:51:58', '2025-05-21 16:52:02', '2025-05-21 17:20:01', 24.00, 0, '2025-05-21 16:51:58', '2025-05-21 17:20:01', 'free'),
(93, 1, 94, '2025-05-21 19:15:51', '2025-05-21 19:16:04', '2025-05-21 19:16:12', 10.07, 1, '2025-05-21 19:15:51', '2025-05-21 19:16:16', 'free'),
(101, 1, 97, '2025-05-21 19:49:56', '2025-05-21 19:50:20', '2025-05-21 19:50:37', 10.14, 1, '2025-05-21 19:49:56', '2025-05-21 19:50:40', 'free'),
(104, 1, 98, '2025-05-21 19:51:17', '2025-05-21 19:51:44', '2025-05-21 19:56:50', 12.55, 1, '2025-05-21 19:51:17', '2025-05-21 19:56:53', 'free'),
(113, 1, 2, '2025-05-21 20:21:06', '2025-05-21 20:21:40', '2025-05-21 20:21:53', 10.11, 1, '2025-05-21 20:21:06', '2025-05-21 20:21:56', 'free'),
(114, 1, 1, '2025-05-21 20:23:42', '2025-05-21 20:24:20', '2025-05-21 20:24:28', 10.07, 1, '2025-05-21 20:23:42', '2025-05-21 20:24:31', 'free'),
(120, 1, 1, '2025-05-21 20:36:23', '2025-05-21 20:36:27', '2025-05-21 20:36:31', 10.03, 1, '2025-05-21 20:36:23', '2025-05-21 20:36:34', 'free'),
(123, 1, 1, '2025-05-21 20:40:03', '2025-05-21 20:40:06', '2025-05-21 20:40:32', 10.22, 1, '2025-05-21 20:40:03', '2025-05-21 20:40:38', 'free'),
(125, 1, 49, '2025-05-21 20:42:10', '2025-05-21 20:42:19', '2025-05-21 21:01:50', 19.77, 1, '2025-05-21 20:42:10', '2025-05-21 21:03:58', 'free'),
(126, 7, 1, '2025-05-21 20:59:03', '2025-05-21 20:59:11', '2025-05-21 20:59:24', 10.11, 0, '2025-05-21 20:59:03', '2025-05-21 20:59:24', 'free'),
(128, 1, 2, '2025-05-21 21:07:54', '2025-05-21 21:08:00', '2025-05-21 21:08:05', 10.05, 1, '2025-05-21 21:07:54', '2025-05-21 21:08:20', 'free'),
(130, 1, 2, '2025-05-21 21:54:58', '2025-05-21 21:55:18', '2025-05-21 21:55:27', 10.08, 1, '2025-05-21 21:54:58', '2025-05-21 21:55:32', 'free');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('E8QwtXodqvFTOG26FpJoSDQV1N5VX1uzEQsqAIDv', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRTVyUWY1VWRqOGN1RUZHSDNSZUJ1U2JDMlE3NXkyd0JIQVNHa3FFayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jbGllbnQvcGFya2luZy9tYW5hZ2UiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1747875337);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 1,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `balance`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `verification_code`, `is_verified`) VALUES
(1, 'kidus ', 'kidusbeckham@gmail.com', 1, 407411.09, NULL, '$2y$12$BJDX7nNPeHM1kvhbvcQfQOaX2uwJdW1swxrPfNWX2I3jitAjl9nNW', 'g7PG2vp1pzB2PFlpgpmT7jWADNZqwHytCRTpowRPVIW93sGoGAJ6wOEaRB7U', '2025-05-17 04:28:24', '2025-05-21 21:55:32', NULL, 1),
(2, 'admin', 'kidusadmin@gmail.com', 3, 0.00, NULL, '$2y$12$KYroF3wltLwyZxJOH6ivsedWmD2xaNadjejWdxOkZ2o/tXvB6JQTO', NULL, '2025-05-17 04:33:07', '2025-05-17 04:33:07', NULL, 1),
(3, 'Test User', 'test@example.com', 1, 0.00, '2025-05-17 06:16:10', '$2y$12$2m/PqbKeOilX0bhj.nsRc.RoLFu5jpNDHZMeQhDyuEDSWr6OSTMCG', '25xyXCMNv4', '2025-05-17 06:16:10', '2025-05-17 06:16:10', NULL, 1),
(5, 'budydy', 'buddykk07@gmail.com', 1, 5000.00, NULL, '$2y$12$0ZwpjHYCr0L.tNteysY/O.TAVx5oC1AV/bXNSaJpFUgXheaIwmMJ.', 'ubF0gLqUIGbFVOA3u1qsapDUa5DY7cdfLO5oihGIhth7ryFqLaALWRjZt7G4', '2025-05-17 16:59:22', '2025-05-21 10:29:16', NULL, 1),
(6, 'Kidus Adugna', 'kidusrash@gmail.com', 1, 0.00, NULL, '$2y$12$gY.SGTGhlY0ldK4YSnAwB.2pligU0gk8h96.v.Q0YMhYztxxuMZ76', 'xbk56kwcybtSV8xkJa8SjNYyXeHra1TwkYOX2Cl8VxcQL6bOzeOMaIqgClwf', '2025-05-21 10:37:37', '2025-05-21 10:37:37', NULL, 1),
(7, 'Elias Desalegn', 'kidusmessi25@example.com', 1, 0.00, NULL, '$2y$12$9RmwGCdOKoSo.Ubf.oWS8.DHSUR.tB/xc.6nVYbqtUAHci59j13I2', NULL, '2025-05-21 20:56:00', '2025-05-21 20:56:00', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `created_at`, `updated_at`) VALUES
(1, 1, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25'),
(2, 4, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25'),
(3, 6, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25'),
(4, 24, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25'),
(5, 26, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25'),
(6, 27, 0.00, '2025-04-30 07:11:25', '2025-04-30 07:11:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_verifications_email_unique` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `parking_spots`
--
ALTER TABLE `parking_spots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_proofs_user_id_foreign` (`user_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipts_user_id_foreign` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_user_id_foreign` (`user_id`),
  ADD KEY `reservations_parking_spot_id_foreign` (`parking_spot_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `parking_spots`
--
ALTER TABLE `parking_spots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_parking_spot_id_foreign` FOREIGN KEY (`parking_spot_id`) REFERENCES `parking_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
