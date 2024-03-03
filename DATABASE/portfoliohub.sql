-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2024 at 05:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfoliohub`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `user_id`, `title`, `description`, `image_path`, `created_at`) VALUES
(1, 33, '📣 Exciting News for Sports Enthusiasts! 🏀🏆', '🏀 Relive the excitement! Our thrilling sports season has concluded, and we invite all participants to proudly upload their certificates. Celebrate your achievements and share the glory with the world! 🎾🏆 #SportsFever #CertificateUpload', 'CERTIFICATE/ADMIN/ANNOUNCEMENT/code_carnival_hemang_lakhadiya.jpeg', '2024-02-28 13:13:40'),
(8, 33, 'Empower Your Learning Journey!', 'Explore new educational horizons with our diverse courses, designed to inspire and elevate your knowledge. Seize opportunities for growth today!', 'CERTIFICATE/ADMIN/ANNOUNCEMENT/templet.jpg', '2024-02-28 15:09:35'),
(12, 33, 'SPRTS', 'TEST', 'CERTIFICATE/ADMIN/ANNOUNCEMENT/Screenshot 2024-02-12 183815.png', '2024-03-02 11:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `organization` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `certificate_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `validation_status` enum('Pending','Validated','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`certificate_id`, `user_id`, `organization`, `category`, `certificate_name`, `date`, `file_path`, `created_at`, `validation_status`) VALUES
(36, 34, 'ATMIYA UNIVERSITY', 'SPORTS', 'RUNNING ', '2024-03-23', 'CERTIFICATES/MANN BHATASANA/SPORTS/certificate (7).pdf', '2024-03-01 16:51:14', 'Validated'),
(37, 34, 'WHO', 'EDUCATION', 'JAVA', '2024-03-23', 'CERTIFICATES/MANN BHATASANA/EDUCATION/certificate_3 (2).jpg', '2024-03-01 16:56:22', 'Validated'),
(38, 34, 'KALYAN HIGH UNIVERSITY', 'SPORTS', 'PHP', '2024-03-16', 'CERTIFICATES/MANN BHATASANA/SPORTS/certificate (5).pdf', '2024-03-01 16:57:25', 'Validated'),
(39, 34, 'ATMIYA UNIVERSITY', 'CULTURE', 'Singing', '2024-03-16', 'CERTIFICATES/MANN BHATASANA/CULTURE/certificate_3.jpg', '2024-03-01 16:57:49', 'Rejected'),
(40, 35, 'ATMIYA UNIVERSITY', 'SPORTS', 'RUNNING ', '1221-12-12', 'CERTIFICATES/PRAGNESH PARMAR/SPORTS/certificate (3) (1).pdf', '2024-03-01 16:58:36', 'Pending'),
(41, 35, 'GREAT LEARNING', 'EDUCATION', 'PHP', '2024-03-07', 'CERTIFICATES/PRAGNESH PARMAR/EDUCATION/certificate (11).pdf', '2024-03-01 16:58:48', 'Pending'),
(42, 35, 'KALYAN HIGH UNIVERSITY', 'CULTURE', 'DANCEING', '2024-02-27', 'CERTIFICATES/PRAGNESH PARMAR/CULTURE/product-jpeg-500x500.jpg', '2024-03-01 16:59:14', 'Validated'),
(43, 35, 'ATMIYA UNIVERSITY', 'SPORTS', 'LONG JUMP', '2023-02-20', 'CERTIFICATES/PRAGNESH PARMAR/SPORTS/certificate (9).pdf', '2024-03-02 09:25:54', 'Validated'),
(44, 40, 'ATMIYA UNIVERSITY', 'SPORTS', '100 meter', '2023-02-20', 'CERTIFICATES/PARV MER/SPORTS/certificate (14).pdf', '2024-03-02 09:31:51', 'Validated'),
(45, 40, 'MARWADI UNIVERSITY', 'EDUCATION', 'PHP WORKSHOP', '2023-03-14', 'CERTIFICATES/PARV MER/EDUCATION/certificate (15).pdf', '2024-03-02 09:34:50', 'Pending'),
(46, 39, 'ATMIYA UNIVERSITY', 'SPORTS', 'PUT SHUT', '2023-03-20', 'CERTIFICATES/KUSH KANERIYA/SPORTS/certificate (16).pdf', '2024-03-02 09:44:32', 'Validated'),
(47, 39, 'MARWADI UNIVERSITY', 'EDUCATION', 'AI LEARNING', '2023-01-20', 'CERTIFICATES/KUSH KANERIYA/EDUCATION/certificate (17).pdf', '2024-03-02 10:03:12', 'Pending'),
(48, 43, 'ATMIYA UNIVERSITY', 'SPORTS', 'CHESS', '2023-03-20', 'CERTIFICATES/JENIL KOTECHA/SPORTS/certificate (18).pdf', '2024-03-02 10:09:21', 'Validated'),
(49, 43, 'MARWADI UNIVERSITY', 'EDUCATION', 'Advance Python', '2023-03-20', 'CERTIFICATES/JENIL KOTECHA/EDUCATION/certificate (19).pdf', '2024-03-02 10:18:39', 'Validated'),
(50, 43, 'ATMIYA UNIVERSITY', 'OTHER', 'blood donation ', '2023-02-20', 'CERTIFICATES/JENIL KOTECHA/OTHER/certificate (20).pdf', '2024-03-02 10:24:44', 'Pending'),
(51, 43, 'ATMIYA UNIVERSITY', 'OTHER', 'LONG JUMP', '2023-12-12', 'CERTIFICATES/JENIL KOTECHA/OTHER/product-jpeg-500x500 (1).jpg', '2024-03-02 10:25:42', 'Pending'),
(52, 44, 'ATMIYA UNIVERSITY', 'EDUCATION', 'PHP', '2023-03-20', 'CERTIFICATES/PRINCE THAKARAR/EDUCATION/certificate (21).pdf', '2024-03-02 11:20:49', 'Validated'),
(53, 45, 'ATMIYA UNIVERSITY', 'EDUCATION', 'PHP', '2023-03-01', 'CERTIFICATES/RAJ KOTHARI/EDUCATION/certificate (22).pdf', '2024-03-02 11:37:27', 'Validated'),
(54, 45, 'MARWADI UNIVERSITY', 'SPORTS', 'AI LEARNING', '2024-03-13', 'CERTIFICATES/RAJ KOTHARI/SPORTS/product-jpeg-500x500 (1).jpg', '2024-03-02 11:40:07', 'Rejected'),
(55, 38, 'ATMIYA', 'CULTURE', 'Matuki Decorations ', '2008-05-04', 'CERTIFICATES/HET HINGRAJIYA/CULTURE/Matuki Decoration Competition.pdf', '2024-03-02 12:57:20', 'Pending'),
(56, 45, 'COURSERA', 'EDUCATION', 'Certificate', '2024-02-16', 'CERTIFICATES/RAJ KOTHARI/EDUCATION/1.pdf', '2024-03-02 12:57:47', 'Validated'),
(57, 46, 'MARWADI UNIVERSITY', 'SPORTS', 'LONG JUMP', '2023-03-20', 'CERTIFICATES/POOJAN DELVADIYA/SPORTS/certificate (23).pdf', '2024-03-02 14:16:15', 'Validated'),
(58, 47, 'ATMIYA UNIVERSITY', 'EDUCATION', 'PHP', '2023-03-20', 'CERTIFICATES/DHRUV SAVALIYA/EDUCATION/certificate (24).pdf', '2024-03-02 19:48:11', 'Validated');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active_status` int(11) DEFAULT -1,
  `verifiedEmail` int(11) DEFAULT 0,
  `token` varchar(21) DEFAULT NULL,
  `role` int(11) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `company_email`, `username`, `password`, `active_status`, `verifiedEmail`, `token`, `role`) VALUES
(1, 'INFOTECH', '15612422044@atmiyauni.edu.in', 'RAJ KOthari', '$2y$10$f2Nw3lXxP.fSKJPA5BfCyuuwMClyzHvvu7v1x4IPPNHPtv1kjmGz6', 0, 1, '828914826853809699735', 2),
(5, 'HEMANG INFOTECH', '15612422332@atmiyauni.edu.in', 'hemang lakhadiya', '$2y$10$wVAwEy.k/.GaWWzLDXqXw.yy5nk0HY/KMx604xn6dUMSw/g.TrNGS', 1, 1, '966159745311637393479', 2);

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expiration_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`id`, `email`, `otp_code`, `expiration_time`) VALUES
(2, 'yuvrajgosai2918@gmail.com', '997562', 0),
(9, 'dhrvuvsavaliya1395@gmail.com', '558745', 0);

-- --------------------------------------------------------

--
-- Table structure for table `userinformation`
--

CREATE TABLE `userinformation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `enrollment_number` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userinformation`
--

INSERT INTO `userinformation` (`id`, `user_id`, `enrollment_number`, `department`, `program_name`) VALUES
(8, 33, '220801189', 'CS & IT', 'BCA'),
(9, 35, '220801121', 'DIPLOMA', 'Computer'),
(10, 38, '220801133', 'CS & IT', 'BCA'),
(11, 34, '220801181', 'CS & IT', 'BCA'),
(12, 40, '2208012346', 'computer engineering', 'B.TECH'),
(13, 44, '123456789', 'DIPLOMA', 'DCE'),
(14, 45, '220801184', 'CS & IT', 'BCA-B4'),
(15, 47, '220801123', 'CS & IT', 'BSC.IT');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `verifiedEmail` tinyint(1) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active_status` int(11) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `username`, `picture`, `verifiedEmail`, `token`, `password`, `active_status`, `role`) VALUES
(33, 'lakhadiyahemang@gmail.com', 'HEMANG LAKHADIYA', 'https://lh3.googleusercontent.com/a/ACg8ocL0mFZ28Pa6LMhL6a0WsAVSWRYiRsWmSA8bcOzHortv-Mk=s96-c', 1, '111290462656461462861', '$2y$10$PeWhQ5pvE0dlKUqtuQSonuaTke7gY52o7ffQ08JHTFjmkf.WYb80i', 0, 1),
(34, 'mannbhatasanacap@gmail.com', 'MANN BHATASANA', NULL, 1, '507419605576963081284', '$2y$10$v3MXZlD5tql87o/iCDxpcuyjdhlq8gF90R82FvgqY21HQ/1GC8zyy', 0, 0),
(35, 'pragneshparmar176@gmail.com', 'PRAGNESH PARMAR', NULL, 1, '130922901951183522734', '$2y$10$UU9T3bmclgOv6IrNE.lFv.YgSKAdp/uXMC0/cCBISU7AqqSbhM3Bq', 0, 0),
(37, 'uvuv2964@gmail.com', 'YUVRAJ GOSAI', 'https://lh3.googleusercontent.com/a/ACg8ocLi1bpxKK8_DRLesZHT88aycVNyMXjGSaTNCUyOtHK-=s96-c', 1, '106364853471759536678', '$2y$10$H/hmQiG/wjiVSJlb..FiWePdQuzMaBmNAbVvWrqYSCVObrypmF1vm', 0, 0),
(38, 'hetpatel8517@gmail.com', 'HET HINGRAJIYA', NULL, 1, '705230708267095990527', '$2y$10$c1dLzCxlAUdV7sNS8/SQfuRrnhBAPjQwhgwewtlYIbRG9gTzP6zlG', 1, 0),
(39, 'kushpatel16102003@gmail.com', 'KUSH KANERIYA', NULL, 1, '651682382965790778801', '$2y$10$FHotHIXDx2/gT0CDSMZUY.A/lNd4kcMrzjcoWecgckR6NEXcGn0Ii', 0, 0),
(40, 'parvmer32@gmail.com', 'PARV MER', NULL, 1, '404897000203758128407', '$2y$10$RjUAeyGAJNRjNUzD0FH1o.wePz.4aIxEtr/1BOQMrHNMux2YTUClq', 0, 0),
(42, 'yuvrajgosai2918@gmail.com', 'GOSAI YUVRAJ', NULL, 1, '229479975237630686174', '$2y$10$lSnU8N0iluqmo1uFP8MOeO.1q2CiR28OYCfO9fTHlKu0JKCwIiLQi', 0, 0),
(43, 'kotechajenil144@gmail.com', 'JENIL KOTECHA', NULL, 1, '814025192851674647002', '$2y$10$Ct6rY.ALIpp3jWLAjvubhukFimdLyxn3mACN7XW5chNBIR6.RWlOa', 0, 0),
(44, 'prince.thakarar40@gmail.com', 'PRINCE THAKARAR', NULL, 1, '178260293281720717350', '$2y$10$ML9BePj89SzBrzQsSkpuN.7nSFkviTz5ZjaRKv0r6eJZoVxuBpBa.', 0, 0),
(45, 'rajkothari885@gmail.com', 'RAJ KOTHARI', NULL, 1, '951215504621767631755', '$2y$10$BHMUCf2MSDNHdBLbGejiH.G6WmTunFnzRJ7B94dvvCSI70CMu6jbK', 1, 0),
(46, 'poojandelvadiya27@gmail.com', 'POOJAN DELVADIYA', NULL, 1, '202978997370856986657', '$2y$10$yMpEkw58wRwsQ41gdzQFAeWqELJ/Za1uyaSkcYD4Mq7BW2xMv1WI6', 0, 0),
(47, 'dhruvsavaliya1395@gmail.com', 'DHRUV SAVALIYA', NULL, 1, '352843829342295253373', '$2y$10$9cB81TFfFYRD.p3WVkQPxOKOjpqstMz05E9.gG1QZO6pC0pp.RNom', 0, 0),
(48, 'krishmanvarcap@gmail.com', 'KRISH MANVARCAP', 'https://lh3.googleusercontent.com/a/ACg8ocIaovRrrqTiwDtNMAetXNSdgSlKm88krKYIlPUflyMy=s96-c', 1, '117865850921360353858', '$2y$10$mX8ivZniZ0ZTznPKKeHrs.s7DJ8hbxiLwbsL5vNL5/YMeq8FG.TIe', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userinformation`
--
ALTER TABLE `userinformation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `userinformation`
--
ALTER TABLE `userinformation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `userinformation`
--
ALTER TABLE `userinformation`
  ADD CONSTRAINT `userinformation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
