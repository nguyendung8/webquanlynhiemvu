<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

// Thêm sự kiện
if (isset($_POST['add_event'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);

    $insert_event = mysqli_query($conn, "INSERT INTO `events` 
        (title, description, start_time, end_time, user_id) 
        VALUES 
        ('$title', '$description', '$start_time', '$end_time', '$user_id')") 
        or die('Query failed');

    if ($insert_event) {
        $message[] = 'Thêm sự kiện thành công!';
    } else {
        $message[] = 'Thêm sự kiện thất bại!';
    }
}

// Xóa sự kiện
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete_event = mysqli_query($conn, "DELETE FROM `events` WHERE event_id = '$delete_id'") or die('Query failed');
    if ($delete_event) {
        $message[] = 'Xóa sự kiện thành công!';
    } else {
        $message[] = 'Xóa sự kiện thất bại!';
    }
    header('location:event.php');
}

// Cập nhật sự kiện
if (isset($_POST['update_event'])) {
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);

    $update_event = mysqli_query($conn, "UPDATE `events` SET 
        title = '$title', description = '$description', 
        start_time = '$start_time', end_time = '$end_time' 
        WHERE event_id = '$event_id'") 
        or die('Query failed');

    if ($update_event) {
        $message[] = 'Cập nhật sự kiện thành công!';
    } else {
        $message[] = 'Cập nhật sự kiện thất bại!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý sự kiện</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
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

<section class="add-products">
    <h1 class="title">Quản lý sự kiện</h1>

    <!-- Form thêm sự kiện -->
    <form action="" method="post">
        <h3>Thêm sự kiện mới</h3>
        <input type="text" name="title" class="box" placeholder="Tiêu đề sự kiện" required>
        <textarea name="description" class="box" placeholder="Mô tả sự kiện"></textarea>
        <label for="">Thời gian bắt đầu</label>
        <input type="datetime-local" name="start_time" class="box" required>
        <label for="">Thời gian kết thúc</label>
        <input type="datetime-local" name="end_time" class="box" required>
        <input type="submit" name="add_event" value="Thêm sự kiện" class="new-btn btn-primary">
    </form>
</section>

<section class="show-events">
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_events = mysqli_query($conn, "SELECT * FROM `events` WHERE user_id = '$user_id'") or die('Query failed');
                if (mysqli_num_rows($select_events) > 0) {
                    while ($event = mysqli_fetch_assoc($select_events)) {
                ?>
                        <tr>
                            <td><?php echo $event['event_id']; ?></td>
                            <td><?php echo $event['title']; ?></td>
                            <td><?php echo $event['start_time']; ?></td>
                            <td><?php echo $event['end_time']; ?></td>
                            <td>
                                <button type="button" class="fs-4 btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateEventModal<?php echo $event['event_id']; ?>">Sửa</button>
                                <a href="event.php?delete=<?php echo $event['event_id']; ?>" class="fs-3 btn-danger btn-sm" onclick="return confirm('Xóa sự kiện này?');">Xóa</a>
                            </td>
                        </tr>

                        <!-- Modal chỉnh sửa -->
                        <div class="modal fade" id="updateEventModal<?php echo $event['event_id']; ?>" tabindex="-1" aria-labelledby="updateEventModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateEventModalLabel">Cập nhật sự kiện</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                            <input type="text" name="title" class="form-control" value="<?php echo $event['title']; ?>" required>
                                            <textarea name="description" class="form-control"><?php echo $event['description']; ?></textarea>
                                            <label for="">Thời gian bắt đầu</label>
                                            <input type="datetime-local" name="start_time" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_time'])); ?>" required>
                                            <label for="">Thời gian kết thúc</label>
                                            <input type="datetime-local" name="end_time" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_time'])); ?>" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="new-btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <input type="submit" name="update_event" value="Lưu thay đổi" class="new-btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">Không có sự kiện nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
