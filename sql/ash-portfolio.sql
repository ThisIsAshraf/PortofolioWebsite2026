-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 12:38 PM
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
-- Database: `ash-portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'SystemAdmin', '$2y$10$I4M.x5S2a4G3OkyEi7434unQBnolWm2gQkTqifT8yE7axH3/GU1S6', '2026-11-08 10:21:12');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(254) NOT NULL,
  `subject` varchar(180) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(1, 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', 'sdsdss', 'ssssssssssssssss', '2026-11-08 11:34:54', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(10) UNSIGNED NOT NULL,
  `degree` varchar(220) NOT NULL,
  `institution` varchar(220) NOT NULL,
  `period` varchar(100) NOT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `degree`, `institution`, `period`, `detail`, `sort_order`, `is_visible`, `created_at`) VALUES
(1, 'Bachelor of Science in Information Technology', 'Limkokwing University of Creative Technology', '2019 – 2021', 'Cyberjaya, Selangor, Malaysia · CGPA: 3.91 / 4.00', 1, 1, '2026-11-08 10:20:04');

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` varchar(180) NOT NULL,
  `company` varchar(180) NOT NULL,
  `period` varchar(100) NOT NULL,
  `location` varchar(180) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experience`
--

INSERT INTO `experience` (`id`, `role`, `company`, `period`, `location`, `sort_order`, `is_visible`, `created_at`) VALUES
(1, 'Senior Desktop Support Engineer', 'Noor Staffing Group', 'Mar 2026 – Present', 'Bangladesh', 1, 1, '2026-11-08 11:14:21'),
(2, 'Desktop Support Specialist', 'Noor Staffing Group', 'Oct 2024 – Feb 2026', 'Bangladesh', 2, 1, '2026-11-08 11:14:21'),
(3, 'Technical Support Advisor', 'Amber Software Solutions', 'Feb 2023 – Sep 2024', 'Remote / UK customers', 3, 1, '2026-11-08 11:14:21'),
(4, 'IT Support Engineer', 'Flyte Solutions Ltd', 'Nov 2021 – Jan 2023', 'Bangladesh', 4, 1, '2026-11-08 11:14:21'),
(5, 'Junior Back-End Developer Intern', 'JomRun Sdn Bhd', 'Oct 2021 – Dec 2021', 'Malaysia', 5, 1, '2026-11-08 11:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `experience_responsibilities`
--

CREATE TABLE `experience_responsibilities` (
  `id` int(10) UNSIGNED NOT NULL,
  `experience_id` int(10) UNSIGNED NOT NULL,
  `responsibility` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experience_responsibilities`
--

INSERT INTO `experience_responsibilities` (`id`, `experience_id`, `responsibility`, `sort_order`) VALUES
(1, 1, 'Provide L1/L2 support for end-user devices, software, peripherals, connectivity, and business applications.', 1),
(2, 1, 'Manage incidents and service requests through Zendesk.', 2),
(3, 1, 'Administer Google Workspace accounts, groups, permissions, mailboxes, and license allocation.', 3),
(4, 1, 'Manage RingCentral users and support LinkedIn and Indeed user administration.', 4),
(5, 1, 'Coordinate onboarding/offboarding, IT assets, purchasing, quotations, and vendor follow-up.', 5),
(6, 1, 'Supported the successful Bangladesh office network migration from Cat5 to Cat6.', 6),
(7, 2, 'Delivered desktop and end-user support for hardware, software, printers, applications, and connectivity.', 1),
(8, 2, 'Managed incidents and service requests using Jira Service Management.', 2),
(9, 2, 'Supported Google Workspace administration, onboarding, account setup, and access management.', 3),
(10, 2, 'Maintained IT asset records and supported workstation deployment.', 4),
(11, 3, 'Supported customers by phone, email, and remote sessions.', 1),
(12, 3, 'Troubleshot technical, application, hardware, software, and connectivity issues.', 2),
(13, 3, 'Assisted with Outlook configuration and email maintenance.', 3),
(14, 3, 'Contributed web portal updates using HTML, CSS, JavaScript, and core PHP.', 4),
(15, 4, 'Supported desktops, laptops, operating systems, printers, peripherals, email, and network connectivity.', 1),
(16, 4, 'Assisted with user accounts, shared resources, workstation deployment, upgrades, and maintenance.', 2),
(17, 4, 'Documented common support procedures and helped users with workplace technology.', 3),
(18, 5, 'Completed a short back-end development internship and gained exposure to software development workflows.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) NOT NULL,
  `project_type` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `project_type`, `description`, `sort_order`, `is_visible`, `created_at`) VALUES
