-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 02:54 PM
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
-- Table structure for table `click_logs`
--

CREATE TABLE `click_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `element_id` varchar(100) DEFAULT NULL,
  `element_text` varchar(150) DEFAULT NULL,
  `target_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Ashraful Alam Shihab', 'alamshihab526@gmail.com', 'System Mail', 'bkllhbkllbnlllllllll', '2026-11-08 12:44:30', 'read');

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
(1, 'Bachelor of Science in Information Technology', 'Limkokwing University of Creative Technology', 'Feb 2019 – Dec 2021', 'Cyberjaya, Selangor, Malaysia ·', 0, 1, '2026-11-08 12:26:49');

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
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

INSERT INTO `experience` (`id`, `parent_id`, `role`, `company`, `period`, `location`, `sort_order`, `is_visible`, `created_at`) VALUES
(1, NULL, 'Desktop Support Specialist', 'Noor Staffing Group', 'Oct 2024 – Feb 2026', 'Bangladesh', 2, 1, '2026-11-08 12:08:47'),
(2, NULL, 'Senior Desktop Support Engineer', 'Noor Staffing Group', 'Mar 2026 – Present', 'Bangladesh', 1, 1, '2026-11-08 12:10:10'),
(3, NULL, 'Technical Support Advisor', 'Amber Software Solutions', 'Feb 2023 – Sep 2024', 'Dhaka, Bangladesh', 3, 1, '2026-11-08 12:22:39');

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
(71, 1, 'Delivered desktop and end-user support for hardware, software, printers, applications, and connectivity.', 1),
(72, 1, 'Managed incidents and service requests using Jira Service Management.', 2),
(73, 1, 'Supported Google Workspace administration, onboarding, account setup, and access management.', 3),
(74, 1, 'Maintained IT asset records and supported workstation deployment.', 4),
(75, 2, 'Provide L1/L2 support for end-user devices, software, peripherals, connectivity, and business applications.', 1),
(76, 2, 'Manage incidents and service requests through Zendesk.', 2),
(77, 2, 'Administer Google Workspace accounts, groups, permissions, mailboxes, and license allocation.', 3),
(78, 2, 'Manage RingCentral users and support LinkedIn and Indeed user administration.', 4),
(79, 2, 'Coordinate onboarding/offboarding, IT assets, purchasing, quotations, and vendor follow-up.', 5),
(80, 2, 'Supported the successful Bangladesh office network migration from Cat5 to Cat6.', 6),
(85, 3, 'Supported customers by phone, email, and remote sessions.', 1),
(86, 3, 'Troubleshot technical, application, hardware, software, and connectivity issues.', 2),
(87, 3, 'Assisted with Outlook configuration and email maintenance.', 3),
(88, 3, 'Contributed web portal updates using HTML, CSS, JavaScript, and core PHP.', 4);

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
(1, 'Office Network Migration', 'Infrastructure · Networking', 'Supported the Bangladesh office migration from Cat5 to Cat6 cabling, coordinating implementation activities and helping deliver a successful upgrade.', 0, 1, '2026-11-08 12:25:35');

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
(3, 1, 'Office Infrastructure');

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
('availability_status', 'IT SUPPORT · OPERATIONS · INFRASTRUCTURE'),
('edu_learning_text', 'Focused on Microsoft 365, Entra ID, Azure administration, and lab-based deployments.'),
('edu_learning_title', 'Building toward cloud & enterprise infrastructure'),
('email', 'meet.ashraf.uk@gmail.com'),
('github', 'https://github.com/ashraful98'),
('headline', 'IT support, systems administration, and reliable technology experiences.'),
('intro', 'I help people and teams work confidently with technology—from end-user support and SaaS administration to IT assets, vendor coordination, and office infrastructure.'),
('learning_text', ''),
('learning_title', ''),
('linkedin', 'https://www.linkedin.com/in/ashraful98/'),
('location', 'Dhaka, Bangladesh'),
('name', 'Ashraful Alam Shihab'),
('phone', '01982401992'),
('photo', 'profile_fe1567c7ace17621.png'),
('role', 'Senior Desktop Support Engineer'),
('sec_about_accent', 'work for people.'),
('sec_about_kicker', '01 / ABOUT'),
('sec_about_title', 'Making technology'),
('sec_contact_accent', 'Let’s talk.'),
('sec_contact_intro', 'For IT support opportunities, collaboration, or a professional conversation, send me a message.'),
('sec_contact_kicker', '06 / GET IN TOUCH'),
('sec_contact_title', 'Have a challenge?'),
('sec_edu_accent', 'strong foundation.'),
('sec_edu_kicker', '05 / EDUCATION'),
('sec_edu_title', 'Built on a'),
('sec_exp_accent', 'experience.'),
('sec_exp_intro', 'The roles, responsibilities, and practical experience that have shaped my approach to IT.'),
('sec_exp_kicker', '03 / CAREER'),
('sec_exp_title', 'Work'),
('sec_proj_accent', 'matters.'),
('sec_proj_intro', 'Experience highlights and initiatives.'),
('sec_proj_kicker', '04 / SELECTED WORK'),
('sec_proj_title', 'Work that'),
('sec_skills_accent', 'strengths.'),
('sec_skills_intro', 'A practical mix of user support, SaaS administration, IT operations, and developing infrastructure skills.'),
('sec_skills_kicker', '02 / TOOLKIT'),
('sec_skills_title', 'Skills &');

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
(1, 1, 'L1/L2 IT & Desktop Support', 'Core', 0, 1, '2026-11-08 11:43:16');

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
(1, 'Support', 0);

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

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `device_type` enum('Desktop','Mobile','Tablet','Bot') NOT NULL DEFAULT 'Desktop',
  `browser` varchar(50) NOT NULL DEFAULT 'Other',
  `platform` varchar(50) NOT NULL DEFAULT 'Other',
  `country` varchar(100) DEFAULT 'Localhost / Unknown',
  `city` varchar(100) DEFAULT 'Unknown',
  `page_url` varchar(255) NOT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`id`, `session_id`, `ip_address`, `user_agent`, `device_type`, `browser`, `platform`, `country`, `city`, `page_url`, `referrer`, `created_at`, `last_activity`) VALUES
(1, '03vn4ifk2rv7c9c503gg0igtsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'Desktop', 'Chrome', 'Windows', 'Localhost / Intranet', 'Internal', '/portfolio/', NULL, '2026-11-08 13:38:52', '2026-11-08 13:38:52'),
(2, '03vn4ifk2rv7c9c503gg0igtsd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'Desktop', 'Chrome', 'Windows', 'Localhost / Intranet', 'Internal', '/portfolio/', NULL, '2026-11-08 13:51:56', '2026-11-08 13:51:56');

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
-- Indexes for table `click_logs`
--
ALTER TABLE `click_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_click_created` (`created_at`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_experience_parent` (`parent_id`);

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
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `click_logs`
--
ALTER TABLE `click_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `experience_responsibilities`
--
ALTER TABLE `experience_responsibilities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_tags`
--
ALTER TABLE `project_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skill_groups`
--
ALTER TABLE `skill_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `fk_experience_parent` FOREIGN KEY (`parent_id`) REFERENCES `experience` (`id`) ON DELETE CASCADE;

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
