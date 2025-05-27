-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 04:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webquanlynhiemvu`
--

-- --------------------------------------------------------

--
-- Table structure for table `collaborations`
--

CREATE TABLE `collaborations` (
  `collaboration_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `shared_with_user_id` int(11) NOT NULL,
  `is_accept` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collaborations`
--

INSERT INTO `collaborations` (`collaboration_id`, `task_id`, `shared_with_user_id`, `is_accept`) VALUES
(2, 3, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `start_time`, `end_time`, `user_id`) VALUES
(1, 'Ngày hội khởi nghiệp (Startup Fair)', 'Tổ chức một buổi hội chợ hoặc cuộc thi khởi nghiệp, nơi sinh viên có thể giới thiệu ý tưởng kinh doanh của mình và nhận được sự đánh giá từ các chuyên gia trong ngành. Sự kiện này không chỉ khuyến khích tinh thần sáng tạo mà còn giúp sinh viên phát triển kỹ năng quản lý, tiếp thị và gọi vốn.', '2024-11-23 16:39:00', '2024-11-29 16:39:00', 1),
(2, 'Chương trình âm nhạc với các ban nhạc sinh viên (Battle of the Bands)', 'Tổ chức cuộc thi âm nhạc với sự góp mặt của các ban nhạc sinh viên, nơi họ có thể thể hiện tài năng và thi đấu với nhau. Điều này tạo cơ hội cho sinh viên có đam mê âm nhạc được thể hiện, và cả trường có thể tận hưởng không gian giải trí sôi động.', '2024-11-30 21:41:00', '2024-12-02 16:47:00', 1),
(3, 'Tuần lễ sáng tạo nghệ thuật (Creative Art Week)', 'Sự kiện kéo dài một tuần với các hoạt động nghệ thuật bao gồm hội họa, nhiếp ảnh, điêu khắc, hoặc trình diễn sân khấu, nơi sinh viên có thể trưng bày các tác phẩm của mình. Đây là cơ hội để khám phá tài năng nghệ thuật của sinh viên và tôn vinh sự sáng tạo.', '2024-12-03 16:41:00', '2024-12-06 16:41:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `send_time` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `content`, `send_time`, `is_read`, `user_id`) VALUES
(1, 'Nhiệm vụ \'Học PHP\' sắp hết hạn vào ngày 2024-11-24. Hãy hoàn thành sớm!', '2024-11-23 17:46:40', 1, 1),
(2, 'Nhiệm vụ \'Code đồ án quản lý nhiệm vụ\' sắp hết hạn vào ngày 2024-11-25. Hãy hoàn thành sớm!', '2024-11-24 10:16:56', 1, 1),
(3, 'Nhiệm vụ \'Học Laravel\' sắp hết hạn vào ngày 2024-11-25. Hãy hoàn thành sớm!', '2024-11-24 10:16:56', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `estimated_time` int(11) DEFAULT NULL,
  `priority` enum('Cao','Trung bình','Thấp') NOT NULL,
  `label` enum('Học tập','Công việc','Cá nhân') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Chưa bắt đầu','Đang thực hiện','Hoàn thành') DEFAULT 'Chưa bắt đầu',
  `user_id` int(11) NOT NULL,
  `acceptance_status` varchar(255) DEFAULT 'Chờ xác nhận',
  `is_done` int(11) DEFAULT 0,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `start_date`, `end_date`, `estimated_time`, `priority`, `label`, `notes`, `status`, `user_id`, `acceptance_status`, `is_done`, `file`) VALUES
(3, 'Code đồ án quản lý nhiệm vụ', 'code mới từ đầu ', '2024-11-19', '2024-11-25', 44, 'Cao', 'Công việc', 'ưu tiên nhé', 'Đang thực hiện', 1, 'Đã chấp nhận', 0, NULL),
(4, 'Học hát', 'Tự học hát qua youtube', '2024-11-18', '2024-11-26', 4, 'Thấp', 'Cá nhân', '', 'Hoàn thành', 1, 'Từ chối', 0, NULL),
(5, 'Học Laravel', 'học code laravel', '2024-11-23', '2024-11-25', 48, 'Cao', 'Học tập', '', 'Chưa bắt đầu', 1, 'Chờ xác nhận', 0, NULL),
(7, 'Học PHP', 'Học và làm php cơ bản. nộp file lại cho tôi', '2025-05-27', '2025-05-29', 48, 'Cao', 'Học tập', 'hoàn thành đúng hạn nhé', 'Chưa bắt đầu', 2, 'Chờ xác nhận', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `user_type`, `password`, `created_at`) VALUES
(1, 'User', 'user@gmail.com', 'user', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-18 22:54:23'),
(2, 'User 2', 'user2@gmail.com', 'user', 'e10adc3949ba59abbe56e057f20f883e', '2024-11-23 23:06:01'),
(3, 'Admin', 'admin@gmail.com', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '2025-05-27 20:33:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collaborations`
--
ALTER TABLE `collaborations`
  ADD PRIMARY KEY (`collaboration_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `shared_with_user_id` (`shared_with_user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collaborations`
--
ALTER TABLE `collaborations`
  MODIFY `collaboration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `collaborations`
--
ALTER TABLE `collaborations`
  ADD CONSTRAINT `collaborations_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collaborations_ibfk_2` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