(1, 'Office Network Migration', 'Infrastructure · Networking', 'Supported the Bangladesh office migration from Cat5 to Cat6 cabling, coordinating implementation activities and helping deliver a successful upgrade.', 1, 1, '2026-11-08 11:14:21'),
(2, 'Google Workspace Administration', 'SaaS · IT Operations', 'Manage mailbox and account requirements, groups, permissions, and license allocation with attention to access needs and cost-conscious administration.', 2, 1, '2026-11-08 11:14:21'),
(3, 'Service Desk & Asset Operations', 'Support · Operations', 'Support ticket workflows, onboarding/offboarding, equipment allocation, inventory records, purchasing, and vendor follow-up.', 3, 1, '2026-11-08 11:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `project_tags`
--

CREATE TABLE `project_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `tag_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_tags`
--

INSERT INTO `project_tags` (`id`, `project_id`, `tag_name`) VALUES
(1, 1, 'Cat6 Cabling'),
(2, 1, 'Coordination'),
(3, 1, 'Office Infrastructure'),
(4, 2, 'Google Workspace'),
(5, 2, 'Licensing'),
(6, 2, 'Access Management'),
(7, 3, 'Zendesk'),
(8, 3, 'IT Assets'),
(9, 3, 'Procurement');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('email', 'meet.ashraf.uk@gmail.com'),
('headline', 'IT support, systems administration, and reliable technology experiences.'),
('intro', 'I help people and teams work confidently with technology—from end-user support and SaaS administration to IT assets, vendor coordination, and office infrastructure.'),
('linkedin', 'https://www.linkedin.com/in/ashraful98/'),
('location', 'Dhaka, Bangladesh'),
('name', 'Shihab'),
('phone', '01982401992'),
('photo', 'profile_1e9051013ce29b6d.jpg'),
('role', 'Senior Desktop Support Engineer');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `level` varchar(120) NOT NULL DEFAULT 'Core',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `group_id`, `name`, `level`, `sort_order`, `is_visible`, `created_at`) VALUES
(1, 1, 'L1/L2 Technical Support', 'Core', 1, 1, '2026-11-08 11:14:21'),
(2, 1, 'Service Desk & Ticketing', 'Core', 2, 1, '2026-11-08 11:14:21'),
(3, 1, 'Hardware & Software Troubleshooting', 'Core', 3, 1, '2026-11-08 11:14:21'),
(4, 2, 'Google Workspace Administration', 'Core', 4, 1, '2026-11-08 11:14:21'),
(5, 2, 'Mailbox & License Management', 'Core', 5, 1, '2026-11-08 11:14:21'),
(6, 3, 'IT Asset Management', 'Core', 6, 1, '2026-11-08 11:14:21'),
(7, 3, 'IT Procurement & Vendor Coordination', 'Core', 7, 1, '2026-11-08 11:14:21'),
(8, 4, 'TCP/IP, DNS & LAN/WAN', 'Working knowledge', 8, 1, '2026-11-08 11:14:21'),
(9, 5, 'Microsoft 365 & Entra ID', 'Currently developing', 9, 1, '2026-11-08 11:14:21'),
(10, 5, 'Microsoft Azure (AZ-104 path)', 'Currently developing', 10, 1, '2026-11-08 11:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `skill_groups`
--

CREATE TABLE `skill_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_groups`
--

INSERT INTO `skill_groups` (`id`, `name`, `sort_order`) VALUES
(1, 'Support', 1),
(2, 'Administration', 2),
(3, 'Operations', 3),
(4, 'Networking', 4),
(5, 'Learning', 5);

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `host` varchar(191) NOT NULL DEFAULT 'smtp.gmail.com',
  `port` int(10) UNSIGNED NOT NULL DEFAULT 587,
  `encryption` enum('tls','ssl','none') NOT NULL DEFAULT 'tls',
  `username` varchar(191) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `from_email` varchar(191) NOT NULL DEFAULT '',
  `from_name` varchar(191) NOT NULL DEFAULT 'Portfolio Admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `host`, `port`, `encryption`, `username`, `password`, `from_email`, `from_name`, `is_active`, `updated_at`) VALUES
(1, 'smtp.gmail.com', 587, 'tls', 'alam.office.my@gmail.com', 'cbkk hghv ybom dooq', 'alam.office.my@gmail.com', 'Ashraful Portfolio', 1, '2026-11-08 11:26:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_created_at` (`created_at`),
  ADD KEY `idx_contact_status` (`status`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience_responsibilities`
--
ALTER TABLE `experience_responsibilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experience_id` (`experience_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_tags`
--
ALTER TABLE `project_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `skill_groups`
--
ALTER TABLE `skill_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `experience_responsibilities`
--
ALTER TABLE `experience_responsibilities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_tags`
--
ALTER TABLE `project_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `skill_groups`
--
ALTER TABLE `skill_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `experience_responsibilities`
--
ALTER TABLE `experience_responsibilities`
  ADD CONSTRAINT `experience_responsibilities_ibfk_1` FOREIGN KEY (`experience_id`) REFERENCES `experience` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_tags`
--
ALTER TABLE `project_tags`
  ADD CONSTRAINT `project_tags_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `skill_groups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
