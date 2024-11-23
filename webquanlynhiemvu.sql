
-- Bảng quản lý người dùng
TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
)

-- Bảng quản lý chia sẻ và cộng tác
TABLE `collaborations` (
  `collaboration_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `shared_with_user_id` int(11) NOT NULL,
  `permissions` enum('Xem','Chỉnh sửa') DEFAULT 'Xem'
)


-- Bảng quản lý sự kiện
TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `user_id` int(11) NOT NULL
)


-- Bảng thông báo
TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `send_time` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `user_id` int(11) NOT NULL
)


-- Bảng quản lý nhiệm vụ
TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `estimated_time` int(11) DEFAULT NULL,
  `priority` enum('Cao','Trung bình','Thấp') NOT NULL,
  `label` enum('Học tập','Công việc','Cá nhân') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Chưa bắt đầu','Đang thực hiện','Hoàn thành') DEFAULT 'Chưa bắt đầu',
  `user_id` int(11) NOT NULL
)

