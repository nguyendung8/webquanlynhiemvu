<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}


// Cập nhật trạng thái
if (isset($_POST['update_status'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE `tasks` SET status = '$new_status' WHERE task_id = '$task_id'") or die('Query failed');
}

// Lấy dữ liệu nhiệm vụ với bộ lọc
$filter_priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$filter_label = isset($_GET['label']) ? $_GET['label'] : '';

$query = "
    SELECT tasks.* 
    FROM `tasks`
    INNER JOIN `collaborations` ON tasks.task_id = collaborations.task_id
    WHERE collaborations.shared_with_user_id = '$user_id'
";

if ($filter_priority) {
    $query .= " AND tasks.priority = '$filter_priority'";
}
if ($filter_label) {
    $query .= " AND tasks.label = '$filter_label'";
}

$tasks = mysqli_query($conn, $query) or die('Query failed');
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý nhiệm vụ cộng tác</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
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
</head>
<body>

<?php include 'header.php'; ?>

<section class="manage-tasks container mt-4">
    <h1 class="text-center mb-4 fs-1">Quản lý Danh Sách Việc Cộng Tác Với Người Khác</h1>

    <!-- Bộ lọc -->
    <form action="" method="get" style="gap: 30px;" class="d-flex mb-3 justify-content-end align-item-center">
        <div>
            <select name="priority" class="form-select w-auto d-inline-block">
                <option value="">Tất cả mức ưu tiên</option>
                <option value="Cao" <?php if ($filter_priority == 'Cao') echo 'selected'; ?>>Cao</option>
                <option value="Trung bình" <?php if ($filter_priority == 'Trung bình') echo 'selected'; ?>>Trung bình</option>
                <option value="Thấp" <?php if ($filter_priority == 'Thấp') echo 'selected'; ?>>Thấp</option>
            </select>
            <select name="label" class="form-select w-auto d-inline-block">
                <option value="">Tất cả nhãn</option>
                <option value="Học tập" <?php if ($filter_label == 'Học tập') echo 'selected'; ?>>Học tập</option>
                <option value="Công việc" <?php if ($filter_label == 'Công việc') echo 'selected'; ?>>Công việc</option>
                <option value="Cá nhân" <?php if ($filter_label == 'Cá nhân') echo 'selected'; ?>>Cá nhân</option>
            </select>
        </div>
        <button type="submit" class="new-btn btn-primary">Lọc</button>
    </form>

    <!-- Hiển thị nhiệm vụ -->
    <div class="row">
        <div class="col-md-4">
            <div class="task-column column-not-started">
                <h3 class="text-center fs-2">Chưa bắt đầu</h3>
                <?php
                mysqli_data_seek($tasks, 0);
                while ($task = mysqli_fetch_assoc($tasks)) {
                    if ($task['status'] == 'Chưa bắt đầu') {
                        echo '<div class="task-item">';
                        echo '<h5>' . $task['title'] . '</h5>';
                        echo '<p>Thời gian ước tính: ' . $task['estimated_time'] . ' giờ' . '</p>';
                        echo '<p>Mô tả: ' . $task['description'] . '</p>';
                        echo '<p>Nhãn: ' . $task['label'] . '</p>';
                        echo '<p>Ưu tiên: ' . $task['priority'] . '</p>';
                        echo '<form action="" method="post">';
                        echo '<input type="hidden" name="task_id" value="' . $task['task_id'] . '">';
                        echo '<select name="status" class="form-select form-select-sm">';
                        echo '<option value="Đang thực hiện">Đang thực hiện</option>';
                        echo '<option value="Hoàn thành">Hoàn thành</option>';
                        echo '</select>';
                        echo '<button type="submit" name="update_status" class="new-btn fs-4 btn-sm btn-primary mt-2">Cập nhật</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="task-column column-in-progress">
                <h3 class="text-center fs-2">Đang thực hiện</h3>
                <?php
                mysqli_data_seek($tasks, 0);
                while ($task = mysqli_fetch_assoc($tasks)) {
                    if ($task['status'] == 'Đang thực hiện') {
                        echo '<div class="task-item">';
                        echo '<h5>' . $task['title'] . '</h5>';
                        echo '<p>Thời gian ước tính: ' . $task['estimated_time'] . 'giờ' . '</p>';
                        echo '<p>Mô tả: ' . $task['description'] . '</p>';
                        echo '<p>Nhãn: ' . $task['label'] . '</p>';
                        echo '<p>Ưu tiên: ' . $task['priority'] . '</p>';
                        echo '<form action="" method="post">';
                        echo '<input type="hidden" name="task_id" value="' . $task['task_id'] . '">';
                        echo '<select name="status" class="form-select form-select-sm">';
                        echo '<option value="Chưa bắt đầu">Chưa bắt đầu</option>';
                        echo '<option value="Hoàn thành">Hoàn thành</option>';
                        echo '</select>';
                        echo '<button type="submit" name="update_status" class="new-btn fs-4 btn-sm btn-primary mt-2">Cập nhật</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="task-column column-completed">
                <h3 class="text-center fs-2">Hoàn thành</h3>
                <?php
                mysqli_data_seek($tasks, 0);
                while ($task = mysqli_fetch_assoc($tasks)) {
                    if ($task['status'] == 'Hoàn thành') {
                        echo '<div class="task-item">';
                        echo '<h5>' . $task['title'] . '</h5>';
                        echo '<p>Thời gian ước tính: ' . $task['estimated_time'] . 'giờ' . '</p>';
                        echo '<p>Mô tả: ' . $task['description'] . '</p>';
                        echo '<p>Nhãn: ' . $task['label'] . '</p>'; 
                        echo '<p>Ưu tiên: ' . $task['priority'] . '</p>';
                        echo '<form action="" method="post">';
                        echo '<input type="hidden" name="task_id" value="' . $task['task_id'] . '">';
                        echo '</form>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
