<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

// Kiểm tra và thêm thông báo cho nhiệm vụ sắp hết hạn (còn 1 ngày)
$tasks_query = mysqli_query(
    $conn,
    "SELECT * FROM `tasks` 
     WHERE user_id = '$user_id' 
     AND status != 'Hoàn thành'
     AND DATE(end_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)"
) or die('Query failed');

while ($task = mysqli_fetch_assoc($tasks_query)) {
    $task_id = $task['task_id'];
    $title = $task['title'];
    $end_date = $task['end_date'];

    // Kiểm tra nếu chưa tồn tại thông báo cho nhiệm vụ này
    $notification_check_query = mysqli_query(
        $conn,
        "SELECT * FROM `notifications` 
         WHERE user_id = '$user_id' 
         AND content LIKE '%$title%'"
    ) or die('Query failed');

    if (mysqli_num_rows($notification_check_query) == 0) {
        // Thêm thông báo mới vào bảng notifications
        $content = mysqli_real_escape_string($conn, "Nhiệm vụ '$title' sắp hết hạn vào ngày $end_date. Hãy hoàn thành sớm!");

        // Thực hiện chèn thông báo vào bảng notifications
        mysqli_query(
            $conn,
            "INSERT INTO `notifications` (content, send_time, is_read, user_id) 
            VALUES ('$content', NOW(), 0, '$user_id')"
        ) or die('Insert failed');
    }
}

// Truy vấn danh sách thông báo
$notifications_query = mysqli_query(
    $conn,
    "SELECT * FROM `notifications` 
     WHERE user_id = '$user_id' 
     ORDER BY send_time DESC"
) or die('Query failed');

// Đánh dấu thông báo là đã đọc nếu có thao tác
if (isset($_POST['mark_notifications_as_read'])) {
    mysqli_query($conn, "UPDATE `notifications` SET is_read = 1 WHERE user_id = '$user_id'") or die('Update failed');
    header('Location: notification.php'); // Làm mới trang
}

// Đếm số thông báo chưa đọc
$unread_count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS unread_count FROM `notifications` 
     WHERE user_id = '$user_id' AND is_read = 0"
) or die('Query failed');
$unread_count = mysqli_fetch_assoc($unread_count_query)['unread_count'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý nhiệm vụ</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .notification-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item.unread {
            background-color: #ffcd00;
        }
        .notification-item h5 {
            margin: 0;
            font-size: 18px;
        }
        .notification-item p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .notification-item time {
            font-size: 12px;
            color: #888;
        }
         .task-column {
           border-radius: 5px;
           padding: 10px;
           margin: 5px;
           min-height: 300px;
       }
       .column-not-started {
           background-color: #ffe5e5; /* Màu đỏ nhạt */
       }
       .column-in-progress {
           background-color: #fff4cc; /* Màu vàng nhạt */
       }
       .column-completed {
           background-color: #e5ffe5; /* Màu xanh nhạt */
       }
       .task-item {
           border: 1px solid #ccc;
           padding: 10px;
           border-radius: 5px;
           margin-bottom: 10px;
           background-color: #f9f9f9;
           transition: all 0.3s ease;
       }
       .task-item:hover {
           background-color: #f1f1f1;
           transform: translateY(-5px);
       }
       .task-item h5 {
           margin: 0 0 5px 0;
       }
       .task-item p {
           margin: 0;
           font-size: 16px;
       }
      .card {
         border-radius: 12px !important;
      }
      label {
        float: left;
      }
      .card-img-top {
         height: 203px;
         width: 305px;
         border-top-left-radius: 12px !important;
         border-top-right-radius: 12px !important;
      }
      .form-select {
         height: 84%;
         cursor: pointer;
      }
      .card-body {
         font-size: 16px;
      }
      .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Thêm hiệu ứng chuyển động và đổ bóng */
         }

         /* Khi hover, thẻ card sẽ nổi lên */
      .card:hover {
         cursor: pointer;
         transform: translateY(-10px); /* Nổi lên một chút */
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Thêm đổ bóng nhẹ để tạo hiệu ứng nổi */
      }
   </style>
   <style>
    .fc-toolbar-title {
         font-size: 20px;
         font-weight: bold;
    }
    .fc .fc-daygrid-event-harness-abs {
        width: fit-content;
    }
    .task-event {
         font-size: 14px;
         padding: 4px;
         border-radius: 4px;
    }
    .task-event {
        padding: 5px;
        border-radius: 4px;
        color: white;
    }

    .priority-cao {
        background-color: red;
    }

    .priority-trung-bình {
        background-color: orange;
    }

    .priority-thấp {
        background-color: green;
    }

   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-4 mb-5">
        <div class="notification-container">
            <h1 class="text-center">Thông báo</h1>
            <div class="text-end mb-3">
                <form method="post">
                    <button name="mark_notifications_as_read" class="new-btn btn-primary">Đánh dấu tất cả là đã đọc</button>
                </form>
            </div>

            <?php if (mysqli_num_rows($notifications_query) > 0): ?>
                <?php while ($notification = mysqli_fetch_assoc($notifications_query)): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                        <h5><?php echo $notification['content']; ?></h5>
                        <time><?php echo date('d/m/Y H:i:s', strtotime($notification['send_time'])); ?></time>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="fs-2 text-center">Không có thông báo nào.</p>
            <?php endif; ?>
        </div>
