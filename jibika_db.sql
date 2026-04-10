-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 07:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jibika_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `user_id`, `status`, `applied_at`) VALUES
(1, 1, 1, 'Rejected', '2026-04-05 19:56:58'),
(2, 2, 1, 'Accepted', '2026-04-05 21:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_id`, `district_name`) VALUES
(5, 'Barishal'),
(2, 'Chattogram'),
(1, 'Dhaka'),
(3, 'Khulna'),
(8, 'Mymensingh'),
(4, 'Rajshahi'),
(7, 'Rangpur'),
(6, 'Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `employment_status`
--

CREATE TABLE `employment_status` (
  `status_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_status` enum('unemployed','employed','training','self_employed') NOT NULL DEFAULT 'unemployed',
  `remarks` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employment_status`
--

INSERT INTO `employment_status` (`status_id`, `user_id`, `current_status`, `remarks`, `updated_at`) VALUES
(1, 1, 'training', 'Under Tranning', '2026-04-05 22:58:56');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `district_id` int(11) DEFAULT NULL,
  `upazila_id` int(11) DEFAULT NULL,
  `ward_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `employer_id`, `title`, `description`, `location`, `salary`, `created_at`, `district_id`, `upazila_id`, `ward_id`) VALUES
(1, 3, 'Web Developer', 'Need a junior web developer with HTML, CSS skills', 'Dhaka', '15000', '2026-04-05 19:49:21', NULL, NULL, NULL),
(2, 3, 'Plumber', 'Plumber Need Emergency', 'bogura', '158000', '2026-04-05 21:49:57', 5, 9, 10);

-- --------------------------------------------------------

--
-- Table structure for table `job_seeker_profiles`
--

CREATE TABLE `job_seeker_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nid` varchar(30) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `upazila` varchar(100) DEFAULT NULL,
  `education` varchar(150) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `about` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `district_id` int(11) DEFAULT NULL,
  `upazila_id` int(11) DEFAULT NULL,
  `ward_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_seeker_profiles`
--

INSERT INTO `job_seeker_profiles` (`profile_id`, `user_id`, `nid`, `district`, `upazila`, `education`, `skills`, `about`, `created_at`, `district_id`, `upazila_id`, `ward_id`) VALUES
(1, 1, '0102992193', 'Bogura', 'Shajahanpur', 'BSCSE', 'Nai', 'Ki r boli', '2026-04-05 19:21:48', 4, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `skill_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `user_id`, `skill_name`, `created_at`) VALUES
(1, 1, 'HTML', '2026-04-05 19:40:33'),
(3, 1, 'DRIVING', '2026-04-05 19:40:50'),
(4, 1, 'CSS', '2026-04-05 21:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `upazilas`
--

CREATE TABLE `upazilas` (
  `upazila_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `upazila_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upazilas`
--

INSERT INTO `upazilas` (`upazila_id`, `district_id`, `upazila_name`) VALUES
(1, 1, 'Dhamrai'),
(2, 1, 'Savar'),
(3, 1, 'Keraniganj'),
(4, 2, 'Patiya'),
(5, 2, 'Raozan'),
(6, 3, 'Batiaghata'),
(7, 4, 'Paba'),
(8, 5, 'Babuganj'),
(9, 6, 'Beanibazar'),
(10, 7, 'Pirgacha'),
(11, 8, 'Trishal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('job_seeker','employer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Test User', 'testuser@gmail.com', '01728248170', '$2y$10$9CaL/CHFCLXATE5Wzy3lY.M4eer3rEU9vF/uQxT/wRyu3NsVkiObK', 'job_seeker', '2026-04-05 19:01:07'),
(3, 'Employee1', 'employee1@gmail.com', '01728248170', '$2y$10$0.HS4k1/sU8RLGiRdqAysuusm7xWQQg1taZrQaGYbyxw8o8efrZg6', 'employer', '2026-04-05 19:46:36'),
(7, 'SHARIF AHMED', 'sharifahmedadmin@gmail.com', '01728248170', '$2y$10$2adsdIGY9TuckJ6uN5CYpeu1Yz4rACFeEyVXa9hx1enq/YnZqKcbC', 'employer', '2026-04-05 20:50:36'),
(8, 'SHARIF AHMED', 'sharifahmed@gmail.com', '01728248170', '$2y$10$nAhI5Z.imdlqffB8nKw3Nu4sGzMVbAB7/NTfiIW2dRN3l5.MbBD..', 'admin', '2026-04-05 21:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `wards`
--

CREATE TABLE `wards` (
  `ward_id` int(11) NOT NULL,
  `upazila_id` int(11) NOT NULL,
  `ward_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wards`
--

INSERT INTO `wards` (`ward_id`, `upazila_id`, `ward_name`) VALUES
(1, 1, 'Ward 1'),
(2, 1, 'Ward 2'),
(3, 2, 'Ward 1'),
(4, 2, 'Ward 2'),
(5, 3, 'Ward 1'),
(6, 4, 'Ward 1'),
(7, 5, 'Ward 1'),
(8, 6, 'Ward 1'),
(9, 7, 'Ward 1'),
(10, 8, 'Ward 1'),
(11, 9, 'Ward 1'),
(12, 10, 'Ward 1'),
(13, 11, 'Ward 1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`district_id`),
  ADD UNIQUE KEY `district_name` (`district_name`);

--
-- Indexes for table `employment_status`
--
ALTER TABLE `employment_status`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `fk_jobs_district` (`district_id`),
  ADD KEY `fk_jobs_upazila` (`upazila_id`),
  ADD KEY `fk_jobs_ward` (`ward_id`);

--
-- Indexes for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_profile_district` (`district_id`),
  ADD KEY `fk_profile_upazila` (`upazila_id`),
  ADD KEY `fk_profile_ward` (`ward_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `upazilas`
--
ALTER TABLE `upazilas`
  ADD PRIMARY KEY (`upazila_id`),
  ADD KEY `district_id` (`district_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wards`
--
ALTER TABLE `wards`
  ADD PRIMARY KEY (`ward_id`),
  ADD KEY `upazila_id` (`upazila_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employment_status`
--
ALTER TABLE `employment_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `upazilas`
--
ALTER TABLE `upazilas`
  MODIFY `upazila_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wards`
--
ALTER TABLE `wards`
  MODIFY `ward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `employment_status`
--
ALTER TABLE `employment_status`
  ADD CONSTRAINT `employment_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_jobs_upazila` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`upazila_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_jobs_ward` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`ward_id`) ON DELETE SET NULL;

--
-- Constraints for table `job_seeker_profiles`
--
ALTER TABLE `job_seeker_profiles`
  ADD CONSTRAINT `fk_profile_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_profile_upazila` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`upazila_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_profile_ward` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`ward_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_seeker_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `upazilas`
--
ALTER TABLE `upazilas`
  ADD CONSTRAINT `upazilas_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON DELETE CASCADE;

--
-- Constraints for table `wards`
--
ALTER TABLE `wards`
  ADD CONSTRAINT `wards_ibfk_1` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`upazila_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