</div>

   <!-- FullCalendar JS -->
   <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js"></script>

   <!-- Bootstrap Bundle JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

   <script>
   document.addEventListener('DOMContentLoaded', function () {
      const calendarEl = document.getElementById('calendar');

      const calendar = new FullCalendar.Calendar(calendarEl, {
         initialView: 'dayGridMonth', // Hiển thị dạng tháng
         locale: 'vi', // Ngôn ngữ Tiếng Việt
         headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay'
         },
         events: [
            <?php
            // Truy vấn các nhiệm vụ từ bảng `tasks`
            $tasks_query = mysqli_query($conn, "SELECT * FROM `tasks` WHERE user_id = '$user_id'") or die('Query failed');
            if (mysqli_num_rows($tasks_query) > 0) {
               while ($task = mysqli_fetch_assoc($tasks_query)) {
                  echo "{
                     title: '" . addslashes($task['title']) . "',
                     start: '" . $task['start_date'] . "',
                     end: '" . date('Y-m-d', strtotime($task['end_date'] . ' +1 day')) . "', // Kết thúc bao gồm ngày cuối cùng
                     description: '" . addslashes($task['description']) . "',
                     priority: '" . $task['priority'] . "',
                     label: '" . $task['label'] . "',
                     status: '" . $task['status'] . "',
                     className: 'task-event priority-" . strtolower($task['priority']) . "'
                  },";
               }
            }

            // Truy vấn các sự kiện từ bảng `events`
            $events_query = mysqli_query($conn, "SELECT * FROM `events` WHERE user_id = '$user_id'") or die('Query failed');
            if (mysqli_num_rows($events_query) > 0) {
               while ($event = mysqli_fetch_assoc($events_query)) {
                  echo "{
                     title: '" . addslashes($event['title']) . "',
                     start: '" . $event['start_time'] . "',
                     end: '" . date('Y-m-d', strtotime($event['end_time'] . ' +1 day')) . "', // Kết thúc bao gồm ngày cuối cùng
                     description: '" . addslashes($event['description']) . "',
                     className: 'event-item'
                  },";
               }
            }
            ?>
         ],
         eventClick: function (info) {
            const event = info.event;

            // Hiển thị thông tin chi tiết của nhiệm vụ hoặc sự kiện
            alert(`Tiêu đề: ${event.title}\nMô tả: ${event.extendedProps.description}`);
         },
         eventClassNames: function (arg) {
            // Gắn màu sắc hoặc kiểu dáng cho nhiệm vụ hoặc sự kiện
            const isTask = arg.event.classNames.includes('task-event');
            if (isTask) {
               const priority = arg.event.extendedProps.priority.toLowerCase();
               return [`priority-${priority}`];
            }
            return ['event-item'];
         }
      });

      calendar.render();
   });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
